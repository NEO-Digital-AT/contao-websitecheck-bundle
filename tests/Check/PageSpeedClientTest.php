<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check;

use Neo\WebsitecheckBundle\Check\PageSpeedClient;
use Neo\WebsitecheckBundle\Tests\Support\TestSettings;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/** Google PageSpeed Insights, played by MockHttpClient. */
#[CoversClass(PageSpeedClient::class)]
final class PageSpeedClientTest extends TestCase
{
    public function testEveryCategoryIsAskedForAndTheResultReduced(): void
    {
        $requested = [];
        $client = new MockHttpClient(static function (string $method, string $url) use (&$requested): MockResponse {
            $requested[] = $url;

            return new MockResponse(json_encode(['lighthouseResult' => [
                'lighthouseVersion' => '12.0.0',
                'finalUrl' => 'https://www.example.com/',
                'categories' => ['performance' => ['score' => 0.934], 'accessibility' => ['score' => 1], 'best-practices' => ['score' => null], 'seo' => ['score' => 0.5]],
                'audits' => ['largest-contentful-paint' => ['numericValue' => 2100.5], 'cumulative-layout-shift' => ['numericValue' => 0.02]],
            ]]));
        });

        $psi = new PageSpeedClient($client, TestSettings::create(['pagespeed' => ['api_key' => 'psi-key']]));
        $result = $psi->runMany(['home_mobile' => ['url' => 'https://www.example.com/', 'strategy' => 'mobile']], 'de-AT');

        $this->assertTrue($psi->isConfigured());
        parse_str((string) parse_url($requested[0], PHP_URL_QUERY), $query);
        $this->assertSame(['url' => 'https://www.example.com/', 'strategy' => 'mobile', 'locale' => 'de', 'key' => 'psi-key'], array_diff_key($query, ['category' => true]));
        $this->assertStringEndsWith('&category=performance&category=accessibility&category=best-practices&category=seo', $requested[0], 'repeated, not category[0]=…');

        $this->assertArrayNotHasKey('error', $result['home_mobile']);
        $this->assertSame(['performance' => 93, 'accessibility' => 100, 'best-practices' => null, 'seo' => 50], $result['home_mobile']['scores']);
        $this->assertSame(2100.5, $result['home_mobile']['metrics']['lcp']);
        $this->assertNull($result['home_mobile']['metrics']['tbt']);
    }

    public function testErrorsAreKeptPerJob(): void
    {
        $client = new MockHttpClient([
            new MockResponse(json_encode(['error' => ['message' => 'Quota exceeded']]), ['http_code' => 429]),
            new MockResponse('{"kind":"no lighthouse"}'),
        ]);

        $result = (new PageSpeedClient($client, TestSettings::create()))->runMany([
            'a' => ['url' => 'https://www.example.com/', 'strategy' => 'mobile'],
            'b' => ['url' => 'https://www.example.com/kontakt', 'strategy' => 'desktop'],
        ], 'en');

        $this->assertSame(['error' => 'Quota exceeded', 'url' => 'https://www.example.com/', 'strategy' => 'mobile'], $result['a']);
        $this->assertSame(['error' => 'HTTP 200', 'url' => 'https://www.example.com/kontakt', 'strategy' => 'desktop'], $result['b']);
    }

    public function testWithoutKeyNoKeyIsSent(): void
    {
        $requested = '';
        $client = new MockHttpClient(static function (string $method, string $url) use (&$requested): MockResponse {
            $requested = $url;

            return new MockResponse('{}', ['http_code' => 500]);
        });

        $psi = new PageSpeedClient($client, TestSettings::create());
        $psi->runMany(['a' => ['url' => 'https://www.example.com/', 'strategy' => 'mobile']], 'en');

        $this->assertFalse($psi->isConfigured());
        $this->assertStringNotContainsString('key=', $requested);
    }
}
