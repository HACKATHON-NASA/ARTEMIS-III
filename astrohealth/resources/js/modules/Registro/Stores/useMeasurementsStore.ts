import { defineStore } from 'pinia';
import { ref } from 'vue';
import type {
    Indicator,
    Measurement,
    RecordPayload,
} from '../Types/measurement';
import { MOCK_INDICATORS } from '../Utils/indicatorsMockData';
import { mockRecord } from '../Utils/recordMock';

/**
 * El registro de indicadores (M1): el catálogo y lo que se acaba de guardar.
 *
 * ⚠️ HOY con datos de prueba. Cuando existan GET /api/v1/indicators y
 * POST /api/v1/measurements, loadIndicators() y record() pasan a usar
 * `api()` (el astronauta sale del token); los componentes no cambian.
 */
const wait = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

export const useMeasurementsStore = defineStore('measurements', () => {
    const indicators = ref<Indicator[]>([]);
    const lastRecorded = ref<Measurement[]>([]);

    async function loadIndicators(): Promise<void> {
        if (indicators.value.length) {
            return;
        }

        await wait(200);
        indicators.value = [...MOCK_INDICATORS].sort(
            (a, b) => a.sort_order - b.sort_order,
        );
    }

    async function record(
        astronautId: number,
        payload: RecordPayload,
    ): Promise<Measurement[]> {
        await wait(600);
        lastRecorded.value = mockRecord(astronautId, payload);

        return lastRecorded.value;
    }

    function clearLast(): void {
        lastRecorded.value = [];
    }

    return { indicators, lastRecorded, loadIndicators, record, clearLast };
});
