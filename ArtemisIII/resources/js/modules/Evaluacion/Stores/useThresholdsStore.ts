import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '@/lib/api';
import { primeThresholds } from '@/mocks/thresholds';
import type { IndicatorCode } from '@/types/health';
import type { GuideEnvironment, Threshold } from '../Types/threshold';

/**
 * Los umbrales del semáforo por entorno (M2): la Guía de salud los muestra
 * y quien tiene guide.manage los cambia.
 *
 * GET /api/v1/thresholds?environment= y PUT /api/v1/thresholds/{indicator}
 * ?environment=. El backend valida que el verde quede dentro del amarillo y
 * avisa (ThresholdUpdated) para volver a evaluar las mediciones.
 */
export const useThresholdsStore = defineStore('thresholds', () => {
    const environment = ref<GuideEnvironment>('mars');
    const thresholds = ref<Threshold[]>([]);

    async function load(env: GuideEnvironment): Promise<void> {
        environment.value = env;
        const list = await api<Threshold[]>(`/thresholds?environment=${env}`);

        // Si mientras cargaba se eligió otro entorno, esta respuesta ya no sirve.
        if (environment.value === env) {
            thresholds.value = list;
        }

        primeThresholds(env, list);
    }

    function find(indicator: IndicatorCode): Threshold | null {
        return (
            thresholds.value.find((item) => item.indicator === indicator) ??
            null
        );
    }

    async function save(threshold: Threshold): Promise<void> {
        const env = environment.value;
        const saved = await api<Threshold>(
            `/thresholds/${threshold.indicator}?environment=${env}`,
            {
                method: 'PUT',
                body: {
                    green_min: threshold.green_min,
                    green_max: threshold.green_max,
                    yellow_min: threshold.yellow_min,
                    yellow_max: threshold.yellow_max,
                },
            },
        );

        thresholds.value = thresholds.value.map((item) =>
            item.indicator === saved.indicator ? saved : item,
        );
        primeThresholds(env, thresholds.value);
    }

    return { environment, thresholds, load, find, save };
});
