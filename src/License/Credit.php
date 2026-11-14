<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\License;

use Dompdf\Canvas;
use Dompdf\Dompdf;

/**
 * Credit line of the free edition in the PDF report and in the e-mails — the
 * same line as the one the shared script of the Basic bundle draws in the
 * element: "powered by" and the NEO wordmark, with a link. Without a licence
 * only (Editions::UNBRANDED). Where only text is possible, it reads "powered by
 * NEO Digital" (the alternative text of the wordmark is "NEO Digital").
 *
 * The e-mail gets it as its last line, added to the rendered HTML, with the
 * wordmark as a PNG (resources/credit/neo-wordmark.png) that the mailer embeds
 * as an inline attachment (cid:), so no image is loaded from a server. The PDF
 * report gets it on every page, drawn on the canvas after Dompdf has laid out the
 * pages (stampPdf()); with Gotenberg it is the last line of the report, with the
 * wordmark as inline SVG. No design, preset or template takes part, so none can
 * leave it out.
 */
final class Credit
{
    public const URL = 'https://neo-digital.at';

    /** Words before the wordmark. */
    public const LEAD = 'powered by';

    /** Alternative text of the wordmark: "powered by NEO Digital" where no image is shown. */
    public const ALT = 'NEO Digital';

    /** The wordmark as PNG for the e-mail, 85 × 27 px (three times its size in the e-mail, 28 × 9 px). */
    public const MAIL_PNG = __DIR__.'/../../resources/credit/neo-wordmark.png';

    /** Font size in pt, colour of text and wordmark #4f5761 (7.3:1 on the white field behind it). */
    private const PDF_SIZE = 7.5;

    private const PDF_COLOR = [0.31, 0.341, 0.38];

    private const PDF_WHITE = [1, 1, 1];

    /** Middle of the text line from the right paper edge, start of the text above the bottom edge, both in mm. */
    private const PDF_FROM_RIGHT = 6.0;

    private const PDF_FROM_BOTTOM = 20.0;

    /** Cap height of Arimo (and of Helvetica/Arial), in em: the height of the wordmark. */
    private const PDF_CAP_HEIGHT = 0.716;

    /** Space between "by" and the wordmark, in em (as the line of the Basic bundle: 5 px at 11 px). */
    private const PDF_GAP = 0.45;

    /**
     * NEO wordmark, the same paths as in public/credit.js of the Basic bundle
     * (viewBox 449.49 × 143.2, y downwards): three polygons (N) as x, y pairs, the
     * rectangles of E and O as x, y, width, height, and the hole of the O, which is
     * drawn in white on top (the field behind the line is white). Public for the
     * generator of the PNG (tools/credit-wordmark/).
     */
    public const LOGO_WIDTH = 449.49;

    public const LOGO_HEIGHT = 143.2;

    public const LOGO_POLYGONS = [
        [21.72, 66.74, 28.64, 71.98, 28.64, 143.2, 0, 143.2, 0, 50.29],
        [141.67, 0, 141.67, 92.92, 119.95, 76.47, 113.04, 71.23, 113.04, 0],
        [141.67, 107.29, 141.67, 143.2, 141.66, 143.2, 113.04, 121.52, 28.64, 57.61, 0, 35.92, 0, 0, 0.01, 0, 28.64, 21.69, 113.04, 85.6],
    ];

    public const LOGO_RECTANGLES = [
        [153.14, 0, 141.67, 28.64],
        [153.14, 114.56, 141.67, 28.64],
        [153.14, 57.28, 141.67, 28.64],
        [306.29, 0, 143.2, 143.2],
    ];

    public const LOGO_HOLE = [334.93, 28.64, 85.92, 85.92];

    /**
     * Draws the credit on every page of a rendered Dompdf document: "powered by"
     * and the NEO wordmark, rotated by 90° (reads from bottom to top) in the right
     * page margin near the bottom right corner, on a white field, with a link over
     * the whole field. The wordmark is drawn as vectors in the colour of the text;
     * its rotated coordinates are computed here, so the canvas needs no transform.
     * Call after render().
     */
    public static function stampPdf(Dompdf $dompdf): void
    {
        $metrics = $dompdf->getFontMetrics();
        $font = $metrics->getFont('arimo') ?? $metrics->getFont('helvetica') ?? 'Helvetica';
        $textLength = $metrics->getTextWidth(self::LEAD, $font, self::PDF_SIZE);
        $scale = self::PDF_CAP_HEIGHT * self::PDF_SIZE / self::LOGO_HEIGHT;
        $logoStart = $textLength + self::PDF_GAP * self::PDF_SIZE;
        $length = $logoStart + self::LOGO_WIDTH * $scale;
        // Canvas::text() places the text one font height below the given point (unscaled by the height ratio).
        $lineHeight = $metrics->getFontHeight($font, self::PDF_SIZE) / max(0.1, (float) $dompdf->getOptions()->getFontHeightRatio());
        $mm = 72 / 25.4;
        $ascent = 0.75 * self::PDF_SIZE;
        $descent = 0.25 * self::PDF_SIZE;
        $pad = 1.0;

        $dompdf->getCanvas()->page_script(static function (int $page, int $pages, Canvas $canvas) use ($font, $length, $logoStart, $scale, $lineHeight, $mm, $ascent, $descent, $pad): void {
            // Rotated by -90°, the glyphs stand left of the baseline (ascent) and right of it (descent);
            // the line starts at $bottom and runs upwards.
            $baseline = $canvas->get_width() - self::PDF_FROM_RIGHT * $mm + ($ascent - $descent) / 2;
            $bottom = $canvas->get_height() - self::PDF_FROM_BOTTOM * $mm;
            $left = $baseline - $ascent - $pad;
            $top = $bottom - $length - $pad;
            $width = $ascent + $descent + 2 * $pad;
            $height = $length + 2 * $pad;

            $canvas->filled_rectangle($left, $top, $width, $height, self::PDF_WHITE);
            $canvas->text($baseline, $bottom - $lineHeight, self::LEAD, $font, self::PDF_SIZE, self::PDF_COLOR, 0.0, 0.0, -90.0);

            // A point (x, y) of the wordmark lies $logoStart + x·scale along the line and
            // (LOGO_HEIGHT - y)·scale above the baseline: on the page left of the baseline, above $bottom.
            $point = static fn (float $x, float $y): array => [$baseline - (self::LOGO_HEIGHT - $y) * $scale, $bottom - $logoStart - $x * $scale];
            $box = static function (array $rect) use ($point, $scale): array {
                [$x, $y, $w, $h] = $rect;
                [$pageX, $pageY] = $point($x + $w, $y);

                return [$pageX, $pageY, $h * $scale, $w * $scale];
            };

            foreach (self::LOGO_POLYGONS as $polygon) {
                $points = [];

                foreach (array_chunk($polygon, 2) as [$x, $y]) {
                    array_push($points, ...$point($x, $y));
                }

                $canvas->polygon($points, self::PDF_COLOR, null, [], true);
            }

            foreach (self::LOGO_RECTANGLES as $rect) {
                [$x, $y, $w, $h] = $box($rect);
                $canvas->filled_rectangle($x, $y, $w, $h, self::PDF_COLOR);
            }

            [$x, $y, $w, $h] = $box(self::LOGO_HOLE);
            $canvas->filled_rectangle($x, $y, $w, $h, self::PDF_WHITE);
            $canvas->add_link(self::URL, $left, $top, $width, $height);
        });
    }

    /**
     * Last line of the PDF report for engines without a canvas (Gotenberg, which
     * renders the HTML in Chromium): centred, small, own colours (readable on any
     * theme), the wordmark as inline SVG as high as the capitals.
     */
    public static function pdf(string $html): string
    {
        return self::beforeBodyEnd($html, sprintf(
            '<div style="margin:8mm 0 0;padding:2mm 0 0;border-top:1px solid #d1d9e0;text-align:center;font-size:7.5pt;line-height:1.4;page-break-inside:avoid;">'
            .'<a href="%s" style="display:inline-block;padding:.6mm 2mm;background:#ffffff;color:#4f5761;text-decoration:none;">%s %s</a></div>',
            self::URL,
            self::LEAD,
            self::svg(self::PDF_CAP_HEIGHT * self::PDF_SIZE, 'pt'),
        ));
    }

    /**
     * Last line of an e-mail, below the designed content (table markup for mail
     * programs). The wordmark goes in as a data URI; ReportMailer::inlineImages()
     * turns it into an inline attachment (cid:) when the e-mail is sent, the
     * previews in the back end show it as it is. Without images the mail program
     * shows the alternative text: "powered by NEO Digital".
     */
    public static function mail(string $html): string
    {
        $png = is_file(self::MAIL_PNG) ? (string) file_get_contents(self::MAIL_PNG) : '';
        $mark = '' === $png
            ? self::ALT
            : sprintf(
                // Font and colour on the image style its alternative text where a program shows that instead.
                '<img src="data:image/png;base64,%s" width="28" height="9" alt="%s" style="display:inline-block;width:28px;height:9px;margin:0 0 0 1px;border:0;outline:none;vertical-align:baseline;color:#4f5761;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:1;">',
                base64_encode($png),
                self::ALT,
            );

        return self::beforeBodyEnd($html, sprintf(
            '<table role="presentation" width="100%%" cellpadding="0" cellspacing="0"><tr><td align="center" style="padding:0 12px 24px;">'
            .'<a href="%s" target="_blank" rel="nofollow noopener" style="display:inline-block;padding:4px 10px;background:#ffffff;color:#4f5761;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:1.4;text-decoration:none;">%s %s</a>'
            .'</td></tr></table>',
            self::URL,
            self::LEAD,
            $mark,
        ));
    }

    /**
     * The wordmark as inline SVG in the colour of the text (#4f5761), with the
     * paths of the constants above; the hole of the O is cut out (even-odd), so
     * it works on any background. Height in $unit (a CSS unit); the width follows
     * from the proportions of the wordmark.
     */
    public static function svg(float $height, string $unit = 'px'): string
    {
        $shapes = '';

        foreach (self::LOGO_POLYGONS as $polygon) {
            $shapes .= '<polygon points="'.implode(' ', $polygon).'"/>';
        }

        foreach (self::LOGO_RECTANGLES as $index => [$x, $y, $w, $h]) {
            if ($index === array_key_last(self::LOGO_RECTANGLES)) {
                // The square of the O with its hole.
                [$hx, $hy, $hw, $hh] = self::LOGO_HOLE;
                $shapes .= sprintf('<path fill-rule="evenodd" d="M%s %sh%sv%sh-%sz M%s %sh%sv%sh-%sz"/>', $x, $y, $w, $h, $w, $hx, $hy, $hw, $hh, $hw);

                continue;
            }

            $shapes .= sprintf('<rect x="%s" y="%s" width="%s" height="%s"/>', $x, $y, $w, $h);
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %s %s" role="img" aria-label="%s" style="display:inline-block;width:%s%s;height:%s%s;vertical-align:baseline;"><title>%s</title><g fill="#4f5761">%s</g></svg>',
            self::LOGO_WIDTH,
            self::LOGO_HEIGHT,
            self::ALT,
            round($height * self::LOGO_WIDTH / self::LOGO_HEIGHT, 2),
            $unit,
            round($height, 2),
            $unit,
            self::ALT,
            $shapes,
        );
    }

    private static function beforeBodyEnd(string $html, string $line): string
    {
        $end = strripos($html, '</body>');

        return false === $end ? $html.$line : substr($html, 0, $end).$line.substr($html, $end);
    }
}
