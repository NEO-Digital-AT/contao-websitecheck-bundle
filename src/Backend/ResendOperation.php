<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Backend;

use Contao\Backend;
use Contao\Controller;
use Contao\DataContainer;
use Contao\Message;
use Neo\WebsitecheckBundle\Check\CheckRepository;
use Neo\WebsitecheckBundle\License\Editions;
use Neo\WebsitecheckBundle\Mail\ReportMailer;

/**
 * Back-end row operation "key=resend": sends the report e-mail of a finished,
 * claimed check again (e.g. when the visitor mistyped their address and the
 * operator corrected it in the back end). Only with a licence for the
 * website the check was started on (Editions::VISITOR_MAIL); without one the
 * visitor never gets the e-mail.
 */
final class ResendOperation
{
    public function __construct(
        private readonly CheckRepository $repository,
        private readonly ReportMailer $mailer,
        private readonly Editions $editions,
        private readonly RequestInput $input,
    ) {
    }

    public function __invoke(DataContainer $dc): string
    {
        $id = (int) $this->input->query('id');
        $row = $this->repository->find($id);
        $back = str_replace('&key=resend', '', Backend::addToUrl('', true, ['key', 'id']));

        if (null === $row || CheckRepository::STATUS_DONE !== $row['status'] || '' === (string) $row['email']) {
            Message::addError(sprintf($GLOBALS['TL_LANG']['tl_neo_websitecheck']['resendFailed'] ?? 'Could not send: %s', 'check not finished or no e-mail'));
            Controller::redirect($back);
        }

        if (!$this->editions->allows(Editions::VISITOR_MAIL, Editions::hostOf($row))) {
            Message::addError($GLOBALS['TL_LANG']['tl_neo_websitecheck']['resendLicense'] ?? 'The e-mail to the visitor needs a licence.');
            Controller::redirect($back);
        }

        try {
            $this->mailer->send($row, true);
            Message::addConfirmation(sprintf($GLOBALS['TL_LANG']['tl_neo_websitecheck']['resendDone'] ?? 'Report for %s sent to %s.', $row['domain'], $row['email']));
        } catch (\Throwable $e) {
            Message::addError(sprintf($GLOBALS['TL_LANG']['tl_neo_websitecheck']['resendFailed'] ?? 'Could not send: %s', $e->getMessage()));
        }

        Controller::redirect($back);
    }
}
