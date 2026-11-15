<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application\DTOs;

use App\Modules\Evaluacion\Domain\Entidades\Assessment;
use App\Modules\Evaluacion\Domain\Enums\Status;

/**
 * La última evaluación con su estado general (sale del puntaje).
 */
final readonly class AssessmentResult
{
    public function __construct(
        public Assessment $assessment,
        public Status $status,
    ) {}
}
