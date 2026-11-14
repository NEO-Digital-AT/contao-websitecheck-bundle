<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check\Analyzer;

use Neo\WebsitecheckBundle\Check\Analyzer\HtmlAnalyzer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(HtmlAnalyzer::class)]
final class HtmlAnalyzerTest extends TestCase
{
    private const PAGE = <<<'HTML'
        <!DOCTYPE html>
        <html lang="de-AT"><head>
        <title> Hotel Example – Zimmer </title>
        <meta name="Description" content="Zimmer und Preise.">
        <meta name="viewport" content="width=device-width">
        <meta name="robots" content="NOINDEX, follow">
        <link rel="canonical" href="https://www.example.com/zimmer">
        <link rel="icon" href="/favicon.ico">
        <script type="application/ld+json">{"@context":"https://schema.org","@graph":[{"@type":"Hotel"},{"@type":["LocalBusiness","Organization"],"address":{"@type":"PostalAddress"}}]}</script>
        <script src="https://consent.cookiebot.com/uc.js"></script>
        </head><body>
        <header><nav><a href="/zimmer">Zimmer</a><a href="kontakt">Anfrage</a></nav></header>
        <h1>Willkommen</h1><h3>Übersprungen</h3><h2>Zimmer</h2><h1>Zweite h1</h1>
        <p>Öffnungszeiten: Montag bis Freitag. Hauptstraße 1, 1010 Musterstadt.</p>
        <img src="a.jpg" alt="Zimmer"><img src="b.jpg"><img src="c.jpg" alt="">
        <span class="material-icons" aria-hidden="true">location_on</span>
        <a href="tel:+431234567">Anrufen</a><a href="tel:+431234567">Anrufen</a><a href="mailto:office@example.com">Mail</a>
        <a href="https://www.instagram.com/example">Instagram</a><a href="https://maps.app.goo.gl/abc">Anfahrt</a>
        <a href="javascript:void(0)">x</a><a href="#top">Nach oben</a>
        <footer><a href="/impressum">Impressum</a><a href="/datenschutz">Datenschutzerklärung</a></footer>
        <script>var hidden = 'Montag';</script>
        </body></html>
        HTML;

    public function testWhatAPageSaysAboutItself(): void
    {
        $result = (new HtmlAnalyzer())->analyze('https://www.example.com/zimmer', self::PAGE);

        $this->assertSame('Hotel Example – Zimmer', $result['title']);
        $this->assertSame(22, $result['titleLength']);
        $this->assertSame(['Zimmer und Preise.', 'de-AT', 'width=device-width', 'https://www.example.com/zimmer'], [$result['description'], $result['lang'], $result['viewport'], $result['canonical']]);
        $this->assertTrue($result['noindex']);
        $this->assertTrue($result['favicon']);
        $this->assertSame(1, $result['headingSkips'], 'h1 → h3');
        $this->assertSame([3, 1], [$result['imgTotal'], $result['imgNoAlt']], 'alt="" is decoration, not missing');
        $this->assertEqualsCanonicalizing(['Hotel', 'LocalBusiness', 'Organization', 'PostalAddress'], $result['schema']);
        $this->assertContains('cookiebot', $result['cmp']);
        $this->assertSame(['https://consent.cookiebot.com/uc.js'], $result['scripts']);
        $this->assertTrue($result['openingHours']);
        $this->assertTrue($result['address']);
        $this->assertStringNotContainsString('location_on', $result['text'], 'icon ligatures are no text');
        $this->assertStringNotContainsString('hidden', $result['text']);
    }

    public function testLinksOfThePage(): void
    {
        $links = (new HtmlAnalyzer())->analyze('https://www.example.com/zimmer', self::PAGE)['links'];

        $this->assertSame(['https://www.example.com/zimmer', 'https://www.example.com/kontakt', 'https://www.example.com/impressum', 'https://www.example.com/datenschutz'], $links['internal']);
        $this->assertSame(['imprint' => 'https://www.example.com/impressum', 'privacy' => 'https://www.example.com/datenschutz'], $links['legal']);
        $this->assertSame(['https://www.example.com/kontakt'], $links['contact']);
        $this->assertSame(['tel:+431234567'], $links['tel']);
        $this->assertSame(1, $links['mailto']);
        $this->assertSame(2, $links['external']);
        $this->assertSame(['instagram.com'], $links['social']);
        $this->assertTrue($links['maps']);
        $this->assertSame(['https://www.example.com/zimmer', 'https://www.example.com/kontakt', 'https://www.example.com/impressum', 'https://www.example.com/datenschutz'], $links['nav']);
    }

    public function testAPageInAnotherEncoding(): void
    {
        $result = (new HtmlAnalyzer())->analyze('https://www.example.com/', mb_convert_encoding('<html><head><title>Café</title></head><body><h1>Über uns</h1></body></html>', 'ISO-8859-1', 'UTF-8'));

        $this->assertSame('Café', $result['title']);
        $this->assertSame([['level' => 1, 'text' => 'Über uns']], $result['headings']);
    }
}
