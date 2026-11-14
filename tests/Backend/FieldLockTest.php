<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Backend;

use Neo\WebsitecheckBundle\Backend\FieldLock;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FieldLock::class)]
final class FieldLockTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['TL_DCA']['tl_test']['fields'] = [
            'log_days' => ['inputType' => 'select', 'eval' => ['mandatory' => true, 'tl_class' => 'w50']],
            'privacy_page' => ['inputType' => 'pageTree', 'eval' => ['mandatory' => true]],
            'position' => ['label' => ['Position', 'Where the banner appears.'], 'inputType' => 'select'],
        ];
        $GLOBALS['TL_LANG']['tl_test']['log_days'] = ['Keep for', 'How long consents are kept.'];
        unset($GLOBALS['TL_CSS']);
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['TL_DCA']['tl_test'], $GLOBALS['TL_LANG']['tl_test'], $GLOBALS['TL_CSS']);
    }

    public function testALockedFieldIsDisabledWithTheReasonAsItsHelp(): void
    {
        FieldLock::lock('tl_test', 'log_days', 'Only when consents are stored.');
        $field = $GLOBALS['TL_DCA']['tl_test']['fields']['log_days'];

        $this->assertSame(['Keep for', 'Only when consents are stored.'], $field['label']);
        $this->assertTrue($field['eval']['disabled']);
        $this->assertFalse($field['eval']['mandatory'], 'must not block saving the rest of the form');
        $this->assertSame('w50 nwc-locked', $field['eval']['tl_class']);
        $this->assertSame('bundles/neowebsitecheck/backend.css|static', $GLOBALS['TL_CSS']['neo_websitecheck_backend']);
    }

    public function testAPickerKeepsWorkingAndOnlyShowsTheReason(): void
    {
        FieldLock::lock('tl_test', 'privacy_page', 'Only with the banner switched on.');
        $field = $GLOBALS['TL_DCA']['tl_test']['fields']['privacy_page'];

        $this->assertArrayNotHasKey('disabled', $field['eval']);
        $this->assertTrue($field['eval']['mandatory']);
        $this->assertSame(['privacy_page', 'Only with the banner switched on.'], $field['label']);
        $this->assertSame('nwc-locked', $field['eval']['tl_class']);
    }

    public function testAWarningLeavesTheFieldEditable(): void
    {
        FieldLock::warn('tl_test', 'position', 'Has no effect with the current layout.');
        $field = $GLOBALS['TL_DCA']['tl_test']['fields']['position'];

        $this->assertSame(['Position', 'Has no effect with the current layout.'], $field['label']);
        $this->assertArrayNotHasKey('disabled', $field['eval']);
        $this->assertSame('nwc-locked', $field['eval']['tl_class']);
    }

    public function testNothingHappensWithoutAReasonOrAField(): void
    {
        $before = $GLOBALS['TL_DCA']['tl_test'];

        FieldLock::lock('tl_test', 'log_days', '');
        FieldLock::lock('tl_test', 'missing', 'Reason');

        $this->assertSame($before, $GLOBALS['TL_DCA']['tl_test']);
        $this->assertArrayNotHasKey('missing', $GLOBALS['TL_DCA']['tl_test']['fields']);
        $this->assertArrayNotHasKey('TL_CSS', $GLOBALS);
    }
}
