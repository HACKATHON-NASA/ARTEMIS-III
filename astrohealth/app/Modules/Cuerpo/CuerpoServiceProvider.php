<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo;

use App\Shared\ModuleServiceProvider;

/**
 * M3 · Cuerpo: dueño de body_parts y clinical_notes. Muestra cómo está cada
 * zona del cuerpo y guarda el expediente privado del equipo médico.
 */
final class CuerpoServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Cuerpo';
    }
}
