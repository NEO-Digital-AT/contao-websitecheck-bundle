<?php

declare(strict_types=1);

namespace Neo\WebsitecheckBundle\Tests\Check;

use Neo\WebsitecheckBundle\Check\DemoData;
use Neo\WebsitecheckBundle\Check\Grader;
use Neo\WebsitecheckBundle\Tests\Support\Translations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/** Grades 1 (best) to 5 for the seven areas and overall, on the bundled demo measurement. */
#[CoversClass(Grader::class)]
#[CoversClass(DemoData::class)]
final class GraderTest extends TestCase
{
    /** @return iterable<string, array{float, int}> */
    public static function scores(): iterable
    {
        yield '100' => [100.0, 1];
        yield '90' => [90.0, 1];
        yield '89.9' => [89.9, 2];
        yield '78' => [78.0, 2];
        yield '65' => [65.0, 3];
        yield '64.9' => [64.9, 4];
        yield '50' => [50.0, 4];
        yield '49.9' => [49.9, 5];
        yield '0' => [0.0, 5];
    }

    #[DataProvider('scores')]
    public function testScoreToGrade(float $score, int $grade): void
    {
        $this->assertSame($grade, Grader::gradeFor($score));
    }

    public function testTheDemoMeasurementIsGradedInEveryArea(): void
    {
        $result = $this->grade((new DemoData())->data());

        $this->assertSame(Grader::AREAS, array_column($result['areas'], 'key'));
        $this->assertSame('example-hotel.com', $result['domain']);
        $grades = [];
        $sum = 0.0;

        foreach ($result['areas'] as $area) {
            $this->assertNotSame('area.'.$area['key'], $area['label'], 'translated');

            if (null === $area['score']) {
                $this->assertSame(0, $area['grade']);
                continue;
            }

            $this->assertGreaterThanOrEqual(0, $area['score']);
            $this->assertLessThanOrEqual(100, $area['score']);
            $this->assertSame(Grader::gradeFor($area['score']), $area['grade']);
            $grades[] = $area['grade'];
            $sum += $area['score'];
        }

        // The average decides, but the overall grade is at most one better than the worst area.
        $this->assertSame(max(Grader::gradeFor($sum / \count($grades)), max($grades) - 1), $result['grade']);
        $this->assertContains($result['verdictKey'], ['good', 'mid', 'low']);
        $this->assertLessThanOrEqual(3, \count($result['measures']));
        $this->assertLessThanOrEqual(4, \count($result['strengths']));
    }

    public function testAKnownVulnerabilityMakesTheOverallGradeFourAtBest(): void
    {
        $data = (new DemoData())->data();
        $data['server'] = ['php' => true, 'raw' => '8.3.12', 'source' => 'header', 'phpinfo' => ''];

        $result = $this->grade($data);

        $this->assertGreaterThanOrEqual(4, $result['grade']);
        $this->assertSame('critical', $result['alerts'][0]['level']);
        $this->assertSame('vulnerable', $result['server']['status']);
        $this->assertSame('server', $result['measures'][0]['area'], 'the first measure');
    }

    public function testWithoutMeasurementOfThePageSpeedTheAreasAreUnavailable(): void
    {
        $data = (new DemoData())->data();
        $data['psi']['home_mobile'] = ['error' => 'timeout'];

        $result = $this->grade($data);
        $byKey = array_column($result['areas'], null, 'key');

        $this->assertNull($byKey['speed']['score']);
        $this->assertSame(0, $byKey['speed']['grade']);
        $this->assertSame(['perf' => null, 'a11y' => null, 'bp' => null, 'seo' => null], $result['scores']);
    }

    /** @param array<string, mixed> $data */
    private function grade(array $data): array
    {
        return (new Grader(Translations::translator()))->grade($data, 'en');
    }
}
