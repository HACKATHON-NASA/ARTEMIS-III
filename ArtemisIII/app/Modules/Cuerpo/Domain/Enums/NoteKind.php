<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Domain\Enums;

/**
 * El tipo de una nota clínica.
 */
enum NoteKind: string
{
    case Evolution = 'evolution';
    case Impression = 'impression';
    case Plan = 'plan';
    case FollowUp = 'follow_up';
}
