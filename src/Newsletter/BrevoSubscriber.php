<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Newsletter;

use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Routing\ContentUrlGenerator;
use Contao\PageModel;
use Neo\WebsitecheckBundle\Settings;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Optional newsletter opt-in via Brevo's double-opt-in endpoint, with the API
 * key of the Brevo newsletter module (back end): the visitor only becomes a subscriber
 * after clicking the confirmation link Brevo sends.
 * Failures are never shown to the visitor (the report still arrives); they go
 * to the application log with Brevo's answer and, as an error, to Contao's
 * system log, where the operator sees them.
 */
final class BrevoSubscriber
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly Settings $settings,
        private readonly ContaoFramework $framework,
        private readonly ContentUrlGenerator $urls,
        private readonly LoggerInterface|null $logger = null,
        private readonly LoggerInterface|null $contaoErrorLog = null,
    ) {
    }

    public function isEnabled(int $configId = 0, string $locale = ''): bool
    {
        return $this->settings->newsletterEnabled($configId, $locale);
    }

    /**
     * Double opt-in with the list, template and confirmation page of the check's
     * configuration (= its language on multilingual sites).
     */
    public function subscribe(string $email, string $firstName, string $lastName, int $configId = 0, string $locale = ''): bool
    {
        if (!$this->isEnabled($configId, $locale) || 'brevo' !== $this->settings->newsletterProvider($configId, $locale)) {
            return false;
        }

        $nl = $this->settings->newsletterFor($configId, $locale);
        $redirectUrl = '';

        if ((int) $nl['brevo_redirect_page'] > 0) {
            $this->framework->initialize();
            $redirectPage = PageModel::findByPk((int) $nl['brevo_redirect_page']);
            $redirectUrl = null === $redirectPage ? '' : $this->urls->generate($redirectPage, [], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        try {
            $response = $this->httpClient->request('POST', 'https://api.brevo.com/v3/contacts/doubleOptinConfirmation', [
                'headers' => ['api-key' => $nl['brevo_api_key'], 'Content-Type' => 'application/json', 'Accept' => 'application/json'],
                'json' => [
                    'email' => $email,
                    'attributes' => ['VORNAME' => $firstName, 'NACHNAME' => $lastName, 'FIRSTNAME' => $firstName, 'LASTNAME' => $lastName],
                    'includeListIds' => [(int) $nl['brevo_list_id']],
                    'templateId' => (int) $nl['brevo_template_id'],
                    'redirectionUrl' => $redirectUrl ?: (string) $nl['brevo_redirect_url'],
                    'updateEnabled' => true,
                ],
                'timeout' => 15,
            ]);

            $status = $response->getStatusCode();

            if ($status >= 300) {
                $body = $response->getContent(false);
                $this->logger?->warning('Brevo double opt-in failed: HTTP '.$status.' '.$body);
                $this->failed('HTTP '.$status.self::brevoMessage($body));

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            $this->logger?->warning('Brevo double opt-in failed: '.$e->getMessage());
            $this->failed($e->getMessage());

            return false;
        }
    }

    /** System › System log: the sign-up did not reach Brevo (without the visitor's address). */
    private function failed(string $reason): void
    {
        $this->contaoErrorLog?->error('Website check: the newsletter sign-up at Brevo failed, the visitor was not subscribed ('.mb_substr($reason, 0, 300).').');
    }

    /** Brevo's own error text ({"code": …, "message": …}), if the answer has one. */
    private static function brevoMessage(string $body): string
    {
        try {
            $data = json_decode($body, true, 4, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return '';
        }

        $message = \is_array($data) ? trim((string) ($data['message'] ?? $data['code'] ?? '')) : '';

        return '' === $message ? '' : ': '.$message;
    }
}
