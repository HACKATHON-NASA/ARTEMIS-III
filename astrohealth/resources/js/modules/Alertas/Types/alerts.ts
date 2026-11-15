import type { IndicatorCode, Status } from '@/types/health';

/**
 * Tipos de M5, con la forma de la API: GET /api/v1/alerts,
 * POST /api/v1/alerts (mensajes del astronauta),
 * PATCH /api/v1/alerts/{id}/acknowledge y GET /recommendations.
 *
 * Toda alerta sale hacia Tierra SOLA al crearse: no hay que "enviarla".
 * - `vital`: un valor en amarillo o rojo (sensores o registro manual).
 * - `sos`: emergencia del astronauta.
 * - `help`: pide ayuda médica (con un motivo).
 * - `report`: cuenta una novedad (no urgente).
 */
export type AlertKind = 'vital' | 'sos' | 'help' | 'report';

/** Rojo y amarillo como el semáforo; `info` para las novedades. */
export type AlertLevel = 'red' | 'yellow' | 'info';

export type Alert = {
    id: number;
    astronaut_id: number;
    kind: AlertKind;
    /** Solo en las de signos vitales. */
    indicator: IndicatorCode | null;
    level: AlertLevel;
    message: string;
    /** Lo que escribió el astronauta (ayuda, novedad o SOS). */
    note: string | null;
    source: 'device' | 'manual' | 'astronaut';
    /** Sale al crearse; llega a Tierra en ground_eta_at. */
    sent_at: string;
    ground_eta_at: string;
    delay_seconds: number;
    acknowledged_at: string | null;
    /** La respuesta de Tierra (tarda lo mismo en volver a la nave). */
    reply: string | null;
    created_at: string;
};

export type NewMessage = {
    kind: Exclude<AlertKind, 'vital'>;
    category: string | null;
    note: string;
};

export type Recommendation = {
    indicator: IndicatorCode;
    status: Exclude<Status, 'green'>;
    title: string;
    /** Pasos, uno por línea. */
    action_text: string;
    risk_ref: string | null;
};
