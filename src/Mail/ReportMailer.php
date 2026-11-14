<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Mail;

use Contao\CoreBundle\Framework\ContaoFramework;
use Neo\WebsitecheckBundle\Check\CheckRepository;
use Neo\WebsitecheckBundle\License\Editions;
use Neo\WebsitecheckBundle\Report\PdfGenerator;
use Neo\WebsitecheckBundle\Report\ReportRenderer;
use Neo\WebsitecheckBundle\Settings;
use Neo\WebsitecheckBundle\Text\TextProvider;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Sends the report to the visitor (PDF attached) and, when configured, a
 * notification to the operator that carries the lead data in subject and
 * body. Two separate e-mails on purpose: a Bcc could not show the operator
 * who requested the report.
 *
 * Without a licence for the website the check was started on
 * (Editions::VISITOR_MAIL) the visitor gets nothing: the same designed e-mail
 * with the PDF goes to the operator only (the copy address, else the sender
 * address), with a box on top that names the visitor; e-mail and PDF carry the
 * credit line.
 */
final class ReportMailer
{
    public function __construct(
        private readonly MailerInterface $mailer,
        private readonly ReportRenderer $renderer,
        private readonly PdfGenerator $pdf,
        private readonly CheckRepository $repository,
        private readonly Settings $settings,
        private readonly TranslatorInterface $translator,
        private readonly ContaoFramework $framework,
        private readonly TextProvider $texts,
        private readonly Editions $editions,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     * @param bool                 $freshPdf render the PDF again (resend from the back end: current design and texts)
     *
     * @return bool whether the visitor got the report (false without licence)
     */
    public function send(array $row, bool $freshPdf = false): bool
    {
        $locale = (string) ($row['locale'] ?: 'en');
        $toVisitor = $this->editions->allows(Editions::VISITOR_MAIL, Editions::hostOf($row));
        $notify = trim($this->settings->mail()['notify_email']);
        $visitor = new Address((string) $row['email'], trim($row['firstname'].' '.$row['lastname']));

        $pdfPath = $this->pdf->generate($row, $freshPdf);
        // The e-mail mentions the attached PDF only when there is one (block "PDF note").
        $row['pdf_path'] = null !== $pdfPath ? basename($pdfPath) : '';
        $vars = $this->renderer->context($row)['vars'];
        $config = $this->renderer->configFor($row);
        $from = $this->from($locale, $config);

        if (!$toVisitor) {
            $html = $this->renderer->renderMailForOperator($row);
            $email = (new Email())
                ->from($from)
                ->to('' !== $notify ? $notify : $from->getAddress())
                ->replyTo($visitor)
                ->subject($this->texts->get('mail.subject', $locale, $vars))
                // The box on top already names the visitor, also in the text version.
                ->text($this->plain($row, $locale, false, $html));
            $email->html(self::inlineImages($email, $html));

            if (null !== $pdfPath) {
                $email->attachFromPath($pdfPath, basename($pdfPath), 'application/pdf');
            }

            $this->mailer->send($email);
            $this->markDone($row, false);

            return false;
        }

        $html = $this->renderer->renderMail($row, 'mail_visitor');
        $email = (new Email())
            ->from($from)
            ->to($visitor)
            ->subject($this->texts->get('mail.subject', $locale, $vars))
            ->text($this->plain($row, $locale, false, $html));
        $email->html(self::inlineImages($email, $html));

        $replyTo = trim((string) ($config['mail_reply_to'] ?? '')) ?: $this->settings->mail()['reply_to'];

        if ('' !== $replyTo) {
            $email->replyTo($replyTo);
        }

        if (null !== $pdfPath) {
            $email->attachFromPath($pdfPath, basename($pdfPath), 'application/pdf');
        }

        $this->mailer->send($email);

        if ('' !== $notify) {
            $copy = (new Email())
                ->from($from)
                ->to($notify)
                ->replyTo($visitor)
                ->subject($this->texts->get('mail.owner_subject', $locale, $vars + ['firstname' => trim($row['firstname'].' '.$row['lastname'])]))
                ->text($this->plain($row, $locale, true, $html));
            $copy->html(self::inlineImages($copy, $this->renderer->renderMail($row, 'mail_owner')));

            if (null !== $pdfPath) {
                $copy->attachFromPath($pdfPath, basename($pdfPath), 'application/pdf');
            }

            $this->mailer->send($copy);
        }

        $this->markDone($row, true);

        return true;
    }

    /**
     * mail_sent: the visitor got the report. Without it the e-mail to the
     * operator is recorded in the data, so a second request for the same check
     * does not send it again.
     *
     * @param array<string, mixed> $row
     */
    private function markDone(array $row, bool $report): void
    {
        if ($report) {
            $this->repository->update((int) $row['id'], ['mail_sent' => time()]);

            return;
        }

        $data = (array) ($this->repository->find((int) $row['id'])['data'] ?? $row['data'] ?? []);
        $data['ownerNotified'] = time();
        $this->repository->update((int) $row['id'], ['data' => $data]);
    }

    private function from(string $locale = 'en', array|null $config = null): Address
    {
        $mail = $this->settings->mail();
        $address = trim((string) ($config['mail_from_email'] ?? '')) ?: $mail['from_email'];
        $mail['from_name'] = trim((string) ($config['mail_from_name'] ?? '')) ?: $mail['from_name'];

        if ('' === $address) {
            $this->framework->initialize();
            $address = (string) $this->framework->getAdapter(\Contao\Config::class)->get('adminEmail');
        }

        if ('' === $address) {
            throw new \RuntimeException('No sender address: set neo_websitecheck.mail.from_email or the Contao admin e-mail.');
        }

        return new Address($address, $mail['from_name'] ?: ($this->settings->brand()['name'] ?: ''));
    }

    /**
     * Plain-text alternative (also what text-only clients and spam filters
     * read). Derived from the HTML that was actually designed, so it carries
     * the customer's words and nothing else; the operator copy is prefixed
     * with the lead line.
     *
     * @param array<string, mixed> $row
     */
    private function plain(array $row, string $locale, bool $owner = false, string|null $html = null): string
    {
        $text = self::htmlToText($html ?? $this->renderer->renderMail($row, 'mail_visitor'));

        if (!$owner) {
            return $text;
        }

        $lead = $this->translator->trans('mail.owner_lead', [
            '%name%' => trim($row['firstname'].' '.$row['lastname']),
            '%email%' => $row['email'],
            '%domain%' => $row['domain'],
            '%newsletter%' => $this->translator->trans('1' === (string) $row['newsletter'] ? 'word.yes' : 'word.no', [], 'neo_websitecheck', $locale),
        ], 'neo_websitecheck', $locale);

        $lead .= "\n".$this->translator->trans('mail.owner_contact', [], 'neo_websitecheck', $locale).': '
            .$this->translator->trans('1' === (string) ($row['contact'] ?? '') ? 'word.yes' : 'word.no', [], 'neo_websitecheck', $locale);

        return $lead."\n\n".$text;
    }

    /** Readable text version of an HTML e-mail: block ends become line breaks, links keep their address. */
    /**
     * Turns embedded images (src="data:image/…;base64,…") into inline attachments
     * referenced by cid:. Outlook, Gmail and most other mail programs show no
     * data: images at all — silently, without a "blocked images" notice.
     * Identical images (a logo used twice) are attached once.
     */
    public static function inlineImages(Email $email, string $html): string
    {
        $seen = [];

        return (string) preg_replace_callback(
            '#(\bsrc\s*=\s*)(["\'])data:(image/(?:png|jpe?g|gif|webp|svg\+xml));base64,([A-Za-z0-9+/=\s]+)\2#i',
            static function (array $m) use ($email, &$seen): string {
                $binary = base64_decode(preg_replace('/\s+/', '', $m[4]) ?? '', true);

                if (false === $binary || '' === $binary) {
                    return $m[0];
                }

                $key = md5($binary);

                if (!isset($seen[$key])) {
                    $ext = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/gif' => 'gif', 'image/webp' => 'webp', 'image/svg+xml' => 'svg'][strtolower($m[3])] ?? 'img';
                    $seen[$key] = 'img'.(\count($seen) + 1).'-'.substr($key, 0, 8).'.'.$ext;
                    $email->embed($binary, $seen[$key], strtolower($m[3]));
                }

                return $m[1].$m[2].'cid:'.$seen[$key].$m[2];
            },
            $html,
        );
    }

    public static function htmlToText(string $html): string
    {
        $html = (string) preg_replace('#<(head|style|script|title)\b.*?</\1>#is', '', $html);
        $html = (string) preg_replace_callback('#<a\b[^>]*href=(["\'])(.*?)\1[^>]*>(.*?)</a>#is', static function (array $m): string {
            // An image in a link counts with its alternative text (the credit line: "powered by NEO Digital").
            $inner = (string) preg_replace('#<img\b[^>]*\balt=(["\'])(.*?)\1[^>]*>#is', ' $2 ', $m[3]);
            $label = trim((string) preg_replace('/\s+/', ' ', strip_tags($inner)));
            $href = html_entity_decode($m[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');

            return '' === $label || $label === $href || str_starts_with($href, 'mailto:') ? $label : $label.' ('.$href.')';
        }, $html);
        $html = (string) preg_replace('#<br\s*/?>#i', "\n", $html);
        $html = (string) preg_replace('#<li\b[^>]*>#i', "\n- ", $html);
        $html = (string) preg_replace('#</(p|div|h[1-6]|tr|ol|ul|table)>#i', "\n\n", $html);
        $html = (string) preg_replace('#</td>#i', ' ', $html);
        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace("\u{a0}", ' ', $text);
        $lines = array_map(static fn (string $l): string => trim((string) preg_replace('/[ \t]+/', ' ', $l)), explode("\n", $text));

        return trim((string) preg_replace("/\n{3,}/", "\n\n", implode("\n", $lines)));
    }
}
