<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Enums;

/**
 * El tema de una indicación del médico.
 */
enum AdviceCategory: string
{
    case Sleep = 'sleep';
    case Exercise = 'exercise';
    case Nutrition = 'nutrition';
    case Medication = 'medication';
    case Rest = 'rest';
    case Mental = 'mental';
    case Other = 'other';
}
