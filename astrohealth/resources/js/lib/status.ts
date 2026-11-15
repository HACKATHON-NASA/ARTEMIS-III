import type { MaybeStatus } from '@/types/health';
import { t } from './i18n';

/**
 * Cómo se ve cada estado del semáforo. Los colores son SIEMPRE las variables
 * --status-* de app.css (norma P8): aquí solo se nombran, nunca se calculan.
 */
export type StatusMeta = {
    label: string;
    color: string;
    /** Clases de Tailwind para fondos y textos suaves. */
    soft: string;
};

const META: Record<'green' | 'yellow' | 'red' | 'none', StatusMeta> = {
    green: {
        label: 'Normal',
        color: 'var(--status-green)',
        soft: 'bg-[var(--status-green)]/12 text-emerald-700 dark:text-emerald-300 border-[var(--status-green)]/30',
    },
    yellow: {
        label: 'Precaución',
        color: 'var(--status-yellow)',
        soft: 'bg-[var(--status-yellow)]/12 text-amber-700 dark:text-amber-300 border-[var(--status-yellow)]/30',
    },
    red: {
        label: 'Crítico',
        color: 'var(--status-red)',
        soft: 'bg-[var(--status-red)]/12 text-red-700 dark:text-red-300 border-[var(--status-red)]/35',
    },
    none: {
        label: 'Sin datos',
        color: 'var(--status-none)',
        soft: 'bg-[var(--status-none)]/12 text-slate-600 dark:text-slate-300 border-[var(--status-none)]/30',
    },
};

/** El nombre del estado sale del idioma actual (status.green…). */
export function statusMeta(status: MaybeStatus): StatusMeta {
    const key = status ?? 'none';

    return { ...META[key], label: t(`status.${key}`) };
}

/**
 * El color del estado en hexadecimal (para WebGL/Three.js, que no entiende
 * var()). Se lee de las mismas variables --status-* de app.css.
 */
export function statusHex(status: MaybeStatus): string {
    const name = `--status-${status ?? 'none'}`;
    const value = getComputedStyle(document.documentElement)
        .getPropertyValue(name)
        .trim();

    return value || '#64748b';
}

/** Las zonas en amarillo o rojo "laten" (es un signo vital, no adorno). */
export function needsAttention(status: MaybeStatus): boolean {
    return status === 'yellow' || status === 'red';
}
