import { defineStore } from 'pinia';
import { ref } from 'vue';
import { mockSeries } from '@/mocks/crewHistory';
import { mockThresholds } from '@/mocks/thresholds';
import type { IndicatorCode } from '@/types/health';
import type {
    Bands,
    CrewSeries,
    CrewSnapshotRow,
    DoseRow,
    HealthChange,
    TrendRange,
} from '../Types/crewTrends';
import { CHART_INDICATORS, seriesColor } from '../Utils/indicatorMeta';

/**
 * La comparación de la tripulación de una misión (M4): el mapa de riesgos
 * (último valor de cada indicador), la serie de un indicador para todos y la
 * dosis de radiación acumulada.
 *
 * ⚠️ HOY con datos de prueba (@/mocks/crewHistory). Con el backend:
 * GET /api/v1/missions/{id}/crew-measurements?indicator=&range=.
 */
export type CrewMemberRef = { id: number; name: string };

const wait = (ms = 250) => new Promise((resolve) => setTimeout(resolve, ms));

export const useCrewTrendsStore = defineStore('crewTrends', () => {
    const snapshot = ref<CrewSnapshotRow[]>([]);
    const series = ref<CrewSeries[]>([]);
    const bands = ref<Bands | null>(null);
    const doses = ref<DoseRow[]>([]);
    const changes = ref<HealthChange[]>([]);

    let crew: CrewMemberRef[] = [];
    let today = 0;

    function fromDay(range: TrendRange): number {
        return range === 'mission' ? 1 : Math.max(1, today - range + 1);
    }

    /** Fija la tripulación y el día de misión, y calcula el mapa y las dosis. */
    async function load(members: CrewMemberRef[], day: number): Promise<void> {
        crew = members;
        today = day;
        await wait();

        snapshot.value = crew.map((member, index) => {
            const values: CrewSnapshotRow['values'] = {};

            for (const { code } of CHART_INDICATORS) {
                const last = mockSeries(member.id, code, today, today).at(-1);

                if (last) {
                    values[code] = { value: last.value, status: last.status };
                }
            }

            return {
                astronaut_id: member.id,
                name: member.name,
                color: seriesColor(index),
                values,
                attention: Object.values(values).filter(
                    (item) => item.status !== 'green',
                ).length,
            };
        });

        doses.value = crew.map((member, index) => {
            const points = mockSeries(member.id, 'radiation', today);

            return {
                astronaut_id: member.id,
                name: member.name,
                color: seriesColor(index),
                total: points.reduce((sum, point) => sum + point.value, 0),
                days: points.length,
            };
        });

        changes.value = computeChanges();
    }

    /** Lo que cambió de color en los últimos `days` días: lo que empeoró, primero. */
    function computeChanges(days = 3): HealthChange[] {
        const rank = { green: 0, yellow: 1, red: 2 } as const;
        const found: HealthChange[] = [];

        for (const member of crew) {
            for (const { code } of CHART_INDICATORS) {
                const points = mockSeries(member.id, code, today, today - days);
                const [first, last] = [points[0], points.at(-1)];

                if (first && last && first.status !== last.status) {
                    found.push({
                        astronaut_id: member.id,
                        name: member.name,
                        indicator: code,
                        from: { value: first.value, status: first.status },
                        to: { value: last.value, status: last.status },
                        worse: rank[last.status] > rank[first.status],
                    });
                }
            }
        }

        return found.sort(
            (a, b) =>
                Number(b.worse) - Number(a.worse) ||
                rank[b.to.status] - rank[a.to.status],
        );
    }

    async function loadSeries(
        indicator: IndicatorCode,
        range: TrendRange,
    ): Promise<void> {
        await wait(150);
        const [green_min, green_max, yellow_min, yellow_max] =
            mockThresholds()[indicator];
        bands.value = { green_min, green_max, yellow_min, yellow_max };
        series.value = crew.map((member, index) => ({
            astronaut_id: member.id,
            name: member.name,
            color: seriesColor(index),
            points: mockSeries(member.id, indicator, today, fromDay(range)),
        }));
    }

    return { snapshot, series, bands, doses, changes, load, loadSeries };
});
