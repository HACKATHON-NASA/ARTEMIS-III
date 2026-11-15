import { currentLocale, t } from '@/lib/i18n';

/**
 * Tiempos para mostrar: "hace 5 min", "hace 2 h".
 */

const STEPS: [Intl.RelativeTimeFormatUnit, number][] = [
    ['second', 60],
    ['minute', 60],
    ['hour', 24],
    ['day', 30],
    ['month', 12],
];

export function timeAgo(iso: string | null): string {
    if (iso === null) {
        return t('Identidad.admin.never');
    }

    // En el idioma de ahora: "5 min ago" · "hace 5 min".
    const relative = new Intl.RelativeTimeFormat(currentLocale(), {
        numeric: 'auto',
    });
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
