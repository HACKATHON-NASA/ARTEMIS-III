import type { Alert } from '../Types/alerts';

/**
 * En qué paso va una alerta, a partir de sus fechas (las pone el backend):
 * viajando a Tierra → recibida en Tierra → atendida (con respuesta).
 */
export type AlertState = 'traveling' | 'received' | 'acknowledged';

export function alertState(alert: Alert, now = Date.now()): AlertState {
    if (alert.acknowledged_at !== null) {
        return 'acknowledged';
    }

    return new Date(alert.ground_eta_at).getTime() > now
        ? 'traveling'
        : 'received';
}

export const STATE_LABELS: Record<AlertState, string> = {
    traveling: 'Viajando a Tierra',
    received: 'Recibida en Tierra',
    acknowledged: 'Atendida',
};

/** Pestañas del centro de alertas. */
export type AlertFilter = 'open' | 'urgent' | 'acknowledged' | 'all';

export function matchesFilter(alert: Alert, filter: AlertFilter): boolean {
    const open = alert.acknowledged_at === null;

    return (
        filter === 'all' ||
        (filter === 'open' && open) ||
        (filter === 'urgent' && open && alert.level === 'red') ||
        (filter === 'acknowledged' && !open)
    );
}

/** Mientras la respuesta de Tierra viaja de vuelta: ms que faltan (0 si ya llegó). */
export function replyRemainingMs(alert: Alert, now = Date.now()): number {
    if (alert.acknowledged_at === null) {
        return 0;
    }

    const arrives =
        new Date(alert.acknowledged_at).getTime() + alert.delay_seconds * 1000;

    return Math.max(0, arrives - now);
}
