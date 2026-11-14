<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Domain\Enums;

/**
 * Roles de AstroHealth: 2 en la nave, 2 en Tierra y la máquina (el simulador).
 *
 * Se guarda como texto en users.role; agregar un rol no exige una migración.
 */
enum Role: string
{
    case Astronaut = 'astronaut';
    case CrewMedicalOfficer = 'crew_medical_officer';
    case FlightSurgeon = 'flight_surgeon';
    case Admin = 'admin';
    case Device = 'device';

    public function location(): Location
    {
        return match ($this) {
            self::Astronaut, self::CrewMedicalOfficer, self::Device => Location::Space,
            self::FlightSurgeon, self::Admin => Location::Earth,
        };
    }

    /** Los roles de la nave son tripulantes: deben estar enlazados a un astronauta. */
    public function requiresAstronaut(): bool
    {
        return $this === self::Astronaut || $this === self::CrewMedicalOfficer;
    }

    /** Solo las personas inician sesión con correo y contraseña. */
    public function isHuman(): bool
    {
        return $this !== self::Device;
    }

    public function label(): string
    {
        return match ($this) {
            self::Astronaut => 'Astronauta',
            self::CrewMedicalOfficer => 'Médico de la tripulación',
            self::FlightSurgeon => 'Médico en Tierra',
            self::Admin => 'Administrador',
            self::Device => 'Dispositivo',
        };
    }

    /**
     * Roles que puede tener un usuario (persona).
     *
     * @return list<self>
     */
    public static function humanRoles(): array
    {
        return array_values(array_filter(self::cases(), fn (self $role): bool => $role->isHuman()));
    }
}
