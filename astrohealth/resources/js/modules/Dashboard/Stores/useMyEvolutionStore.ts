import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api } from '@/lib/api';
import type { IndicatorCode } from '@/types/health';
import type { Bands, Trend, TrendPoint } from '../Types/crewTrends';
import { CHART_INDICATORS } from '../Utils/indicatorMeta';

/**
 * Mi evolución (M4): la historia de cada indicador del astronauta en la
 * misión (un punto por día), su tendencia de la semana y las bandas del
 * semáforo.
 *
 * GET /api/v1/astronauts/{id}/evolution. La tendencia y el color los
 * decide el backend (M2); aquí solo se ordenan para las gráficas.
 */
export type IndicatorHistory = {
    code: IndicatorCode;
    points: TrendPoint[];
    trend: Trend | null;
    bands: Bands;
};

type EvolutionItem = Omit<IndicatorHistory, 'bands'> & { bands: Bands | null };

const NO_BANDS: Bands = {
    green_min: 0,
    green_max: 0,
    yellow_min: 0,
    yellow_max: 0,
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
        const items = await api<EvolutionItem[]>(
            `/astronauts/${astronautId}/evolution`,
        );

        histories.value = CHART_INDICATORS.map(({ code }) => {
            const item = items.find((entry) => entry.code === code);

            return {
                code,
                points: item?.points ?? [],
                trend: item?.trend ?? null,
                bands: item?.bands ?? NO_BANDS,
            };
        });
    }

    function find(code: IndicatorCode): IndicatorHistory | null {
        return histories.value.find((item) => item.code === code) ?? null;
    }

    return { histories, today, worsening, load, find };
});
