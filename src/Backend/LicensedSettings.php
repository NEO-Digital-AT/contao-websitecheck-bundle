<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Backend;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\DataContainer;
use Contao\StringUtil;
use Neo\WebsitecheckBundle\License\Editions;

/**
 * Settings without effect while there is no licence are not shown: they are
 * removed from the palette (Contao then neither shows nor saves them) and
 * marked doNotShow, which also keeps them out of "Edit multiple", "Override
 * multiple" and the details view. Their stored values stay untouched and the
 * settings appear again with a licence.
 *
 * Without a licence the PDF and e-mail designers work as with one; the
 * designed e-mail with the PDF goes to the site operator only. Hidden are the
 * newsletter settings and what only concerns the e-mail to the visitor. The
 * e-mail sections say once where the e-mail goes (mailNote()).
 *
 * The back end may run under another address than the website, so the
 * domain check is skipped, as in the licence row.
 */
final class LicensedSettings
{
    /**
     * Per table the fields without effect without a licence: reply-to of the
     * visitor e-mail, the operator copy (without a licence the operator gets the
     * visitor e-mail itself), the newsletter, and on the page the choice
     * short/full result and the texts that promise an e-mail (the form uses
     * the texts "… without licence").
     *
     * @var array<string, list<string>>
     */
    private const LICENSED_FIELDS = [
        'tl_neo_websitecheck_settings' => ['reply_to'],
        'tl_neo_websitecheck_config' => [
            'mail_reply_to', 'mail_owner_subject', 'owner_body',
            'nl_provider', 'nl_list_id', 'nl_template_id', 'nl_redirect_page',
            'fe_result', 'fe_gate_text', 'fe_gate_legal', 'fe_disclaimer',
        ],
    ];

    public function __construct(private readonly Editions $editions)
    {
    }

    /** onload_callback of tl_neo_websitecheck_settings and tl_neo_websitecheck_config. */
    public function hideUnlicensed(DataContainer|null $dc = null): void
    {
        $table = (string) $dc?->table;
        $fields = self::LICENSED_FIELDS[$table] ?? [];

        if ([] === $fields || $this->editions->isLicensed('')) {
            return;
        }

        $manipulator = PaletteManipulator::create()->removeField($fields);

        foreach (array_keys($GLOBALS['TL_DCA'][$table]['palettes'] ?? []) as $palette) {
            if ('__selector__' !== $palette) {
                $manipulator->applyToPalette($palette, $table);
            }
        }

        foreach ($fields as $field) {
            if (isset($GLOBALS['TL_DCA'][$table]['fields'][$field])) {
                $GLOBALS['TL_DCA'][$table]['fields'][$field]['eval']['doNotShow'] = true;
            }
        }
    }

    /**
     * input_field_callback of the field mail_note at the top of the e-mail
     * sections: without a licence the e-mail goes to the site operator only.
     */
    public function mailNote(DataContainer $dc): string
    {
        if ($this->editions->allows(Editions::VISITOR_MAIL, '')) {
            return '';
        }

        $text = (string) ($GLOBALS['TL_LANG'][$dc->table]['mail_note'] ?? '');

        return '' === $text ? '' : sprintf('<div class="widget clr"><p class="tl_help tl_tip">%s</p></div>', StringUtil::specialchars($text));
    }
}
