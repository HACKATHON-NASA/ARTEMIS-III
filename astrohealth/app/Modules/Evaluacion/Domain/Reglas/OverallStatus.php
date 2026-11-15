<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Reglas;

use App\Modules\Evaluacion\Domain\Enums\Status;

/**
 * Regla: el estado general según el puntaje (desde 80 verde, desde 50
 * amarillo, si no rojo; los cortes llegan de config/evaluation.php).
 */
final class OverallStatus
{
    public static function fromScore(int $score, int $greenFrom, int $yellowFrom): Status
    {
        return match (true) {
            $score >= $greenFrom => Status::Green,
            $score >= $yellowFrom => Status::Yellow,
            default => Status::Red,
        };
    }
}
