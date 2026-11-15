<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Enums;

/**
 * Códigos de error de M0 · Identidad. Un código publicado no cambia.
 */
enum ErrorCode: string
{
    case InvalidCredentials = 'AUTH_INVALID_CREDENTIALS';
    case UserInactive = 'AUTH_USER_INACTIVE';
    case UserWithoutRole = 'AUTH_USER_WITHOUT_ROLE';
    case TokenInvalid = 'AUTH_TOKEN_INVALID';
    case TokenExpired = 'AUTH_TOKEN_EXPIRED';
    case RefreshInvalid = 'AUTH_REFRESH_INVALID';
    case RefreshReused = 'AUTH_REFRESH_REUSED';
    case DeviceInvalid = 'AUTH_DEVICE_INVALID';
    case InvalidRoleAssignment = 'AUTH_INVALID_ROLE_ASSIGNMENT';
    case OriginNotAllowed = 'AUTH_ORIGIN_NOT_ALLOWED';
}
