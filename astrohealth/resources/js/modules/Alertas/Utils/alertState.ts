import type { Alert } from '../Types/alerts';

/**
 * En qué paso va una alerta, a partir de sus fechas (las pone el backend):
 * sin enviar → viajando a Tierra → recibida en Tierra → confirmada.
 */
export type AlertState = 'pending' | 'sent' | 'received' | 'acknowledged';

export function alertState(alert: Alert, now = Date.now()): AlertState {
    if (alert.acknowledged_at !== null) {
        return 'acknowledged';
    }

    if (!alert.sent_to_ground) {
        return 'pending';
    }

    return alert.ground_eta_at !== null &&
        new Date(alert.ground_eta_at).getTime() > now
        ? 'sent'
        : 'received';
}

export const STATE_LABELS: Record<AlertState, string> = {
    pending: 'Sin enviar',
    sent: 'Viajando a Tierra',
    received: 'Recibida en Tierra',
    acknowledged: 'Confirmada',
};

/** Los filtros del centro de alertas (como `state` de GET /alerts). */
export type AlertFilter = 'all' | 'pending' | 'sent' | 'acknowledged';

export function matchesFilter(alert: Alert, filter: AlertFilter): boolean {
    const state = alertState(alert);

    if (filter === 'all') {
        return true;
    }

    if (filter === 'sent') {
        return state === 'sent' || state === 'received';
    }

    return state === filter;
}
