import { mockRecordReadings } from '@/mocks/crewHealth';
import type { IndicatorCode, Status } from '@/types/health';
import type { Measurement, RecordPayload } from '../Types/measurement';
import { MOCK_INDICATORS } from './indicatorsMockData';

/**
 * ⚠️ DATOS DE PRUEBA: imita POST /api/v1/measurements mientras no exista el
 * backend. El color lo pone el BACKEND (M2 Evaluator); esto lo copia solo
 * para la demo, con los umbrales de la ficha M2 (Marte, radiación base 0.6).
 * Guarda los valores en @/mocks/crewHealth para que el cuerpo, el Inicio y
 * el Panel de salud muestren lo registrado. Se borra con el backend de M1.
 */

/** [verde mín, verde máx, amarillo mín, amarillo máx] */
const THRESHOLDS: Record<IndicatorCode, [number, number, number, number]> = {
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

function evaluate(indicator: IndicatorCode, value: number): Status {
    const [greenMin, greenMax, yellowMin, yellowMax] = THRESHOLDS[indicator];

    if (value >= greenMin && value <= greenMax) {
        return 'green';
    }

    return value >= yellowMin && value <= yellowMax ? 'yellow' : 'red';
}

let nextId = 901;

export function mockRecord(
    astronautId: number,
    payload: RecordPayload,
): Measurement[] {
    const measuredAt = payload.measured_at ?? new Date().toISOString();

    const saved = payload.measurements.map(({ indicator, value }) => {
        const meta = MOCK_INDICATORS.find((item) => item.code === indicator);

        return {
            id: nextId++,
            indicator,
            value,
            unit: meta?.unit ?? '',
            status: evaluate(indicator, value),
            body_part: meta?.body_part ?? 'whole_body',
            measured_at: measuredAt,
            source: 'manual',
        } satisfies Measurement;
    });

    mockRecordReadings(astronautId, saved);

    return saved;
}
