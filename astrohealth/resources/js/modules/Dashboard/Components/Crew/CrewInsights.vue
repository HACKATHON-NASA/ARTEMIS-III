<script setup lang="ts">
import { useI18n } from 'vue-i18n';
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

const { t } = useI18n();

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

/** El texto de cada periodo es una clave de i18n. */
const RANGES: { value: TrendRange; label: string }[] = [
    { value: 7, label: 'Dashboard.evolution.days7' },
    { value: 14, label: 'Dashboard.evolution.days14' },
    { value: 'mission', label: 'Dashboard.evolution.wholeMission' },
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
                {{ t('Dashboard.crew.howCrew') }}
            </h2>
            <p class="text-sm text-muted-foreground">
                {{ t('Dashboard.crew.howCrewHint') }}
            </p>
        </div>

        <div class="rounded-2xl border bg-card p-4 sm:p-6">
            <h3 class="mb-3 flex items-center gap-2 font-semibold">
                <Grid3x3 class="size-4 text-sky-500" />
                {{ t('Dashboard.crew.riskMap') }}
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
                    {{
                        t('Dashboard.evolution.dayByDay', {
                            name: indicatorMeta(indicator).label,
                        })
                    }}
                </h3>
                <div
                    class="inline-flex rounded-lg border bg-muted/40 p-0.5"
                    role="group"
                    :aria-label="t('Dashboard.evolution.period')"
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
                        {{ t(option.label) }}
                    </button>
                </div>
            </div>

            <div
                class="mb-4 flex gap-1.5 overflow-x-auto pb-1"
                role="group"
                :aria-label="t('Dashboard.crew.indicator')"
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
                :aria-label="t('Dashboard.crew.crewMembers')"
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
                :meta="indicatorMeta(indicator)"
            />
            <div
                v-else
                class="h-[300px] animate-pulse rounded-xl bg-muted/50"
            />
        </div>

        <div class="rounded-2xl border bg-card p-4 sm:p-6">
            <h3 class="mb-1 flex items-center gap-2 font-semibold">
                <Radiation class="size-4 text-sky-500" />
                {{ t('Dashboard.crew.accumulated') }}
            </h3>
            <p class="mb-4 text-sm text-muted-foreground">
                {{ t('Dashboard.crew.accumulatedHint') }}
            </p>
            <CrewDoseBars
                :rows="store.doses"
                :base-dose="baseDose"
                :environment-name="environmentName"
            />
        </div>
    </section>
</template>
