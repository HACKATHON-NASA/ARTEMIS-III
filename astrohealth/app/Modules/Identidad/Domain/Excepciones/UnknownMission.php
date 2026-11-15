<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * La misión indicada no existe (M6).
 */
final class UnknownMission extends BusinessError
{
    public function __construct(string $message = 'La misión no existe.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::UnknownMission->value;
    }

    public function field(): string
    {
        return 'mission_id';
    }
}
