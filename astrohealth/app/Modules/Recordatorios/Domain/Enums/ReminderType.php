<?php

declare(strict_types=1);

namespace App\Modules\Recordatorios\Domain\Enums;

/**
 * De qué es un recordatorio (el frontend le pone su ícono).
 */
enum ReminderType: string
{
    case MeasureBp = 'measure_bp';
    case Exercise = 'exercise';
    case LogSleep = 'log_sleep';
    case MoodCheck = 'mood_check';
    case Dosimeter = 'dosimeter';
    case Custom = 'custom';
}
