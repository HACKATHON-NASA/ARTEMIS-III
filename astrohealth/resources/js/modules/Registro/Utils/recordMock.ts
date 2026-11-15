import { mockRecordReadings } from '@/mocks/crewHealth';
import { mockEvaluate as evaluate } from '@/mocks/thresholds';
import type { Measurement, RecordPayload } from '../Types/measurement';
import { MOCK_INDICATORS } from './indicatorsMockData';

/**
 * ⚠️ DATOS DE PRUEBA: imita POST /api/v1/measurements mientras no exista el
 * backend. El color lo pone el BACKEND (M2 Evaluator); esto lo copia solo
 * para la demo, con los umbrales de @/mocks/thresholds (ficha M2).
 * Guarda los valores en @/mocks/crewHealth para que el cuerpo, el Inicio y
 * el Panel de salud muestren lo registrado. Se borra con el backend de M1.
 */

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
