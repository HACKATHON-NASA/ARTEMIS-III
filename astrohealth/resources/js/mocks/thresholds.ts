import type { IndicatorCode, Status } from '@/types/health';

/**
 * ⚠️ DATOS DE PRUEBA: los umbrales de la ficha M2 (Marte, radiación base
 * 0.6 mSv/día), para que los mocks pongan el color como lo hará el BACKEND
 * (M2 Evaluator). El frontend real nunca evalúa: se borra con el backend.
 */

/** [verde mín, verde máx, amarillo mín, amarillo máx] */
export const MOCK_THRESHOLDS: Record<
    IndicatorCode,
    [number, number, number, number]
> = {
    hr: [60, 100, 50, 120],
    bp_sys: [90, 119.99, 90, 139.99],
    bp_dia: [60, 79.99, 60, 89.99],
    glucose: [70, 99.99, 60, 125.99],
    temp: [36.1, 37.2, 35.5, 38],
    spo2: [95, 100, 90, 100],
    sleep: [7, 9, 6, 24],
    mood: [4, 5, 3, 5],
    exercise_min: [90, 600, 60, 600],
    radiation: [0, 0.6, 0, 1.2],
};

export function mockEvaluate(indicator: IndicatorCode, value: number): Status {
    const [greenMin, greenMax, yellowMin, yellowMax] =
        MOCK_THRESHOLDS[indicator];

    if (value >= greenMin && value <= greenMax) {
        return 'green';
    }

    return value >= yellowMin && value <= yellowMax ? 'yellow' : 'red';
}
