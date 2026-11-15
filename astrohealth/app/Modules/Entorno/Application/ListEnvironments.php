<?php

declare(strict_types=1);

namespace App\Modules\Entorno\Application;

use App\Modules\Entorno\Domain\ValueObjects\EnvironmentProfile;
use App\Modules\Entorno\Infrastructure\Catalogo\EnvironmentCatalog;

/**
 * Caso de uso: los 4 entornos con sus números (gravedad, radiación, retraso, riesgos).
 */
final readonly class ListEnvironments
{
    public function __construct(private EnvironmentCatalog $catalog) {}

    /**
     * @return list<EnvironmentProfile>
     */
    public function execute(): array
    {
        return $this->catalog->all();
    }
}
