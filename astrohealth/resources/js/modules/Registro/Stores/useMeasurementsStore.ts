import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '@/lib/api';
import { mockRecordReadings } from '@/mocks/crewHealth';
import type {
    Indicator,
    Measurement,
    RecordPayload,
} from '../Types/measurement';

/**
 * El registro de indicadores (M1): el catálogo y lo que se acaba de guardar.
 *
 * GET /api/v1/indicators y POST /api/v1/measurements. El astronauta sale
 * del token; el backend pone el color (M2), recalcula el puntaje y avisa
 * (MeasurementsRecorded).
 *
 * ⚠️ Puente hasta F4/F6: el cuerpo, el Inicio y el Panel aún leen datos de
 * prueba, así que lo guardado también se copia ahí (con el color del backend).
 */
export const useMeasurementsStore = defineStore('measurements', () => {
    const indicators = ref<Indicator[]>([]);
    const lastRecorded = ref<Measurement[]>([]);

    async function loadIndicators(): Promise<void> {
        if (indicators.value.length) {
            return;
        }

        indicators.value = await api<Indicator[]>('/indicators');
    }

    async function record(
        astronautId: number,
        payload: RecordPayload,
    ): Promise<Measurement[]> {
        const saved = await api<Measurement[]>('/measurements', {
            method: 'POST',
            body: payload,
        });

        lastRecorded.value = saved;
        mockRecordReadings(astronautId, saved);

        return saved;
    }

    function clearLast(): void {
        lastRecorded.value = [];
    }

    return { indicators, lastRecorded, loadIndicators, record, clearLast };
});
