import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '@/lib/api';
import type { IndicatorCode, Status } from '@/types/health';
import type {
    Bands,
    CrewSeries,
    CrewSnapshotRow,
    DoseRow,
    HealthChange,
    TrendPoint,
    TrendRange,
} from '../Types/crewTrends';
import { seriesColor } from '../Utils/indicatorMeta';

/**
 * La comparación de la tripulación de una misión (M4): el mapa de riesgos
 * (último valor de cada indicador), la serie de un indicador para todos, la
 * dosis de radiación acumulada y lo que cambió de color.
 *
 * GET /api/v1/crew/overview?astronauts= y GET /api/v1/crew/series. El
 * backend solo devuelve a quien la persona puede ver; aquí se ponen los
 * nombres y los colores de cada tripulante (presentación).
 */
export type CrewMemberRef = { id: number; name: string };

type Overview = {
    snapshot: {
        astronaut_id: number;
        values: CrewSnapshotRow['values'];
        attention: number;
    }[];
    doses: { astronaut_id: number; total: number; days: number }[];
    changes: {
        astronaut_id: number;
        indicator: IndicatorCode;
        from: { value: number; status: Status };
        to: { value: number; status: Status };
        worse: boolean;
    }[];
};

type SeriesResponse = {
    bands: Bands | null;
    series: { astronaut_id: number; points: TrendPoint[] }[];
};

export const useCrewTrendsStore = defineStore('crewTrends', () => {
    const snapshot = ref<CrewSnapshotRow[]>([]);
    const series = ref<CrewSeries[]>([]);
    const bands = ref<Bands | null>(null);
    const doses = ref<DoseRow[]>([]);
    const changes = ref<HealthChange[]>([]);

    let crew: CrewMemberRef[] = [];

    function ids(): string {
        return crew.map((member) => member.id).join(',');
    }

    function nameOf(id: number): string {
        return crew.find((member) => member.id === id)?.name ?? 'Tripulante';
    }

    function colorOf(id: number): string {
        return seriesColor(
            Math.max(
                0,
                crew.findIndex((member) => member.id === id),
            ),
        );
    }

    /** Fija la tripulación y trae el mapa, las dosis y los cambios. */
    async function load(members: CrewMemberRef[], _day: number): Promise<void> {
        crew = members;

        if (crew.length === 0) {
            snapshot.value = [];
            doses.value = [];
            changes.value = [];

            return;
        }

        const overview = await api<Overview>(
            `/crew/overview?astronauts=${ids()}`,
        );

        snapshot.value = overview.snapshot.map((row) => ({
            ...row,
            name: nameOf(row.astronaut_id),
            color: colorOf(row.astronaut_id),
        }));
        doses.value = overview.doses.map((row) => ({
            ...row,
            name: nameOf(row.astronaut_id),
            color: colorOf(row.astronaut_id),
        }));
        changes.value = overview.changes.map((row) => ({
            ...row,
            name: nameOf(row.astronaut_id),
        }));
    }

    async function loadSeries(
        indicator: IndicatorCode,
        range: TrendRange,
    ): Promise<void> {
        if (crew.length === 0) {
            series.value = [];

            return;
        }

        const result = await api<SeriesResponse>(
            `/crew/series?astronauts=${ids()}&indicator=${indicator}&range=${range}`,
        );
        bands.value = result.bands;
        series.value = result.series.map((row) => ({
            ...row,
            name: nameOf(row.astronaut_id),
            color: colorOf(row.astronaut_id),
        }));
    }

    return { snapshot, series, bands, doses, changes, load, loadSeries };
});
