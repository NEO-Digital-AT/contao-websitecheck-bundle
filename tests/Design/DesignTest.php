<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Design;

use Neo\WebsitecheckBundle\Design\BlockRegistry;
use Neo\WebsitecheckBundle\Design\Design;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Twig\Markup;

/** Designs come from the back end and go into PDF, e-mail and page: only a safe subset of HTML. */
#[CoversClass(Design::class)]
final class DesignTest extends TestCase
{
    /** @return iterable<string, array{string, string}> */
    public static function html(): iterable
    {
        yield 'allowed tags' => ['<p>Text <strong>bold</strong> <em>it</em><br></p>', '<p>Text <strong>bold</strong> <em>it</em><br></p>'];
        yield 'script removed' => ['<p>Hi</p><script>alert(1)</script>', '<p>Hi</p>alert(1)'];
        yield 'event handler' => ['<p onclick="alert(1)" class="x" style="color:red">Hi</p>', '<p>Hi</p>'];
        yield 'image removed' => ['<img src=x onerror=alert(1)>Text', 'Text'];
        yield 'https link' => ['<a href="https://example.com/a?b=1&amp;c=2" target="_blank" onclick="x()">Link</a>', '<a href="https://example.com/a?b=1&amp;c=2">Link</a>'];
        yield 'mail link' => ["<a href='mailto:office@example.com'>Mail</a>", '<a href="mailto:office@example.com">Mail</a>'];
        yield 'placeholder link' => ['<a href="{{report_url}}">Report</a>', '<a href="{{report_url}}">Report</a>'];
        yield 'script link' => ['<a href="javascript:alert(1)">x</a>', '<a>x</a>'];
        yield 'encoded script link' => ['<a href="&#106;avascript:alert(1)">x</a>', '<a>x</a>'];
        yield 'data link' => ['<a href="data:text/html;base64,PHNjcmlwdD4=">x</a>', '<a>x</a>'];
        yield 'iframe' => ['<iframe src="https://example.com"></iframe><h2>Title</h2>', '<h2>Title</h2>'];
    }

    #[DataProvider('html')]
    public function testRichTextIsReducedToASafeSubset(string $input, string $expected): void
    {
        $this->assertSame($expected, Design::sanitize($input));
    }

    public function testPlaceholdersAreEscapedInHtml(): void
    {
        $vars = ['domain' => '<b>example.com</b>', 'grade' => 2];

        $this->assertSame('Check of &lt;b&gt;example.com&lt;/b&gt;: 2 {{area_grade}}', Design::fill('Check of {{ domain }}: {{grade}} {{area_grade}}', $vars, true));
        $this->assertSame('Check of <b>example.com</b>', Design::fill('Check of {{domain}}', $vars, false), 'plain text: the template escapes it');
    }

    public function testTranslationKeysAreResolved(): void
    {
        $resolve = static fn (string $key): string => 'T('.$key.')';

        $this->assertSame('<p>T(report.next_text)</p> mail@example.com', Design::resolve('<p>@report.next_text</p> mail@example.com', $resolve));
        $this->assertSame(5, Design::resolve(5, $resolve));
        $this->assertSame('@report.x', Design::resolve('@report.x', null));
    }

    public function testAStoredDesignIsCheckedAgainstTheRegistry(): void
    {
        $kind = 'mail';
        $theme = BlockRegistry::theme($kind);
        $blocks = BlockRegistry::blocks($kind);
        $type = (string) array_key_first($blocks);

        $design = Design::normalise(json_encode([
            'theme' => ['unknown' => 'x'] + array_map(static fn (array $p): string => 'color' === $p['type'] ? 'ZZZ' : 'value', $theme),
            'blocks' => [['type' => 'no-such-block'], ['type' => $type, 'id' => 'a"><script>', 'props' => ['unknown' => 1]], 'not a block'],
        ]), $kind);

        $this->assertSame(Design::VERSION, $design['v']);
        $this->assertSame(array_keys($theme), array_keys($design['theme']));
        $this->assertCount(1, $design['blocks'], 'unknown blocks dropped');
        $this->assertSame('ascript', $design['blocks'][0]['id']);
        $this->assertSame(array_keys($blocks[$type]['props']), array_keys($design['blocks'][0]['props']), 'unknown properties dropped, missing ones defaulted');

        foreach ($theme as $name => $prop) {
            if ('color' === $prop['type']) {
                $this->assertSame((string) ($prop['default'] ?? ''), $design['theme'][$name], 'an invalid colour falls back to the default');
            }
        }

        $this->assertSame([], Design::normalise('not json', $kind)['blocks']);
    }

    public function testPreparedRichTextIsMarkup(): void
    {
        $kind = 'mail';
        $richtext = null;

        foreach (BlockRegistry::blocks($kind) as $type => $definition) {
            foreach ($definition['props'] as $name => $prop) {
                if ('richtext' === $prop['type']) {
                    $richtext = [$type, $name];
                    break 2;
                }
            }
        }

        $this->assertNotNull($richtext, 'the e-mail has a block with rich text');
        [$type, $name] = $richtext;

        $design = Design::prepare(['blocks' => [['type' => $type, 'props' => [$name => '<p onclick="x">Hello {{first_name}}</p>']]]], $kind, ['first_name' => '<i>Max</i>']);
        $value = $design['blocks'][0]['props'][$name];

        $this->assertInstanceOf(Markup::class, $value);
        $this->assertSame('<p>Hello &lt;i&gt;Max&lt;/i&gt;</p>', (string) $value);
    }
}
