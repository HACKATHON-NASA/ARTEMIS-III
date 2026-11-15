<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Activity, BellRing, ClipboardCheck, Siren } from '@lucide/vue';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, salud } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import AlertFeed from '../Alertas/Components/AlertFeed.vue';
import AttendDialog from '../Alertas/Components/AttendDialog.vue';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import type { Alert } from '../Alertas/Types/alerts';
import { useBodyStore } from '../Cuerpo/Stores/useBodyStore';
import MissionPicker from '../Entorno/Components/MissionPicker.vue';
import MissionStrip from '../Entorno/Components/MissionStrip.vue';
import NoCrew from '../Entorno/Components/NoCrew.vue';
import { useCrewStore } from '../Entorno/Stores/useCrewStore';
import { useAssessmentStore } from '../Evaluacion/Stores/useAssessmentStore';
import CrewWatchList from './Components/Panel/CrewWatchList.vue';
import RecentChanges from './Components/Panel/RecentChanges.vue';
import SummaryStat from './Components/Panel/SummaryStat.vue';
import { useCrewTrendsStore } from './Stores/useCrewTrendsStore';
import type { WatchRow } from './Types/crewTrends';

/**
 * Panel de salud (/salud): el tablero de quien monitorea una misión en
 * curso. Lo importante de hoy, en vivo: las novedades que llegan (SOS,
 * ayuda, valores fuera de rango), quién necesita atención y qué cambió de
 * color en las últimas 72 h. Los cuerpos de cada uno están en Tripulación.
 * ⚠️ Por ahora con datos de prueba (ver los stores de cada módulo).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Panel de salud', href: salud() }] },
});

const DAY_MS = 24 * 60 * 60 * 1000;

const auth = useAuthStore();
const crew = useCrewStore();
const alerts = useAlertsStore();
const assessment = useAssessmentStore();
const body = useBodyStore();
const trends = useCrewTrendsStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const attend = ref<{ open: boolean; alert: Alert | null }>({
    open: false,
    alert: null,
});

const missionId = computed({
    get: () => crew.missionId,
    set: (id) => id !== null && crew.selectMission(id),
});

/** Las alertas de esta tripulación: las urgentes sin atender primero. */
const missionAlerts = computed(() => {
    const weight = (alert: Alert) =>
        alert.acknowledged_at
            ? 0
            : alert.kind === 'sos'
              ? 3
              : alert.level === 'red'
                ? 2
                : 1;

    return alerts.history
        .filter((alert) => crew.ids.includes(alert.astronaut_id))
        .sort(
            (a, b) =>
                weight(b) - weight(a) ||
                b.created_at.localeCompare(a.created_at),
        );
});

const open = computed(() =>
    missionAlerts.value.filter((alert) => !alert.acknowledged_at),
);
const urgent = computed(() =>
    open.value.filter((alert) => alert.level === 'red'),
);

const crewRows = computed<WatchRow[]>(() =>
    crew.members
        .map((member) => {
            const result = assessment.crew.find(
                (item) => item.astronaut_id === member.id,
            );

            return {
                id: member.id,
                name: member.name,
                score: result?.score ?? null,
                status: result?.status ?? null,
                summary: result?.summary ?? 'Todavía no registra mediciones.',
            };
        })
        .sort((a, b) => (a.score ?? 101) - (b.score ?? 101)),
);

const atRisk = computed(
    () =>
        crewRows.value.filter((row) => row.status && row.status !== 'green')
            .length,
);

const measured = computed(() => {
    const values = Object.values(body.crew).flatMap((status) =>
        Object.values(status).flatMap((zone) => zone.values),
    );

    return {
        done: values.filter(
            (value) =>
                Date.now() - new Date(value.measured_at).getTime() < DAY_MS,
        ).length,
        total: values.length,
    };
});

function nameOf(id: number): string {
    return crew.find(id)?.name ?? 'Tripulante';
}

async function reply(text: string): Promise<void> {
    if (attend.value.alert) {
        await alerts.acknowledge(attend.value.alert.id, text);
        toast.success('Alerta atendida', {
            description: 'La respuesta va en camino a la nave.',
        });
    }
}

async function loadMission(): Promise<void> {
    await Promise.all([
        assessment.loadCrew(crew.ids),
        body.loadCrew(crew.ids),
        trends.load(crew.members, crew.mission?.current_day ?? 1),
    ]);
}

watch(
    () => crew.missionId,
    (id, previous) => {
        if (previous !== null) {
            void loadMission();
        }
    },
);

onMounted(async () => {
    if (!auth.can('health.read.any')) {
        toast.error('El Panel de salud es para médicos y administración.');
        router.visit(dashboard());

        return;
    }

    // Aquí se monitorea: solo las misiones en curso.
    await crew.load(auth.actor?.astronaut_id ?? null, { activeOnly: true });
    await Promise.all([loadMission(), alerts.loadHistory(null)]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Panel de salud" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            data-reveal
        >
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    {{ crew.mission?.name ?? 'Monitoreo' }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    Panel de salud
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Lo importante de hoy, en vivo.
                </p>
            </div>
            <MissionPicker
                v-if="crew.mission"
                v-model="missionId"
                :missions="crew.choices"
                :locked="crew.locked"
            />
        </header>

        <template v-if="!ready">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Skeleton v-for="n in 4" :key="n" class="h-36 rounded-2xl" />
            </div>
            <Skeleton class="h-96 rounded-2xl" />
        </template>

        <p
            v-else-if="!crew.mission"
            class="rounded-2xl border border-dashed p-10 text-center text-sm text-muted-foreground"
        >
            No hay misiones en curso. El Panel de salud monitorea a las
            tripulaciones que están volando.
        </p>

        <template v-else>
            <div data-reveal><MissionStrip :mission="crew.mission" /></div>

            <NoCrew
                v-if="crew.members.length === 0"
                :mission-name="crew.mission.name"
                :can-manage="auth.can('users.manage')"
            />

            <template v-else>
                <section
                    class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                    data-reveal
                >
                    <SummaryStat
                        label="Sin atender"
                        :value="open.length"
                        :hint="
                            open.length
                                ? 'Alertas y mensajes esperando respuesta'
                                : 'Todo atendido'
                        "
                        :icon="BellRing"
                        :tone="open.length ? 'red' : 'green'"
                    />
                    <SummaryStat
                        label="Urgentes"
                        :value="urgent.length"
                        :hint="
                            urgent.length
                                ? 'SOS o valores críticos sin atender'
                                : 'Nada crítico pendiente'
                        "
                        :icon="Siren"
                        :tone="urgent.length ? 'red' : 'green'"
                    />
                    <SummaryStat
                        label="En precaución o crítico"
                        :value="atRisk"
                        :total="crew.members.length"
                        hint="Tripulantes que hay que vigilar"
                        :icon="Activity"
                        :tone="atRisk ? 'red' : 'green'"
                    />
                    <SummaryStat
                        label="Mediciones de hoy"
                        :value="measured.done"
                        :total="measured.total"
                        hint="De toda la tripulación, últimas 24 h"
                        :icon="ClipboardCheck"
                        tone="sky"
                    />
                </section>

                <div
                    class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)]"
                >
                    <div data-reveal>
                        <AlertFeed
                            :alerts="missionAlerts.slice(0, 8)"
                            :name-of="nameOf"
                            :can-attend="auth.can('alerts.acknowledge')"
                            @attend="attend = { open: true, alert: $event }"
                        />
                    </div>
                    <div class="flex flex-col gap-6" data-reveal>
                        <CrewWatchList :rows="crewRows" />
                        <RecentChanges :changes="trends.changes" />
                    </div>
                </div>
            </template>
        </template>

        <AttendDialog
            v-model:open="attend.open"
            :alert="attend.alert"
            :submit="reply"
        />
    </div>
</template>
