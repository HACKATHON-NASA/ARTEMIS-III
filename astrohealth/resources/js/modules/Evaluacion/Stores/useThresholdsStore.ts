import { defineStore } from 'pinia';
import { ref } from 'vue';
import { mockSaveThreshold, mockThresholds } from '@/mocks/thresholds';
import type { IndicatorCode } from '@/types/health';
import type { GuideEnvironment, Threshold } from '../Types/threshold';

/**
 * Los umbrales del semáforo por entorno (M2): la Guía de salud los muestra
 * y quien tiene guide.manage los cambia. El backend vuelve a evaluar las
 * últimas mediciones cuando cambian.
 *
 * ⚠️ HOY con datos de prueba (@/mocks/thresholds, guardados en este
 * navegador). Con el backend: GET y PUT /api/v1/thresholds.
 */
const wait = (ms = 250) => new Promise((resolve) => setTimeout(resolve, ms));

export const useThresholdsStore = defineStore('thresholds', () => {
    const environment = ref<GuideEnvironment>('mars');
    const thresholds = ref<Threshold[]>([]);

    function toList(env: GuideEnvironment): Threshold[] {
        return Object.entries(mockThresholds(env)).map(([indicator, band]) => ({
            indicator: indicator as IndicatorCode,
            green_min: band[0],
            green_max: band[1],
            yellow_min: band[2],
            yellow_max: band[3],
        }));
    }

    async function load(env: GuideEnvironment): Promise<void> {
        environment.value = env;
        await wait();
        thresholds.value = toList(env);
    }

    function find(indicator: IndicatorCode): Threshold | null {
        return (
            thresholds.value.find((item) => item.indicator === indicator) ??
            null
        );
    }

    async function save(threshold: Threshold): Promise<void> {
        await wait(400);
        mockSaveThreshold(environment.value, threshold.indicator, [
            threshold.green_min,
            threshold.green_max,
            threshold.yellow_min,
            threshold.yellow_max,
        ]);
        thresholds.value = toList(environment.value);
    }

    return { environment, thresholds, load, find, save };
});
