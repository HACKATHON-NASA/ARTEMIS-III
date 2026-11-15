import { currentLocale, t } from '@/lib/i18n';
import type { MissionStatus } from '../Types/missions';

/**
 * Las fechas de una misión. Trabajan con fechas locales "YYYY-MM-DD" (sin
 * hora), así un día de misión no cambia por la zona horaria.
 * Es la misma regla que Domain/Reglas/MissionDay.php del backend.
 */
const DAY_MS = 24 * 60 * 60 * 1000;

function toDate(value: string): Date {
    const [year, month, day] = value.split('-').map(Number);

    return new Date(year, month - 1, day);
}

export function toIsoDate(date: Date): string {
    const pad = (value: number) => value.toString().padStart(2, '0');

    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`;
}

export function today(): string {
    return toIsoDate(new Date());
}

/** Una fecha movida `days` días (negativo = hacia atrás). */
export function shiftDate(value: string, days: number): string {
    const date = toDate(value);
    date.setDate(date.getDate() + days);

    return toIsoDate(date);
}

/** El último día de la misión (incluido). */
export function endDate(start: string, dayCount: number): string {
    return shiftDate(start, dayCount - 1);
}

export function missionStatus(
    start: string,
    dayCount: number,
    on: string = today(),
): { status: MissionStatus; currentDay: number | null } {
    const elapsed = Math.round(
        (toDate(on).getTime() - toDate(start).getTime()) / DAY_MS,
    );

    if (elapsed < 0) {
        return { status: 'planned', currentDay: null };
    }

    if (elapsed >= dayCount) {
        return { status: 'finished', currentDay: null };
    }

    return { status: 'active', currentDay: elapsed + 1 };
}

/** Cuántos días faltan para empezar (para las planificadas). */
export function daysUntil(start: string, on: string = today()): number {
    return Math.round(
        (toDate(start).getTime() - toDate(on).getTime()) / DAY_MS,
    );
}

export function formatDate(value: string): string {
    return toDate(value).toLocaleDateString(currentLocale(), {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
}

/** "Planificada" · "In progress"… en el idioma de ahora. */
export function statusLabel(status: MissionStatus): string {
    return t(`Entorno.status.${status}`);
}
