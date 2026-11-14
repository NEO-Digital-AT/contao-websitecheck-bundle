<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Backend;

use Contao\DataContainer;
use Neo\WebsitecheckBundle\Backend\LicensedSettings;
use Neo\WebsitecheckBundle\Tests\Support\Licences;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** Settings that only work with a licence are not offered without one; their values stay. */
#[CoversClass(LicensedSettings::class)]
final class LicensedSettingsTest extends TestCase
{
    private const TABLE = 'tl_neo_websitecheck_config';

    protected function setUp(): void
    {
        $GLOBALS['TL_DCA'][self::TABLE] = [
            'palettes' => ['__selector__' => ['nl_provider'], 'default' => '{mail_legend},mail_note,mail_subject,mail_reply_to;{nl_legend},nl_provider,nl_list_id;{fe_legend},fe_result,fe_title'],
            'fields' => array_fill_keys(['mail_note', 'mail_subject', 'mail_reply_to', 'nl_provider', 'nl_list_id', 'fe_result', 'fe_title'], ['eval' => []]),
        ];
        $GLOBALS['TL_LANG'][self::TABLE]['mail_note'] = 'Without a licence the e-mail goes to <you>.';
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA'][self::TABLE], $GLOBALS['TL_LANG'][self::TABLE]);
    }

    public function testWithoutLicenceTheVisitorMailAndNewsletterSettingsAreHidden(): void
    {
        $settings = new LicensedSettings(Licences::editions());
        $settings->hideUnlicensed($this->dc());

        $this->assertSame('{mail_legend},mail_note,mail_subject;{fe_legend},fe_title', $GLOBALS['TL_DCA'][self::TABLE]['palettes']['default']);
        $this->assertTrue($GLOBALS['TL_DCA'][self::TABLE]['fields']['nl_provider']['eval']['doNotShow']);
        $this->assertArrayNotHasKey('doNotShow', $GLOBALS['TL_DCA'][self::TABLE]['fields']['mail_subject']['eval']);
        $this->assertArrayNotHasKey('owner_body', $GLOBALS['TL_DCA'][self::TABLE]['fields'], 'no field is added');
        $this->assertSame('<div class="widget clr"><p class="tl_help tl_tip">Without a licence the e-mail goes to &lt;you&gt;.</p></div>', $settings->mailNote($this->dc()));
    }

    public function testWithALicenceEverythingStays(): void
    {
        $before = $GLOBALS['TL_DCA'][self::TABLE];
        $settings = new LicensedSettings(Licences::editions('basic'));
        $settings->hideUnlicensed($this->dc());

        $this->assertSame($before, $GLOBALS['TL_DCA'][self::TABLE]);
        $this->assertSame('', $settings->mailNote($this->dc()));
    }

    private function dc(): DataContainer
    {
        $dc = $this->createMock(DataContainer::class);
        $dc->method('__get')->willReturnCallback(static fn (string $key): mixed => 'table' === $key ? self::TABLE : null);

        return $dc;
    }
}
