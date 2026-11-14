<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\License;

use Dompdf\Canvas;
use Dompdf\Dompdf;
use Neo\WebsitecheckBundle\License\Credit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/** The credit line of the free edition in the PDF report and in the e-mails. */
#[CoversClass(Credit::class)]
final class CreditTest extends TestCase
{
    public function testTheMailGetsItAsItsLastLine(): void
    {
        $html = Credit::mail('<html><body><p>Report</p></body></html>');

        $this->assertMatchesRegularExpression('#^<html><body><p>Report</p><table role="presentation".*</table></body></html>$#s', $html);
        $this->assertStringContainsString('<a href="https://neo-digital.at" target="_blank" rel="nofollow noopener"', $html);
        $this->assertStringContainsString('powered by <img src="data:image/png;base64,', $html);
        $this->assertStringContainsString('alt="NEO Digital"', $html, 'without images: "powered by NEO Digital"');
        $this->assertSame(base64_encode((string) file_get_contents(Credit::MAIL_PNG)), preg_match('#base64,([^"]+)"#', $html, $m) ? $m[1] : '');
        $this->assertStringEndsWith('</table>', Credit::mail('<p>No body</p>'), 'without body: at the end');
    }

    public function testTheWordmarkKeepsItsProportions(): void
    {
        $svg = Credit::svg(9.0);

        $this->assertStringContainsString('viewBox="0 0 449.49 143.2"', $svg);
        $this->assertStringContainsString('width:28.25px;height:9px;', $svg);
        $this->assertStringContainsString('role="img" aria-label="NEO Digital"', $svg);
        $this->assertSame(3, substr_count($svg, '<polygon '));
        $this->assertSame(3, substr_count($svg, '<rect '));
        $this->assertStringContainsString('fill-rule="evenodd"', $svg, 'the hole of the O is cut out');
    }

    public function testThePdfOfGotenbergEndsWithTheLine(): void
    {
        $html = Credit::pdf('<body><main>Report</main></body>');

        $this->assertMatchesRegularExpression('#<main>Report</main><div style="margin:8mm 0 0;[^"]*page-break-inside:avoid;"><a href="https://neo-digital.at"[^>]*>powered by <svg .*</svg></a></div></body>$#s', $html);
        $this->assertStringContainsString('height:5.37pt;', $html, 'as high as the capitals of 7.5 pt');
    }

    /** Dompdf: on every page, rotated, in the right margin near the bottom, on a white field that is the link. */
    public function testThePdfIsStampedInTheRightMargin(): void
    {
        $dompdf = new Dompdf();
        $script = null;
        $canvas = $this->createMock(Canvas::class);
        $canvas->method('page_script')->willReturnCallback(static function (callable $callback) use (&$script): void {
            $script = $callback;
        });
        $dompdf->setCanvas($canvas);

        Credit::stampPdf($dompdf);
        $this->assertIsCallable($script);

        // A4 in pt.
        $calls = ['rect' => [], 'text' => [], 'polygon' => [], 'link' => []];
        $page = $this->createMock(Canvas::class);
        $page->method('get_width')->willReturn(595.28);
        $page->method('get_height')->willReturn(841.89);
        $page->method('filled_rectangle')->willReturnCallback(static function (...$args) use (&$calls): void {
            $calls['rect'][] = $args;
        });
        $page->method('text')->willReturnCallback(static function (...$args) use (&$calls): void {
            $calls['text'][] = $args;
        });
        $page->method('polygon')->willReturnCallback(static function (...$args) use (&$calls): void {
            $calls['polygon'][] = $args;
        });
        $page->method('add_link')->willReturnCallback(static function (...$args) use (&$calls): void {
            $calls['link'][] = $args;
        });

        $script(1, 3, $page);

        [$url, $left, $top, $width, $height] = $calls['link'][0];
        $this->assertSame('https://neo-digital.at', $url);

        // The white field comes first and is the link.
        [$x, $y, $w, $h, $colour] = $calls['rect'][0];
        $this->assertSame([1, 1, 1], $colour);
        $this->assertEqualsWithDelta([$left, $top, $width, $height], [$x, $y, $w, $h], 0.001);

        $mm = 72 / 25.4;
        $this->assertLessThan(595.28, $left + $width, 'inside the page');
        $this->assertGreaterThan(595.28 - 12 * $mm, $left, 'in the right margin');
        $this->assertEqualsWithDelta(841.89 - 20 * $mm + 1.0, $top + $height, 0.01, 'starts 20 mm above the bottom edge');
        $this->assertGreaterThan($width * 5, $height, 'reads from bottom to top');

        [, , $text, , $size, , , , $angle] = $calls['text'][0];
        $this->assertSame(['powered by', 7.5, -90.0], [$text, $size, $angle]);

        // The wordmark: three polygons, four rectangles and the hole, all inside the white field.
        $this->assertCount(3, $calls['polygon']);
        $this->assertCount(6, $calls['rect']);
        $this->assertSame([1, 1, 1], $calls['rect'][5][4], 'the hole of the O');

        foreach ($calls['polygon'] as [$points]) {
            foreach (array_chunk($points, 2) as [$px, $py]) {
                $this->assertGreaterThanOrEqual($left - 0.001, $px);
                $this->assertLessThanOrEqual($left + $width + 0.001, $px);
                $this->assertGreaterThanOrEqual($top - 0.001, $py);
                $this->assertLessThanOrEqual($top + $height + 0.001, $py);
            }
        }

        foreach (\array_slice($calls['rect'], 1) as [$rx, $ry, $rw, $rh]) {
            $this->assertGreaterThanOrEqual($left - 0.001, $rx);
            $this->assertLessThanOrEqual($left + $width + 0.001, $rx + $rw);
            $this->assertGreaterThanOrEqual($top - 0.001, $ry);
            $this->assertLessThanOrEqual($top + $height + 0.001, $ry + $rh);
        }
    }

    public function testARenderedPdfCarriesTheLinkOnEveryPage(): void
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml('<html><body><p>Page one</p><p style="page-break-before:always">Page two</p></body></html>');
        $dompdf->render();
        Credit::stampPdf($dompdf);

        $pdf = (string) $dompdf->output(['compress' => 0]);

        $this->assertSame(2, substr_count($pdf, '(https://neo-digital.at)'), 'one link per page');
        $this->assertStringContainsString('powered by', $pdf);
    }
}
