<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check\Analyzer;

use Neo\WebsitecheckBundle\Check\Analyzer\AiVisibilityAnalyzer;
use Neo\WebsitecheckBundle\Check\Analyzer\ServerAnalyzer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ServerAnalyzer::class)]
#[CoversClass(AiVisibilityAnalyzer::class)]
final class ServerAnalyzerTest extends TestCase
{
    public function testTheVersionAnnouncedInAHeader(): void
    {
        $result = (new ServerAnalyzer())->analyze([
            'https://www.example.com/' => ['status' => 200, 'url' => 'https://www.example.com/', 'body' => '', 'headers' => ['server' => ['Apache'], 'x-powered-by' => ['PHP/8.3.12'], 'set-cookie' => ['PHPSESSID=abc; path=/']]],
        ], [], '<meta name="generator" content="Contao Open Source CMS">');

        $this->assertSame(['php' => true, 'hints' => ['header', 'cookie', 'cms'], 'raw' => '8.3.12', 'source' => 'header', 'phpinfo' => '', 'uploadProgress' => null], $result);
    }

    public function testAForgottenPhpinfoPage(): void
    {
        $probe = 'https://www.example.com/info.php';
        $body = '<title>PHP 8.2.20 - phpinfo()</title><h1 class="p">PHP Version 8.2.20</h1><tr><td class="e">session.upload_progress.enabled</td><td class="v">On</td></tr>';

        $result = (new ServerAnalyzer())->analyze([
            'https://www.example.com/phpinfo.php' => ['status' => 404, 'url' => '', 'body' => 'phpinfo()', 'headers' => []],
            $probe => ['status' => 200, 'url' => $probe, 'body' => $body, 'headers' => []],
        ], ['https://www.example.com/phpinfo.php', $probe], '');

        $this->assertSame(['8.2.20', 'phpinfo', '/info.php', true], [$result['raw'], $result['source'], $result['phpinfo'], $result['uploadProgress']]);
    }

    public function testAHiddenVersionIsNoFinding(): void
    {
        $hidden = (new ServerAnalyzer())->analyze([], [], '<link href="/wp-content/themes/x.css"><a href="/index.php?id=1">x</a>');
        $this->assertSame(['php' => true, 'hints' => ['cms', 'links'], 'raw' => '', 'source' => '', 'phpinfo' => '', 'uploadProgress' => null], $hidden);

        $this->assertFalse((new ServerAnalyzer())->analyze([], [], '<p>Static page</p>')['php']);
    }

    public function testWhichAiCrawlersMayReadTheSite(): void
    {
        $robots = "User-agent: GPTBot\nUser-agent: CCBot # common crawl\nDisallow: /\nSitemap: https://www.example.com/sitemap.xml\n";

        $result = (new AiVisibilityAnalyzer())->analyze(
            ['status' => 200, 'body' => $robots],
            ['status' => 200, 'body' => "# Example\nWe rent bikes.", 'headers' => []],
            ['status' => 200, 'body' => '<!DOCTYPE html><html>Not found</html>', 'headers' => []],
            ['schema' => ['Hotel', 'FAQPage', 'ImageObject'], 'text' => 'We rent bikes in the old town.'],
        );

        $this->assertSame(AiVisibilityAnalyzer::BOTS, array_keys($result['bots']));
        $this->assertSame(['GPTBot', 'CCBot'], $result['botsBlocked'], 'one group for both; the others have no group and may');
        $this->assertTrue($result['llmsTxt']);
        $this->assertSame("# Example\nWe rent bikes.", $result['llmsTxtExcerpt']);
        $this->assertFalse($result['llmsFullTxt'], 'a soft-404 page is no llms-full.txt');
        $this->assertTrue($result['robotsFound']);
        $this->assertTrue($result['sitemapInRobots']);
        $this->assertSame(['Hotel'], $result['schemaIdentity']);
        $this->assertSame(['FAQPage'], $result['schemaExtra']);
    }

    /** RFC 9309: a user-agent line after rules starts a new group; the groups do not share their rules. */
    public function testEachGroupOfRobotsTxtHasItsOwnRules(): void
    {
        $robots = "User-agent: GPTBot\nDisallow: /\n\nUser-agent: PerplexityBot\nDisallow: /\nAllow: /\n\nUser-agent: *\nAllow: /\nDisallow: /admin\n";

        $result = (new AiVisibilityAnalyzer())->analyze(['status' => 200, 'body' => $robots], ['status' => 404, 'body' => '', 'headers' => []], ['status' => 404, 'body' => '', 'headers' => []], []);

        $this->assertFalse($result['bots']['GPTBot'], 'the "Allow: /" of the other groups is not its rule');
        $this->assertTrue($result['bots']['PerplexityBot'], 'an explicit allow wins');
        $this->assertSame(['GPTBot'], $result['botsBlocked'], 'the others fall back to "*"');

        $all = (new AiVisibilityAnalyzer())->analyze(['status' => 200, 'body' => "User-agent: *\nDisallow: /private\n\nUser-agent: GPTBot\nDisallow: /\n"], ['status' => 404, 'body' => '', 'headers' => []], ['status' => 404, 'body' => '', 'headers' => []], []);
        $this->assertSame(['GPTBot'], $all['botsBlocked'], 'the rule of GPTBot does not block everybody');
    }

    public function testWithoutRobotsTxtEveryCrawlerMay(): void
    {
        $result = (new AiVisibilityAnalyzer())->analyze(['status' => 404, 'body' => "User-agent: *\nDisallow: /"], ['status' => 404, 'body' => '', 'headers' => []], ['status' => 404, 'body' => '', 'headers' => []], []);

        $this->assertSame([], $result['botsBlocked']);
        $this->assertFalse($result['robotsFound']);
        $this->assertFalse($result['llmsTxt']);
    }
}
