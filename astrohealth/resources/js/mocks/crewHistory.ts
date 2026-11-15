import type { IndicatorCode, Status } from '@/types/health';
import { mockBodyStatusFor } from './crewHealth';
import { mockEvaluate, mockThresholds } from './thresholds';

/**
 * ⚠️ DATOS DE PRUEBA: el historial diario de cada tripulante (un valor por
 * día de misión), para las gráficas de la tripulación (M4). Siempre termina
 * en el último valor de @/mocks/crewHealth (incluido lo registrado en M1),
 * así las gráficas y el cuerpo cuentan la misma historia. Es determinista
 * (misma semilla → mismos datos). Se borra con GET .../measurements (M4).
 */

export type MockPoint = { day: number; value: number; status: Status };

/** Cuánto se mueve cada indicador de un día a otro, y sus decimales. */
const NOISE: Record<IndicatorCode, [number, number]> = {
    hr: [4, 0],
    bp_sys: [4, 0],
    bp_dia: [3, 0],
    glucose: [4, 0],
    temp: [0.15, 1],
    spo2: [0.6, 0],
    sleep: [0.35, 1],
    mood: [0.45, 0],
    exercise_min: [10, 0],
    radiation: [0.05, 2],
};

/**
 * Las historias de Ares-1 (coherentes con sus alertas):
 * [valor de partida, en cuántos días llegó al valor de hoy].
 */
const STORIES: Record<
    number,
    Partial<Record<IndicatorCode, [number, number]>>
> = {
    1: {
        bp_sys: [117, 6],
        bp_dia: [75, 6],
        sleep: [7.5, 10],
        exercise_min: [105, 8],
    },
    2: { sleep: [7.3, 14], mood: [4.4, 9] },
    3: { spo2: [98, 5], glucose: [91, 10], radiation: [0.6, 6] },
};

/** Una tormenta solar golpea a toda la nave unos días antes de hoy. */
const SOLAR_EVENT_BOOST = 0.35;

function random(seed: number): number {
    let t = (seed + 0x6d2b79f5) | 0;
    t = Math.imul(t ^ (t >>> 15), t | 1);
    t ^= t + Math.imul(t ^ (t >>> 7), t | 61);

    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
}

function latestValue(
    astronautId: number,
    indicator: IndicatorCode,
): number | null {
    const zones = Object.values(mockBodyStatusFor(astronautId));
    const found = zones
        .flatMap((zone) => zone.values)
        .find((value) => value.indicator === indicator);

    return found?.value ?? null;
}

/**
 * La serie de un indicador, del día `from` al `today` de la misión.
 * Sin datos del tripulante, la serie sale vacía.
 */
export function mockSeries(
    astronautId: number,
    indicator: IndicatorCode,
    today: number,
    from = 1,
): MockPoint[] {
    const end = latestValue(astronautId, indicator);

    if (end === null || today < 1) {
        return [];
    }

    const [amplitude, decimals] = NOISE[indicator];
    const [start, span] = STORIES[astronautId]?.[indicator] ?? [end, 1];
    const [, , low, high] = mockThresholds()[indicator];
    const factor = 10 ** decimals;
    const code = Object.keys(NOISE).indexOf(indicator);
    const points: MockPoint[] = [];

    for (let day = Math.max(1, from); day <= today; day++) {
        const progress = Math.min(
            1,
            Math.max(0, (day - (today - span)) / span),
        );
        let value = start + (end - start) * progress;

        if (day < today) {
            value +=
                (random(astronautId * 1000 + code * 37 + day) - 0.5) *
                2 *
                amplitude;

            if (
                indicator === 'radiation' &&
                day >= today - 4 &&
                day <= today - 2
            ) {
                value += SOLAR_EVENT_BOOST;
            }
        }

        value = Math.round(value * factor) / factor;
        value = indicator === 'mood' ? Math.min(5, Math.max(1, value)) : value;
        value = indicator === 'spo2' ? Math.min(100, value) : value;
        value = Math.max(
            indicator === 'radiation' ? 0.05 : low * 0.5,
            Math.min(high * 1.5, value),
        );

        points.push({ day, value, status: mockEvaluate(indicator, value) });
    }

    return points;
}
