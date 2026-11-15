<script setup lang="ts">
import { ChartSpline, Grid3x3, Radiation } from '@lucide/vue';
import { onMounted, ref, watch } from 'vue';
import type { IndicatorCode } from '@/types/health';
import { useCrewTrendsStore } from '../../Stores/useCrewTrendsStore';
import type { CrewMemberRef } from '../../Stores/useCrewTrendsStore';
import type { TrendRange } from '../../Types/crewTrends';
import { CHART_INDICATORS, indicatorMeta } from '../../Utils/indicatorMeta';
import CrewDoseBars from './CrewDoseBars.vue';
import CrewRiskMatrix from './CrewRiskMatrix.vue';
import CrewTrendChart from './CrewTrendChart.vue';

/**
 * Cómo va la tripulación (M4): el mapa de riesgos de hoy, la comparación de
 * todos en un indicador día a día y la radiación acumulada. Cada tripulante
 * tiene su color y lo conserva en las tres gráficas.
 */
const props = defineProps<{
    members: CrewMemberRef[];
    /** Día de misión de hoy. */
    today: number;
    baseDose: number;
    environmentName: string;
}>();

const store = useCrewTrendsStore();
const indicator = ref<IndicatorCode>('sleep');
const range = ref<TrendRange>(14);
const ready = ref(false);

const RANGES: { value: TrendRange; label: string }[] = [
    { value: 7, label: '7 días' },
    { value: 14, label: '14 días' },
    { value: 'mission', label: 'Toda la misión' },
];

async function loadAll(): Promise<void> {
    ready.value = false;
    await store.load(props.members, props.today);
    await store.loadSeries(indicator.value, range.value);
    ready.value = true;
}

watch(
    [indicator, range],
    () => void store.loadSeries(indicator.value, range.value),
);
watch(() => props.members, loadAll);
onMounted(loadAll);
</script>

<template>
    <section class="flex flex-col gap-4">
        <div>
            <h2 class="text-xl font-semibold tracking-tight">
                Cómo va la tripulación
            </h2>
            <p class="text-sm text-muted-foreground">
                Compara a todos en cada riesgo. Toca un indicador del mapa para
                verlo en el tiempo.
            </p>
        </div>

        <div class="rounded-2xl border bg-card p-4 sm:p-6">
            <h3 class="mb-3 flex items-center gap-2 font-semibold">
                <Grid3x3 class="size-4 text-sky-500" />
                Mapa de riesgos de hoy
            </h3>
            <CrewRiskMatrix
                :rows="store.snapshot"
                :selected="indicator"
                @pick="indicator = $event"
            />
        </div>

        <div class="rounded-2xl border bg-card p-4 sm:p-6">
            <div
                class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
            >
                <h3 class="flex items-center gap-2 font-semibold">
                    <ChartSpline class="size-4 text-sky-500" />
                    {{ indicatorMeta(indicator).label }}, día a día
                </h3>
                <div
                    class="inline-flex rounded-lg border bg-muted/40 p-0.5"
                    role="group"
                    aria-label="Periodo"
                >
                    <button
                        v-for="option in RANGES"
                        :key="option.label"
                        type="button"
                        class="rounded-md px-3 py-1 text-xs font-medium transition"
                        :class="
                            range === option.value
                                ? 'bg-card text-foreground shadow-sm'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                        :aria-pressed="range === option.value"
                        @click="range = option.value"
                    >
                        {{ option.label }}
                    </button>
                </div>
            </div>

            <div
                class="mb-4 flex gap-1.5 overflow-x-auto pb-1"
                role="group"
                aria-label="Indicador"
            >
                <button
                    v-for="item in CHART_INDICATORS"
                    :key="item.code"
                    type="button"
                    class="shrink-0 rounded-full border px-3 py-1 text-xs font-medium transition"
                    :class="
                        indicator === item.code
                            ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    :aria-pressed="indicator === item.code"
                    @click="indicator = item.code"
                >
                    {{ item.short }}
                </button>
            </div>

            <ul
                class="mb-2 flex flex-wrap gap-x-4 gap-y-1 text-xs"
                aria-label="Tripulantes"
            >
                <li
                    v-for="line in store.series"
                    :key="line.astronaut_id"
                    class="flex items-center gap-1.5"
                >
                    <span
                        class="h-0.5 w-4 rounded-full"
                        :style="{ background: line.color }"
                    />
                    {{ line.name }}
                </li>
            </ul>

            <CrewTrendChart
                v-if="ready"
                :series="store.series"
                :bands="store.bands"
                :indicator="indicator"
            />
            <div
                v-else
                class="h-[300px] animate-pulse rounded-xl bg-muted/50"
            />
        </div>

        <div class="rounded-2xl border bg-card p-4 sm:p-6">
            <h3 class="mb-1 flex items-center gap-2 font-semibold">
                <Radiation class="size-4 text-sky-500" />
                Radiación acumulada en la misión
            </h3>
            <p class="mb-4 text-sm text-muted-foreground">
                La suma de las dosis diarias de cada uno, frente a lo esperado
                para el entorno.
            </p>
            <CrewDoseBars
                :rows="store.doses"
                :base-dose="baseDose"
                :environment-name="environmentName"
            />
        </div>
    </section>
</template>
