<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Reglas;

/**
 * Regla: el nivel. Nivel = puntos / tamaño + 1; la barra muestra el resto.
 */
final class LevelRule
{
    /**
     * @return array{level: int, progress: int}
     */
    public static function of(int $points, int $size): array
    {
        $size = max(1, $size);
        $points = max(0, $points);

        return ['level' => intdiv($points, $size) + 1, 'progress' => $points % $size];
    }
}
