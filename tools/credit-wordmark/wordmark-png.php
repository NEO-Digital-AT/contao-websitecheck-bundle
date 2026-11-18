<?php

declare(strict_types=1);

/*
 * Writes resources/credit/neo-wordmark.png, the NEO wordmark of the credit line
 * in the e-mail, from the paths in src/License/Credit.php (the same as in
 * public/credit.js of the Basic bundle). Colour #4f5761 on white, 85 × 27 px:
 * three times the 28 × 9 px of the e-mail, sharp on displays with 2x and 3x
 * pixel density. Drawn eight times larger with GD (a requirement of the bundle)
 * and scaled down with resampling, which smooths the edges.
 *
 *     php tools/credit-wordmark/wordmark-png.php
 */

require __DIR__.'/../../src/License/Credit.php';

use Neo\WebsitecheckBundle\License\Credit;

const WIDTH = 85;
const HEIGHT = 27;
const OVERSAMPLE = 8;

$big = imagecreatetruecolor(WIDTH * OVERSAMPLE, HEIGHT * OVERSAMPLE);
$white = imagecolorallocate($big, 255, 255, 255);
$ink = imagecolorallocate($big, 0x4F, 0x57, 0x61);
imagefilledrectangle($big, 0, 0, WIDTH * OVERSAMPLE - 1, HEIGHT * OVERSAMPLE - 1, $white);

// The wordmark fills the height; the width that is left over (85 px instead of
// 84.75) is split on both sides.
$scale = HEIGHT * OVERSAMPLE / Credit::LOGO_HEIGHT;
$left = (WIDTH * OVERSAMPLE - Credit::LOGO_WIDTH * $scale) / 2;
$point = static fn (float $x, float $y): array => [(int) round($left + $x * $scale), (int) round($y * $scale)];

foreach (Credit::LOGO_POLYGONS as $polygon) {
    $points = [];

    foreach (array_chunk($polygon, 2) as [$x, $y]) {
        array_push($points, ...$point($x, $y));
    }

    imagefilledpolygon($big, $points, $ink);
}

$box = static function (array $rect, int $colour) use ($big, $point): void {
    [$x, $y, $w, $h] = $rect;
    [$x0, $y0] = $point($x, $y);
    [$x1, $y1] = $point($x + $w, $y + $h);
    imagefilledrectangle($big, $x0, $y0, $x1 - 1, $y1 - 1, $colour);
};

foreach (Credit::LOGO_RECTANGLES as $rect) {
    $box($rect, $ink);
}

$box(Credit::LOGO_HOLE, $white);

$small = imagecreatetruecolor(WIDTH, HEIGHT);
imagecopyresampled($small, $big, 0, 0, 0, 0, WIDTH, HEIGHT, WIDTH * OVERSAMPLE, HEIGHT * OVERSAMPLE);

$target = __DIR__.'/../../resources/credit/neo-wordmark.png';
imagepng($small, $target, 9);
echo realpath($target), ': ', filesize($target), " bytes\n";
