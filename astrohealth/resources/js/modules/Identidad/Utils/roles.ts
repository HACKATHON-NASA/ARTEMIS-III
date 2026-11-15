import type { PersonRole, RoleOption } from '../Types/admin';

/**
 * Los roles de personas, para mostrar y para el formulario. Las reglas
 * reales (quién necesita misión y tripulante) las valida el backend (RoleAssignment);
 * aquí solo sirven para guiar el formulario.
 */
export const ROLE_OPTIONS: RoleOption[] = [
    {
        value: 'admin',
        label: 'Administración',
        location: 'earth',
        requiresAstronaut: false,
        requiresMission: false,
    },
    {
        value: 'flight_surgeon',
        label: 'Médico de vuelo',
        location: 'earth',
        requiresAstronaut: false,
        requiresMission: true,
    },
    {
        value: 'crew_medical_officer',
        label: 'Médico de la tripulación',
        location: 'space',
        requiresAstronaut: true,
        requiresMission: true,
    },
    {
        value: 'astronaut',
        label: 'Astronauta',
        location: 'space',
        requiresAstronaut: true,
        requiresMission: true,
    },
];

export function roleOption(role: PersonRole | null): RoleOption | undefined {
    return ROLE_OPTIONS.find((option) => option.value === role);
}
