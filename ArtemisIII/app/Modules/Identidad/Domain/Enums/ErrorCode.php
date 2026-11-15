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
    case EmailTaken = 'AUTH_EMAIL_TAKEN';
    case AstronautTaken = 'AUTH_ASTRONAUT_TAKEN';
    case UnknownAstronaut = 'AUTH_UNKNOWN_ASTRONAUT';
    case UnknownMission = 'AUTH_UNKNOWN_MISSION';
    case MissionRequired = 'AUTH_MISSION_REQUIRED';
    case AstronautNotInMission = 'AUTH_ASTRONAUT_NOT_IN_MISSION';
    case ClientIdTaken = 'AUTH_CLIENT_ID_TAKEN';
    case WeakDeviceSecret = 'AUTH_WEAK_DEVICE_SECRET';
    case OwnAccessLocked = 'AUTH_OWN_ACCESS_LOCKED';
    case UserNotFound = 'AUTH_USER_NOT_FOUND';
    case DeviceNotFound = 'AUTH_DEVICE_NOT_FOUND';
}
