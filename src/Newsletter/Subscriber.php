<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Newsletter;

use Neo\WebsitecheckBundle\Settings;

/**
 * Signs a visitor up for the newsletter with the system of the check's
 * configuration: the Brevo newsletter module or Contao's own newsletter module
 * (Settings::newsletterProvider(), automatic: Brevo when installed). Both use
 * double opt-in. Only with a licence (Editions::NEWSLETTER, checked by the caller).
 */
final class Subscriber
{
    public function __construct(
        private readonly Settings $settings,
        private readonly BrevoSubscriber $brevo,
        private readonly ContaoSubscriber $contao,
    ) {
    }

    public function subscribe(string $email, string $firstName, string $lastName, int $configId = 0, string $locale = ''): bool
    {
        if (!$this->settings->newsletterEnabled($configId, $locale)) {
            return false;
        }

        return match ($this->settings->newsletterProvider($configId, $locale)) {
            'brevo' => $this->brevo->subscribe($email, $firstName, $lastName, $configId, $locale),
            'contao' => $this->contao->subscribe($email, $configId, $locale),
            default => false,
        };
    }
}
