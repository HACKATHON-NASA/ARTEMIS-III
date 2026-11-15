import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { mockSeries } from '@/mocks/crewHistory';
import { mockThresholds } from '@/mocks/thresholds';
import type { IndicatorCode } from '@/types/health';
import type { Bands, TrendPoint } from '../Types/crewTrends';
import { CHART_INDICATORS } from '../Utils/indicatorMeta';
import { trendOf } from '../Utils/trends';
import type { Trend } from '../Utils/trends';

/**
 * Mi evolución (M4): la historia de cada indicador del astronauta en la
 * misión, su tendencia y las bandas del semáforo.
 *
 * ⚠️ HOY con datos de prueba (@/mocks/crewHistory). Con el backend:
 * GET /api/v1/astronauts/{id}/measurements?indicator=&range= y
 * GET /api/v1/astronauts/{id}/trends.
 */
export type IndicatorHistory = {
    code: IndicatorCode;
    points: TrendPoint[];
    trend: Trend | null;
    bands: Bands;
};

export const useMyEvolutionStore = defineStore('myEvolution', () => {
    const histories = ref<IndicatorHistory[]>([]);
    const today = ref(1);

    /** Lo que viene empeorando esta semana (para los avisos de arriba). */
    const worsening = computed(() =>
        histories.value.filter((item) => item.trend?.worse),
    );

    async function load(astronautId: number, day: number): Promise<void> {
        today.value = day;
        await new Promise((resolve) => setTimeout(resolve, 250));
        const thresholds = mockThresholds();

        histories.value = CHART_INDICATORS.map(({ code }) => {
            const points = mockSeries(astronautId, code, day);
            const [green_min, green_max, yellow_min, yellow_max] =
                thresholds[code];

            return {
                code,
                points,
                trend: trendOf(code, points),
                bands: { green_min, green_max, yellow_min, yellow_max },
            };
        });
    }

    function find(code: IndicatorCode): IndicatorHistory | null {
        return histories.value.find((item) => item.code === code) ?? null;
    }

    return { histories, today, worsening, load, find };
});
