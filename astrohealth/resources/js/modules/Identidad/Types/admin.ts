import type { Location, Role } from '@/types/auth';

/**
 * Tipos de la vista de administración, con la forma del JSON de
 * GET /api/v1/users y GET /api/v1/devices (M0).
 */

export type PersonRole = Exclude<Role, 'device'>;

export type ManagedUser = {
    id: number;
    name: string;
    email: string;
    role: PersonRole | null;
    astronaut_id: number | null;
    astronaut_name: string | null;
    /** La misión de la persona (nave y médicos de vuelo); admin no tiene. */
    mission_id: number | null;
    mission_name: string | null;
    /** El cargo en la tripulación (solo roles de la nave). */
    crew_role: string | null;
    active: boolean;
    last_login_at: string | null;
};

export type ManagedDevice = {
    id: number;
    name: string;
    client_id: string;
    mission_id: number;
    /** null = gateway de toda la misión. */
    astronaut_id: number | null;
    astronaut_name: string | null;
    active: boolean;
    last_seen_at: string | null;
};

export type CrewOption = {
    id: number;
    name: string;
    crew_role: string;
};

export type MissionSummary = {
    id: number;
    name: string;
    environment: 'iss' | 'transit' | 'moon' | 'mars';
    environment_label: string;
    day: number;
    day_count: number;
};

export type UserFormData = {
    name: string;
    email: string;
    role: PersonRole;
    mission_id: number | null;
    crew_role: string | null;
};

/** Lo que enlaza a la persona con su misión (lo resuelve el principal con M6). */
export type UserLink = {
    astronaut_id: number | null;
    astronaut_name: string | null;
    mission_name: string | null;
};

/** Una misión para elegir en el formulario de usuarios. */
export type MissionOption = {
    id: number;
    name: string;
    status: 'planned' | 'active' | 'finished';
    place: string;
};

export type DeviceFormData = {
    name: string;
    astronaut_id: number | null;
};

/** El secreto en texto plano llega UNA sola vez, al crear el dispositivo. */
export type CreatedDevice = {
    device: ManagedDevice;
    client_secret: string;
};

export type RoleOption = {
    value: PersonRole;
    label: string;
    location: Location;
    /** Va en la nave: se crea su tripulante (con cargo) en la misión. */
    requiresAstronaut: boolean;
    /** Sigue una misión (la nave y los médicos de vuelo). */
    requiresMission: boolean;
};
