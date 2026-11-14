<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Backend;

/**
 * A setting that has no effect because of another setting, a missing key or
 * the edition is never shown as if it worked: the field is disabled (Contao
 * then does not take it from the form, the stored value stays) and the reason
 * takes the place of its help text, marked as a warning (public/backend.css).
 * Called from onload callbacks, after the DCA and its language file are loaded.
 */
final class FieldLock
{
    public const CSS_CLASS = 'nwc-locked';

    /** Disables the field (a picker: see below) and shows the reason at it. */
    public static function lock(string $table, string $field, string $reason): void
    {
        self::mark($table, $field, $reason, true);
    }

    /**
     * Shows the reason at the field and leaves it editable: the current choice
     * has no effect, another one has.
     */
    public static function warn(string $table, string $field, string $reason): void
    {
        self::mark($table, $field, $reason, false);
    }

    private static function mark(string $table, string $field, string $reason, bool $disable): void
    {
        if (!isset($GLOBALS['TL_DCA'][$table]['fields'][$field]) || '' === $reason) {
            return;
        }

        $definition = &$GLOBALS['TL_DCA'][$table]['fields'][$field];
        $label = $definition['label'] ?? $GLOBALS['TL_LANG'][$table][$field] ?? null;

        $definition['label'] = [\is_array($label) ? (string) ($label[0] ?? $field) : (string) ($label ?? $field), $reason];
        $definition['eval']['tl_class'] = trim(($definition['eval']['tl_class'] ?? '').' '.self::CSS_CLASS);

        // A picker (page, file) keeps its button even when disabled, and Contao would then
        // drop a new choice silently: such a field keeps working and only shows the reason.
        if ($disable && !\in_array($definition['inputType'] ?? '', ['pageTree', 'fileTree', 'picker'], true)) {
            $definition['eval']['disabled'] = true;
            // A mandatory field without a choice must not block saving the rest of the form.
            $definition['eval']['mandatory'] = false;
        }

        $GLOBALS['TL_CSS']['neo_websitecheck_backend'] = 'bundles/neowebsitecheck/backend.css|static';
    }
}
