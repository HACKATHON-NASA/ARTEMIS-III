import type { EnvironmentCode } from './mission';

/**
 * Un entorno como lo devuelve GET /api/v1/environments (M6): los números
 * salen de config/environments.php y los riesgos son códigos.
 */
export type RiskCode =
    | 'microgravity'
    | 'fluids'
    | 'radiation'
    | 'isolation'
    | 'distance'
    | 'dust'
    | 'stress';

export type EnvironmentData = {
    code: EnvironmentCode;
    name: string;
    label: string;
    gravity_g: number;
    radiation_msv_day: number;
    ground_delay_seconds: number;
    risks: RiskCode[];
};
