<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Domain\ValueObjects;

use App\Modules\Entorno\Domain\Enums\Environment;

/**
 * Los números de un entorno (de config/environments.php).
 */
final readonly class EnvironmentProfile
{
    /**
     * @param  list<string>  $risks  códigos de riesgo, en orden de importancia
     */
    public function __construct(
        public Environment $code,
        public string $name,
        public string $label,
        public float $gravityG,
        public float $radiationMsvPerDay,
        public float $groundDelaySeconds,
        public array $risks,
    ) {}
}
