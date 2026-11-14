<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\License;

use Neo\WebsitecheckBundle\License\Editions;
use Neo\WebsitecheckBundle\Tests\Support\Licences;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Editions::class)]
final class EditionsTest extends TestCase
{
    public function testWithoutLicenceTheFreeFunctionsOnly(): void
    {
        $editions = Licences::editions();

        $this->assertFalse($editions->allows(Editions::VISITOR_MAIL, 'www.example.com'));
        $this->assertFalse($editions->allows(Editions::NEWSLETTER, 'www.example.com'));
        $this->assertFalse($editions->allows(Editions::UNBRANDED, 'www.example.com'), 'the credit line stays');
        $this->assertTrue($editions->allows('pdf_report', 'www.example.com'), 'a function that is not listed is free');
        $this->assertFalse($editions->isLicensed(''));
        $this->assertSame('', $editions->edition(''));
    }

    public function testEveryEditionUnlocksEverything(): void
    {
        foreach (['basic', 'plus'] as $edition) {
            $editions = Licences::editions($edition);

            foreach ([Editions::VISITOR_MAIL, Editions::NEWSLETTER, Editions::UNBRANDED] as $feature) {
                $this->assertTrue($editions->allows($feature, 'www.example.com'), $edition.': '.$feature);
            }

            $this->assertSame($edition, $editions->edition(''));
        }
    }

    public function testTheHostOfACheck(): void
    {
        $this->assertSame('www.example.com', Editions::hostOf(['site_host' => 'www.example.com']));
        $this->assertSame('', Editions::hostOf([]), 'checks stored before the host was recorded');
    }
}
