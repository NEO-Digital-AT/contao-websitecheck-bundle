<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check;

use Neo\WebsitecheckBundle\Check\PageDiscovery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PageDiscovery::class)]
final class PageDiscoveryTest extends TestCase
{
    public function testOnePagePerRoleNavigationFirst(): void
    {
        $pages = (new PageDiscovery())->select(
            'https://www.example.com/',
            ['https://www.example.com/leistungen', 'https://www.example.com/ueber-uns', 'https://www.example.com/kontakt/', 'https://www.example.com/impressum', 'https://www.example.com/blog'],
            ['https://www.example.com/angebote/sommer/detail', 'https://www.example.com/datenschutz', 'https://www.example.com/news', 'https://www.example.com/'],
            6,
        );

        $this->assertSame([
            ['url' => 'https://www.example.com/', 'role' => 'home'],
            ['url' => 'https://www.example.com/leistungen', 'role' => 'offer'],
            ['url' => 'https://www.example.com/ueber-uns', 'role' => 'about'],
            ['url' => 'https://www.example.com/kontakt', 'role' => 'contact'],
            ['url' => 'https://www.example.com/impressum', 'role' => 'legal'],
            ['url' => 'https://www.example.com/blog', 'role' => 'other'],
        ], $pages);
    }

    public function testNeverMoreThanTheMaximum(): void
    {
        $pages = (new PageDiscovery())->select('https://www.example.com/', ['https://www.example.com/shop', 'https://www.example.com/team', 'https://www.example.com/kontakt'], [], 2);

        $this->assertSame(['home', 'offer'], array_column($pages, 'role'));
    }

    public function testOtherHostsFilesDeepPagesAndTrackingAreLeftOut(): void
    {
        $pages = (new PageDiscovery())->select(
            'https://www.example.com',
            ['https://other.example.org/kontakt', 'https://www.example.com/preise.pdf', 'https://www.example.com/a/b/c', 'https://www.example.com/agb', 'mailto:office@example.com'],
            ['https://www.example.com/zimmer?utm_source=x', 'https://WWW.example.com//galerie'],
            10,
        );

        $this->assertSame([
            ['url' => 'https://www.example.com/', 'role' => 'home'],
            ['url' => 'https://www.example.com/zimmer', 'role' => 'offer'],
            ['url' => 'https://www.example.com/agb', 'role' => 'legal'],
            ['url' => 'https://www.example.com/galerie', 'role' => 'other'],
        ], $pages, 'one legal page; a second one does not fill up');
    }

    public function testRolesInOtherLanguages(): void
    {
        $pages = (new PageDiscovery())->select('https://www.example.com/', ['https://www.example.com/fr/chambres', 'https://www.example.com/it/chi-siamo', 'https://www.example.com/es/privacidad'], [], 5);

        $this->assertSame(['home', 'offer', 'about', 'legal'], array_column($pages, 'role'));
    }
}
