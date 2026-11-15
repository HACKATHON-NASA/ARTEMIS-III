<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Activity, ClipboardCheck, Siren, TriangleAlert } from '@lucide/vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, psicologiaTripulacion } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import CrewTrendChart from '../Dashboard/Components/Crew/CrewTrendChart.vue';
import SummaryStat from '../Dashboard/Components/Panel/SummaryStat.vue';
import type { Bands, CrewSeries } from '../Dashboard/Types/crewTrends';
import { seriesColor } from '../Dashboard/Utils/indicatorMeta';
import MissionPicker from '../Entorno/Components/MissionPicker.vue';
import MissionStrip from '../Entorno/Components/MissionStrip.vue';
import NoCrew from '../Entorno/Components/NoCrew.vue';
import { useCrewStore } from '../Entorno/Stores/useCrewStore';
import GuideTabs from './Components/GuideTabs.vue';
import PsychMatrix from './Components/Psych/PsychMatrix.vue';
import { usePsychStore } from './Stores/usePsychStore';

/**
 * Resultados de la tripulación (/guia/resultados, M2): cómo va cada uno en lo
 * psicológico (ánimo, ansiedad, estrés) y en lo intelectual (atención y
 * reacción), y cómo ha cambiado en la misión. Solo health.read.any.
 * ⚠️ Por ahora con datos de prueba guardados en este navegador.
 */
defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Resultados de la tripulación',
                href: psicologiaTripulacion(),
            },
        ],
    },
});

const auth = useAuthStore();
const psych = usePsychStore();
const crew = useCrewStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const selected = ref<number | null>(1);

const missionId = computed({
    get: () => crew.missionId,
    set: (id) => id !== null && crew.selectMission(id),
});

const today = computed(() => crew.mission?.current_day ?? 1);
const test = computed(() => psych.find(selected.value ?? 0));

/** Lo último de cada tripulante en cada test. */
const latest = computed(() =>
    crew.members.flatMap((member) =>
        psych.tests.map((item) => ({
            member,
            test: item,
            result: psych.latest(member.id, item.id),
        })),
    ),
);

const stats = computed(() => {
    const done = latest.value.filter(
        ({ result, test: item }) =>
            result && today.value - result.day < item.frequency_days,
    );

    return {
        done: done.length,
        expected: latest.value.length,
        yellow: latest.value.filter(({ result }) => result?.status === 'yellow')
            .length,
        red: latest.value.filter(({ result }) => result?.status === 'red')
            .length,
        flagged: latest.value.filter(({ result }) => result?.flagged).length,
    };
});

/** La evolución del test elegido: una línea por tripulante. */
const series = computed<CrewSeries[]>(() =>
    crew.members.map((member, index) => ({
        astronaut_id: member.id,
        name: member.name,
        color: seriesColor(index),
        points: test.value
            ? psych.history(member.id, test.value.id).map((r) => ({
                  day: r.day,
                  value: r.score,
                  status: r.status,
              }))
            : [],
    })),
);

/** La franja "sana" del test: hasta el tope del primer nivel verde. */
const bands = computed<Bands | null>(() => {
    const green = test.value?.bands
        .filter((band) => band.status === 'green')
        .at(-1);
    const yellow = test.value?.bands
        .filter((band) => band.status === 'yellow')
        .at(-1);

    return green
        ? {
              green_min: 0,
              green_max: green.max,
              yellow_min: 0,
              yellow_max: yellow?.max ?? green.max,
          }
        : null;
});

onMounted(async () => {
    if (!auth.can('health.read.any')) {
        toast.error(
            'Los resultados son para el equipo médico y administración.',
        );
        router.visit(dashboard());

        return;
    }

    await Promise.all([
        crew.load(auth.actor?.astronaut_id ?? null, { activeOnly: true }),
        psych.load(),
    ]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Resultados de la tripulación" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header class="flex flex-col gap-4" data-reveal>
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            >
                <div>
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                    >
                        {{ crew.mission?.name ?? 'Guía de salud' }}
                    </p>
                    <h1
                        class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                    >
                        Resultados de la tripulación
                    </h1>
                    <p class="mt-2 max-w-2xl text-muted-foreground">
                        Cómo va cada uno en lo psicológico y en lo intelectual.
                        En todos estos tests, un puntaje más alto es peor.
                    </p>
                </div>
                <MissionPicker
                    v-if="crew.mission"
                    v-model="missionId"
                    :missions="crew.choices"
                    :locked="crew.locked"
                />
            </div>
            <GuideTabs active="results" :show-results="true" />
        </header>

        <template v-if="!ready">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Skeleton v-for="n in 4" :key="n" class="h-32 rounded-2xl" />
            </div>
            <Skeleton class="h-80 rounded-2xl" />
        </template>

        <p
            v-else-if="!crew.mission"
            class="rounded-2xl border border-dashed p-10 text-center text-sm text-muted-foreground"
        >
            No hay misiones en curso.
        </p>

        <template v-else>
            <div data-reveal><MissionStrip :mission="crew.mission" /></div>
            <NoCrew
                v-if="!crew.members.length"
                :mission-name="crew.mission.name"
                :can-manage="auth.can('users.manage')"
            />

            <template v-else>
                <section
                    class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                    data-reveal
                >
                    <SummaryStat
                        label="Tests al día"
                        :value="stats.done"
                        :total="stats.expected"
                        hint="Hechos dentro de su frecuencia"
                        :icon="ClipboardCheck"
                        tone="sky"
                    />
                    <SummaryStat
                        label="En precaución"
                        :value="stats.yellow"
                        hint="Resultados a vigilar"
                        :icon="Activity"
                        :tone="stats.yellow ? 'red' : 'green'"
                    />
                    <SummaryStat
                        label="Críticos"
                        :value="stats.red"
                        hint="Resultados para atender ya"
                        :icon="Siren"
                        :tone="stats.red ? 'red' : 'green'"
                    />
                    <SummaryStat
                        label="Respuestas de riesgo"
                        :value="stats.flagged"
                        hint="Avisaron solas a Tierra"
                        :icon="TriangleAlert"
                        :tone="stats.flagged ? 'red' : 'green'"
                    />
                </section>

                <section
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <h2 class="mb-3 font-semibold">
                        Último resultado de cada uno
                    </h2>
                    <PsychMatrix
                        :members="crew.members"
                        :tests="psych.tests"
                        :history="psych.history"
                        :selected="selected"
                        @pick="selected = $event"
                    />
                </section>

                <section
                    v-if="test"
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <h2 class="font-semibold">{{ test.name }} en la misión</h2>
                    <p class="mb-3 text-sm text-muted-foreground">
                        {{ test.description }} La franja gris es el nivel sano.
                    </p>
                    <ul
                        class="mb-2 flex flex-wrap gap-x-4 gap-y-1 text-xs"
                        aria-label="Tripulantes"
                    >
                        <li
                            v-for="line in series"
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
                        :series="series"
                        :bands="bands"
                        :meta="{
                            label: test.name,
                            unit: test.unit,
                            decimals: 0,
                        }"
                    />
                </section>
            </template>
        </template>
    </div>
</template>
