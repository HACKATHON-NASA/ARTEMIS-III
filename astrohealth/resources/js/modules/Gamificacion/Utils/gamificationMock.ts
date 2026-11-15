import { mockSeries } from '@/mocks/crewHistory';
import type { PointEntry, PointReason } from '../Types/gamification';
import { POINTS } from './rules';

/**
 * ⚠️ DATOS DE PRUEBA de M7: el points_log de cada tripulante. Lo de días
 * pasados sale de su historia real de la misión (@/mocks/crewHistory: si
 * durmió 7 h, si hizo 90 min de ejercicio); lo de hoy se guarda en
 * localStorage al registrar, cumplir recordatorios o hacer tests.
 * Se borra con el backend de M7.
 */
const KEY = 'astrohealth.demo.points.v1';
const DAY_MS = 86_400_000;

/** Días en que no completó el registro (para que las rachas no sean perfectas). */
const MISSED: Record<number, number[]> = { 2: [9, 19], 3: [21] };
/** Días en que hizo los 4 tests (Elena aún no hace los de esta semana). */
const PSYCH_DAYS: Record<number, number[]> = {
    1: [3, 10, 17],
    2: [3, 10, 17, 24],
    3: [3, 10, 17, 24],
    4: [3, 10, 17, 24],
};

export function entry(
    astronautId: number,
    reason: PointReason,
    reference: string,
    day: number,
    today: number,
    points = reason === 'badge' ? 0 : POINTS[reason],
): PointEntry {
    return {
        astronaut_id: astronautId,
        points,
        reason,
        reference,
        day,
        created_at: new Date(Date.now() - (today - day) * DAY_MS).toISOString(),
    };
}

/** Lo ganado en días pasados (y lo de hoy que ya venía hecho en la demo). */
function history(astronautId: number, today: number): PointEntry[] {
    if (!PSYCH_DAYS[astronautId]) {
        return [];
    }

    const sleep = mockSeries(astronautId, 'sleep', today);
    const exercise = mockSeries(astronautId, 'exercise_min', today);
    const missed = MISSED[astronautId] ?? [];
    const log: PointEntry[] = [];
    let run = 0;

    for (let day = 1; day < today; day++) {
        const add = (reason: PointReason, reference = `${day}`) =>
            log.push(entry(astronautId, reason, reference, day, today));

        if (missed.includes(day)) {
            run = 0;
        } else {
            add('daily_complete');
            run++;

            if (run % 7 === 0) {
                add('streak_7');
            }
        }

        if ((sleep[day - 1]?.value ?? 0) >= 7) add('sleep_7h');
        if ((exercise[day - 1]?.value ?? 0) >= 90) add('exercise');

        for (let k = 0; k < 3 + ((day * astronautId) % 3); k++) {
            add('reminder', `r${k}-${day}`);
        }
    }

    for (const day of PSYCH_DAYS[astronautId]) {
        for (let test = 1; test <= 4; test++) {
            log.push(
                entry(astronautId, 'psych_test', `${test}-${day}`, day, today),
            );
        }
    }

    // Hoy: Elena ya midió la presión (recordatorio 11); los demás, todo.
    if (astronautId === 1) {
        log.push(entry(1, 'reminder', `11-${today}`, today, today));
    } else {
        log.push(
            entry(astronautId, 'daily_complete', `${today}`, today, today),
        );
        log.push(entry(astronautId, 'reminder', `r0-${today}`, today, today));
    }

    return log;
}

function readLive(): PointEntry[] {
    try {
        return JSON.parse(localStorage.getItem(KEY) ?? '[]') as PointEntry[];
    } catch {
        return [];
    }
}

const same = (a: PointEntry, b: PointEntry) =>
    a.astronaut_id === b.astronaut_id &&
    a.reason === b.reason &&
    a.reference === b.reference;

/** El points_log completo de un tripulante (sin repetidos, como la llave única). */
export function mockPointsLog(
    astronautId: number,
    today: number,
): PointEntry[] {
    const all = [
        ...history(astronautId, today),
        ...readLive().filter((item) => item.astronaut_id === astronautId),
    ];

    return all.filter(
        (item, index) => all.findIndex((other) => same(item, other)) === index,
    );
}

/** Suma puntos si no existían (insertOrIgnore). Devuelve si se sumó. */
export function mockAward(item: PointEntry, today: number): boolean {
    if (
        mockPointsLog(item.astronaut_id, today).some((other) =>
            same(item, other),
        )
    ) {
        return false;
    }

    try {
        localStorage.setItem(KEY, JSON.stringify([...readLive(), item]));
    } catch {
        return false;
    }

    return true;
}
