<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Infrastructure\Configuracion;

use App\Modules\Gamificacion\Domain\Enums\PointReason;
use Illuminate\Contracts\Config\Repository as Config;

/**
 * Los números de M7 (config/gamification.php).
 */
final readonly class GamificationSettings
{
    public function __construct(private Config $config) {}

    public function pointsFor(PointReason $reason): int
    {
        $value = $this->config->get("gamification.points.{$reason->value}");

        return is_int($value) ? $value : 0;
    }

    public function sleepHours(): float
    {
        $value = $this->config->get('gamification.sleep_hours', 7);

        return is_int($value) || is_float($value) ? (float) $value : 7.0;
    }

    public function exerciseMinutes(): float
    {
        $value = $this->config->get('gamification.exercise_minutes', 90);

        return is_int($value) || is_float($value) ? (float) $value : 90.0;
    }

    public function levelSize(): int
    {
        $value = $this->config->get('gamification.level_size', 100);

        return is_int($value) ? $value : 100;
    }
}
