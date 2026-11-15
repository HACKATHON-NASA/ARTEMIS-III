<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios;

use App\Shared\ModuleServiceProvider;

/**
 * M8 · Recordatorios: la rutina diaria de cada tripulante. Al cumplir uno
 * publica ReminderCompleted (M7 suma los puntos).
 */
final class RecordatoriosServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Recordatorios';
    }
}
