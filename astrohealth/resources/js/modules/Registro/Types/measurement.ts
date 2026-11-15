import type { BodyZoneCode, IndicatorCode, Status } from '@/types/health';

/**
 * Tipos de M1 · Registro, con la forma de la API:
 * GET /api/v1/indicators y POST /api/v1/measurements.
 */

export type Indicator = {
    code: IndicatorCode;
    name: string;
    unit: string;
    /** Rango posible: solo rechaza errores de digitación (no es clínico). */
    min: number;
    max: number;
    /** Paso del campo: 1 para enteros, 0.1 o 0.01 para decimales. */
    step: number;
    body_part: BodyZoneCode;
    sort_order: number;
    /** "¿Cómo me mido?": cómo medirlo y por qué importa en el espacio. */
    help_text: { how: string; why: string };
};

export type Measurement = {
    id: number;
    indicator: IndicatorCode;
    value: number;
    unit: string;
    status: Status;
    body_part: BodyZoneCode;
    measured_at: string;
    source: 'manual' | 'device';
};

export type RecordPayload = {
    measured_at?: string;
    measurements: { indicator: IndicatorCode; value: number }[];
};

/** Un valor que vuela de su campo a su órgano (posiciones en la ventana). */
export type Flight = {
    id: string;
    label: string;
    status: Status;
    from: { x: number; y: number };
    to: { x: number; y: number };
};
