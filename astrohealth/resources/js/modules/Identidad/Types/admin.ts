import type { Location, Role } from '@/types/auth';

/**
 * Tipos de la vista de administración. Tienen la forma del JSON que
 * devolverán GET /api/v1/users y GET /api/v1/devices (M0, pendientes en el
 * backend): cuando existan, el store cambia los datos de prueba por la API
 * sin tocar los componentes.
 */

export type PersonRole = Exclude<Role, 'device'>;

export type ManagedUser = {
    id: number;
    name: string;
    email: string;
    role: PersonRole | null;
    astronaut_id: number | null;
    astronaut_name: string | null;
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
    astronaut_id: number | null;
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
    requiresAstronaut: boolean;
};
