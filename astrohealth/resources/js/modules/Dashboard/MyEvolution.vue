<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, evolucion } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { IndicatorCode } from '@/types/health';
import { useMissionStore } from '../Entorno/Stores/useMissionStore';
import MyTestsSummary from '../Evaluacion/Components/Psych/MyTestsSummary.vue';
import { usePsychStore } from '../Evaluacion/Stores/usePsychStore';
import CrewTrendChart from './Components/Crew/CrewTrendChart.vue';
import IndicatorTrendCard from './Components/Evolution/IndicatorTrendCard.vue';
import TrendBanner from './Components/Evolution/TrendBanner.vue';
import { useMyEvolutionStore } from './Stores/useMyEvolutionStore';
import type { CrewSeries, TrendRange } from './Types/crewTrends';
import { indicatorMeta } from './Utils/indicatorMeta';

const { t } = useI18n();

/**
 * Mi evolución (/evolucion, M4): la historia del astronauta en la misión.
 * Avisa lo que viene empeorando, muestra cada indicador con su flecha y su
 * mini gráfica, abre el elegido en grande y resume sus tests (M2).
 * ⚠️ Por ahora con datos de prueba (ver los stores).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'nav.myEvolution', href: evolucion() }] },
});

const auth = useAuthStore();
const store = useMyEvolutionStore();
const psych = usePsychStore();
const mission = useMissionStore();

const root = ref<HTMLElement | null>(null);
const chart = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const selected = ref<IndicatorCode>('sleep');
const range = ref<TrendRange>(14);
const own = computed(() => auth.actor?.astronaut_id ?? 0);

/** El texto de cada periodo es una clave de i18n. */
const RANGES: { value: TrendRange; label: string }[] = [
    { value: 7, label: 'Dashboard.evolution.days7' },
    { value: 14, label: 'Dashboard.evolution.days14' },
    { value: 'mission', label: 'Dashboard.evolution.wholeMission' },
];

const current = computed(() => store.find(selected.value));

/** La gráfica grande: una sola línea (la mía) en el periodo elegido. */
const series = computed<CrewSeries[]>(() => {
    const points = current.value?.points ?? [];
    const from = range.value === 'mission' ? 0 : store.today - range.value;

    return [
        {
            astronaut_id: own.value,
            name: 'Tú',
            color: 'var(--series-1)',
            points: points.filter((p) => p.day > from),
        },
    ];
});

function open(code: IndicatorCode): void {
    selected.value = code;
    chart.value?.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

onMounted(async () => {
    if (!auth.actor?.astronaut_id) {
        toast.error('Mi evolución es para la tripulación.');
        router.visit(dashboard());

        return;
    }

    await mission.load();
    await Promise.all([
        store.load(own.value, mission.current?.day ?? 1),
        psych.load(),
    ]);
    selected.value = store.worsening[0]?.code ?? 'sleep';
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head :title="t('Dashboard.evolution.pageTitle')" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header data-reveal>
            <p
                class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
            >
                {{
                    mission.current
                        ? t('Dashboard.evolution.missionDay', {
                              mission: mission.current.name,
                              day: mission.current.day,
                              total: mission.current.day_count,
                          })
                        : t('Dashboard.evolution.myHealth')
                }}
            </p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
                {{ t('Dashboard.evolution.title') }}
            </h1>
            <p class="mt-2 max-w-2xl text-muted-foreground">
                {{ t('Dashboard.evolution.subtitle') }}
            </p>
        </header>

        <template v-if="!ready">
            <Skeleton class="h-20 rounded-2xl" />
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                <Skeleton v-for="n in 10" :key="n" class="h-36 rounded-2xl" />
            </div>
        </template>

        <template v-else>
            <div data-reveal>
                <TrendBanner :items="store.worsening" @select="open" />
            </div>

            <section
                class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5"
                data-reveal
            >
                <IndicatorTrendCard
                    v-for="history in store.histories"
                    :key="history.code"
                    :history="history"
                    :selected="history.code === selected"
                    @select="open(history.code)"
                />
            </section>

            <section
                ref="chart"
                class="rounded-2xl border bg-card p-4 sm:p-6"
                data-reveal
            >
                <div
                    class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <h2 class="font-semibold">
                        {{
                            t('Dashboard.evolution.dayByDay', {
                                name: indicatorMeta(selected).label,
                            })
                        }}
                    </h2>
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
                <CrewTrendChart
                    :series="series"
                    :bands="current?.bands ?? null"
                    :meta="indicatorMeta(selected)"
                />
            </section>

            <section class="flex flex-col gap-3" data-reveal>
                <h2 class="text-lg font-semibold">
                    {{ t('Dashboard.evolution.myTests') }}
                </h2>
                <MyTestsSummary
                    :tests="psych.tests"
                    :history="(testId) => psych.history(own, testId)"
                    :is-due="
                        (test) =>
                            psych.isDue(own, test, mission.current?.day ?? 1)
                    "
                />
            </section>
        </template>
    </div>
</template>
