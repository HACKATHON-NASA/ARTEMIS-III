<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Tests\Unit;

use App\Modules\Evaluacion\Domain\Enums\Status;
use App\Modules\Evaluacion\Domain\Enums\TrendDirection;
use App\Modules\Evaluacion\Domain\Excepciones\InvalidThreshold;
use App\Modules\Evaluacion\Domain\Reglas\BodyStatusRule;
use App\Modules\Evaluacion\Domain\Reglas\OverallStatus;
use App\Modules\Evaluacion\Domain\Reglas\RadiationThreshold;
use App\Modules\Evaluacion\Domain\Reglas\ScoreRule;
use App\Modules\Evaluacion\Domain\Reglas\StatusRule;
use App\Modules\Evaluacion\Domain\Reglas\TrendRule;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Las reglas puras de M2, sin Laravel.
 */
final class RulesTest extends TestCase
{
    private const POINTS = ['green' => 100, 'yellow' => 60, 'red' => 20];

    private const WEIGHTS = ['hr' => 1.5, 'bp_sys' => 1.5, 'bp_dia' => 1.5, 'spo2' => 1.5, 'radiation' => 1.5];

    /**
     * Bordes del plan §2: [indicador, verde, amarillo, valor, estado esperado].
     *
     * @return iterable<string, array{array{0: float, 1: float, 2: float, 3: float}, float, Status}>
     */
    public static function borders(): iterable
    {
        $hr = [60, 100, 50, 120];
        yield 'pulso 59 → amarillo' => [$hr, 59, Status::Yellow];
        yield 'pulso 60 → verde' => [$hr, 60, Status::Green];
        yield 'pulso 100 → verde' => [$hr, 100, Status::Green];
        yield 'pulso 101 → amarillo' => [$hr, 101, Status::Yellow];
        yield 'pulso 120 → amarillo' => [$hr, 120, Status::Yellow];
        yield 'pulso 121 → rojo' => [$hr, 121, Status::Red];
        yield 'pulso 49 → rojo' => [$hr, 49, Status::Red];

        $bpSys = [90, 119, 90, 139];
        yield 'sistólica 119 → verde' => [$bpSys, 119, Status::Green];
        yield 'sistólica 120 → amarillo' => [$bpSys, 120, Status::Yellow];
        yield 'sistólica 140 → rojo' => [$bpSys, 140, Status::Red];
        yield 'sistólica 148 → rojo (Elena)' => [$bpSys, 148, Status::Red];
        yield 'sistólica 89 → rojo' => [$bpSys, 89, Status::Red];

        $sleep = [7, 9, 6, 24];
        yield 'sueño 7 → verde' => [$sleep, 7, Status::Green];
        yield 'sueño 6,2 → amarillo' => [$sleep, 6.2, Status::Yellow];
        yield 'sueño 6 → amarillo' => [$sleep, 6, Status::Yellow];
        yield 'sueño 5,8 → rojo (Kenji)' => [$sleep, 5.8, Status::Red];

        $spo2 = [95, 100, 90, 100];
        yield 'SpO₂ 95 → verde' => [$spo2, 95, Status::Green];
        yield 'SpO₂ 93 → amarillo' => [$spo2, 93, Status::Yellow];
        yield 'SpO₂ 89 → rojo' => [$spo2, 89, Status::Red];

        $temp = [36.1, 37.2, 35.5, 38.0];
        yield 'temperatura 37,3 → amarillo' => [$temp, 37.3, Status::Yellow];
        yield 'temperatura 38,1 → rojo' => [$temp, 38.1, Status::Red];
    }

    /**
     * @param  array{0: float, 1: float, 2: float, 3: float}  $band
     */
    #[DataProvider('borders')]
    public function test_status_rule_borders(array $band, float $value, Status $expected): void
    {
        $threshold = new Threshold('x', 'mars', ...$band);

        $this->assertSame($expected, StatusRule::classify($value, $threshold));
    }

    public function test_a_threshold_must_keep_the_green_inside_the_yellow(): void
    {
        foreach ([[100, 60, 50, 120, 'green_min'], [60, 100, 70, 120, 'yellow_min'], [60, 100, 50, 90, 'yellow_max']] as [$gMin, $gMax, $yMin, $yMax, $field]) {
            try {
                new Threshold('hr', 'mars', $gMin, $gMax, $yMin, $yMax);
                $this->fail("Se esperaba InvalidThreshold en {$field}.");
            } catch (InvalidThreshold $e) {
                $this->assertArrayHasKey($field, $e->errors());
            }
        }
    }

    public function test_radiation_depends_on_the_environment(): void
    {
        $mars = RadiationThreshold::for('mars', 0.6);

        $this->assertSame(Status::Green, StatusRule::classify(0.52, $mars));
        $this->assertSame(Status::Yellow, StatusRule::classify(0.94, $mars));
        $this->assertSame(Status::Red, StatusRule::classify(1.3, $mars));
        $this->assertSame(Status::Green, StatusRule::classify(0.94, RadiationThreshold::for('transit', 1.8)));
    }

    public function test_score_is_the_weighted_average(): void
    {
        // Kenji: todo verde salvo el sueño (rojo) y el ánimo (amarillo) → 90.
        $kenji = array_fill_keys(['hr', 'bp_sys', 'bp_dia', 'spo2', 'radiation', 'glucose', 'temp', 'exercise_min'], Status::Green)
            + ['sleep' => Status::Red, 'mood' => Status::Yellow];

        $this->assertSame(90, ScoreRule::weighted($kenji, self::POINTS, self::WEIGHTS));
        $this->assertSame(100, ScoreRule::weighted(['hr' => Status::Green], self::POINTS, self::WEIGHTS));
        $this->assertSame(20, ScoreRule::weighted(['hr' => Status::Red, 'mood' => Status::Red], self::POINTS, self::WEIGHTS));
        // El corazón pesa más: rojo en hr (×1,5) y verde en ánimo (×1) → (30 + 100) / 2,5 = 52.
        $this->assertSame(52, ScoreRule::weighted(['hr' => Status::Red, 'mood' => Status::Green], self::POINTS, self::WEIGHTS));
        $this->assertNull(ScoreRule::weighted([], self::POINTS, self::WEIGHTS));
    }

    public function test_overall_status_cuts(): void
    {
        $this->assertSame(Status::Green, OverallStatus::fromScore(80, 80, 50));
        $this->assertSame(Status::Yellow, OverallStatus::fromScore(79, 80, 50));
        $this->assertSame(Status::Yellow, OverallStatus::fromScore(50, 80, 50));
        $this->assertSame(Status::Red, OverallStatus::fromScore(49, 80, 50));
    }

    public function test_each_zone_takes_its_worst_reading(): void
    {
        $zones = BodyStatusRule::byZone([
            ['zone' => 'heart', 'status' => Status::Green],
            ['zone' => 'heart', 'status' => Status::Red],
            ['zone' => 'heart', 'status' => Status::Yellow],
            ['zone' => 'brain', 'status' => Status::Yellow],
        ]);

        $this->assertSame(['heart' => Status::Red, 'brain' => Status::Yellow], $zones);
    }

    public function test_trend_sleep_falling_three_times_warns(): void
    {
        $sleep = new Threshold('sleep', 'mars', 7, 9, 6, 24);
        $trend = TrendRule::analyze([7.5, 7.1, 6.8, 6.5], $sleep, 7, 3, 2.0);

        $this->assertSame(TrendDirection::Down, $trend->direction);
        $this->assertTrue($trend->alert, 'Se aleja del centro del verde 3 veces seguidas.');
    }

    public function test_trend_stable_and_recovering_values_do_not_warn(): void
    {
        $hr = new Threshold('hr', 'mars', 60, 100, 50, 120);

        $stable = TrendRule::analyze([80, 80.2, 79.9, 80.1, 80], $hr, 7, 3, 2.0);
        $this->assertSame(TrendDirection::Stable, $stable->direction);
        $this->assertFalse($stable->alert);

        $recovering = TrendRule::analyze([110, 104, 98, 90], $hr, 7, 3, 2.0);
        $this->assertSame(TrendDirection::Down, $recovering->direction);
        $this->assertFalse($recovering->alert, 'Bajar hacia el centro del verde es mejorar.');
    }

    public function test_trend_with_too_few_values_is_stable(): void
    {
        $trend = TrendRule::analyze([7.0], new Threshold('sleep', 'mars', 7, 9, 6, 24), 7, 3, 2.0);

        $this->assertSame(TrendDirection::Stable, $trend->direction);
        $this->assertFalse($trend->alert);
    }
}
