import type { IndicatorCode, Status } from '@/types/health';

/**
 * Tipos de la comparación de la tripulación (M4). Forma propuesta para
 * GET /api/v1/missions/{id}/crew-measurements?indicator=&range= (una serie
 * por tripulante, con las bandas de umbral que devuelve M2).
 */

export type TrendPoint = { day: number; value: number; status: Status };

export type CrewSeries = {
    astronaut_id: number;
    name: string;
    /** Color de identidad del tripulante (var(--series-N)), fijo por orden. */
    color: string;
    points: TrendPoint[];
};

export type Bands = {
    green_min: number;
    green_max: number;
    yellow_min: number;
    yellow_max: number;
};

/** 7 o 14 días, o toda la misión. */
export type TrendRange = 7 | 14 | 'mission';

/** Una fila del mapa de riesgos: el último valor de cada indicador. */
export type CrewSnapshotRow = {
    astronaut_id: number;
    name: string;
    color: string;
    values: Partial<Record<IndicatorCode, { value: number; status: Status }>>;
    /** Cuántos indicadores están en amarillo o rojo. */
    attention: number;
};

/** La dosis de radiación acumulada en la misión. */
export type DoseRow = {
    astronaut_id: number;
    name: string;
    color: string;
    total: number;
    days: number;
};
