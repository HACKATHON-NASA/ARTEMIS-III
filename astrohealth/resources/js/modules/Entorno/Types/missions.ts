import type { EnvironmentCode } from './mission';

/**
 * Tipos de la gestión de misiones (M6, vista de administración), con la
 * forma que tendrán GET/POST /api/v1/missions y GET /api/v1/astronauts.
 */

export type MissionStatus = 'planned' | 'active' | 'finished';

export type Mission = {
    id: number;
    name: string;
    environment: EnvironmentCode;
    /** Fecha local YYYY-MM-DD. */
    start_date: string;
    day_count: number;
    objective: string;
};

/** Un tripulante y la misión a la que pertenece. */
export type Astronaut = {
    id: number;
    name: string;
    crew_role: string;
    mission_id: number;
};

export type MissionFormData = {
    name: string;
    environment: EnvironmentCode;
    start_date: string;
    day_count: number;
    objective: string;
};

/** La misión con lo que se calcula de sus fechas y su tripulación. */
export type MissionView = Mission & {
    status: MissionStatus;
    /** Día de misión (1…day_count) si está en curso; si no, null. */
    current_day: number | null;
    end_date: string;
    crew: Astronaut[];
};

export type MissionFilter = 'all' | MissionStatus;
