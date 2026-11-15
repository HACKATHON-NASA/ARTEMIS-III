<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Domain\Enums;

/**
 * psychological: un cuestionario (suma de puntos) · cognitive: una prueba
 * de rendimiento (PVT, promedio en milisegundos).
 */
enum PsychKind: string
{
    case Psychological = 'psychological';
    case Cognitive = 'cognitive';
}
