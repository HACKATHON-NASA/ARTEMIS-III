/**
 * Tiempos para mostrar: "hace 5 min", "hace 2 h".
 */
const relative = new Intl.RelativeTimeFormat('es', { numeric: 'auto' });

const STEPS: [Intl.RelativeTimeFormatUnit, number][] = [
    ['second', 60],
    ['minute', 60],
    ['hour', 24],
    ['day', 30],
    ['month', 12],
];

export function timeAgo(iso: string | null): string {
    if (iso === null) {
        return 'Nunca';
    }

    let value = (new Date(iso).getTime() - Date.now()) / 1000;

    for (const [unit, size] of STEPS) {
        if (Math.abs(value) < size) {
            return relative.format(Math.round(value), unit);
        }

        value /= size;
    }

    return relative.format(Math.round(value), 'year');
}

/** Un dispositivo está "en línea" si habló con el sistema en los últimos 5 minutos. */
export function isOnline(lastSeenAt: string | null): boolean {
    return (
        lastSeenAt !== null &&
        Date.now() - new Date(lastSeenAt).getTime() < 5 * 60 * 1000
    );
}

export function minutesAgo(minutes: number): string {
    return new Date(Date.now() - minutes * 60 * 1000).toISOString();
}
