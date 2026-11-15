/**
 * La misión actual, como la devolverá GET /api/v1/missions/current (M6).
 */
export type EnvironmentCode = 'iss' | 'transit' | 'moon' | 'mars';

export type CurrentMission = {
    id: number;
    name: string;
    environment: EnvironmentCode;
    environment_label: string;
    gravity_g: number;
    /** Retraso de la comunicación con Tierra, en segundos (un sentido). */
    ground_delay_seconds: number;
    day: number;
    day_count: number;
};
