/**
 * Tipos de M0 · Identidad, tal como los devuelve /api/v1/auth/*.
 */

export type ActorType = 'user' | 'device';

export type Role =
    | 'admin'
    | 'flight_surgeon'
    | 'crew_medical_officer'
    | 'astronaut'
    | 'device';

export type Location = 'space' | 'earth';

/** Quién es: persona o máquina, su rol y sus permisos (GET /auth/me). */
export type Actor = {
    type: ActorType;
    id: number;
    name: string;
    email: string | null;
    role: Role;
    role_label: string;
    location: Location;
    astronaut_id: number | null;
    mission_id: number | null;
    permissions: string[];
};

/** Respuesta de POST /auth/login y /auth/refresh. El refresh token va en una cookie HttpOnly. */
export type TokenResponse = {
    access_token: string;
    token_type: 'Bearer';
    expires_in: number;
    actor: Actor;
};
