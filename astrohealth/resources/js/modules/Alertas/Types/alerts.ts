import type { IndicatorCode, Status } from '@/types/health';

/**
 * Tipos de M5, con la forma de la API:
 * GET /api/v1/alerts, POST /alerts/{id}/send-to-ground y GET /recommendations.
 */
export type Alert = {
    id: number;
    astronaut_id: number;
    indicator: IndicatorCode;
    level: Exclude<Status, 'green'>;
    message: string;
    sent_to_ground: boolean;
    sent_at: string | null;
    ground_eta_at: string | null;
    delay_seconds: number;
    acknowledged_at: string | null;
    created_at: string;
};

export type Recommendation = {
    indicator: IndicatorCode;
    status: Exclude<Status, 'green'>;
    title: string;
    /** Pasos, uno por línea. */
    action_text: string;
    risk_ref: string | null;
};
