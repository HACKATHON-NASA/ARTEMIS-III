<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Excepciones;

use App\Modules\Identidad\Domain\Enums\ErrorCode;
use App\Shared\Excepciones\BusinessError;

/**
 * El médico de vuelo debe seguir una misión.
 */
final class MissionRequired extends BusinessError
{
    public function __construct(string $message = 'El médico de vuelo debe seguir una misión.')
    {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return ErrorCode::MissionRequired->value;
    }

    public function field(): string
    {
        return 'mission_id';
    }
}
