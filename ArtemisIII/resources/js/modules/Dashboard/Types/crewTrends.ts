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

/**
 * La tendencia de la semana de un indicador, como la calcula el backend
 * (M2): el promedio de hace una semana contra hoy, y si eso es empeorar
 * (alejarse del centro del rango sano).
 */
export type Trend = {
    direction: 'up' | 'down' | 'stable';
    worse: boolean;
    from: number;
    to: number;
};

/** Una fila del mapa de riesgos: el último valor de cada indicador. */
export type CrewSnapshotRow = {
    astronaut_id: number;
    name: string;
    color: string;
    values: Partial<Record<IndicatorCode, { value: number; status: Status }>>;
    /** Cuántos indicadores están en amarillo o rojo. */
    attention: number;
};

/** Un indicador que cambió de color en los últimos días (para el Panel de salud). */
export type HealthChange = {
    astronaut_id: number;
    name: string;
    indicator: IndicatorCode;
    from: { value: number; status: Status };
    to: { value: number; status: Status };
    /** true = empeoró (pasó a amarillo o rojo); false = se recuperó. */
    worse: boolean;
};

/** La dosis de radiación acumulada en la misión. */
export type DoseRow = {
    astronaut_id: number;
    name: string;
    color: string;
    total: number;
    days: number;
};

/** Una fila de "Quién necesita atención" (Panel de salud). */
export type WatchRow = {
    id: number;
    name: string;
    score: number | null;
    status: Status | null;
    summary: string;
};

/** Lo que necesita la gráfica de líneas para nombrar y formatear los valores. */
export type ChartMeta = { label: string; unit: string; decimals: number };
