<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { useReveal } from '@/composables/useReveal';
import { dashboard, inicio, logros, psicologia, registrar } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { BodyZoneCode } from '@/types/health';
import AlertBanner from '../Alertas/Components/AlertBanner.vue';
import AstronautComms from '../Alertas/Components/AstronautComms.vue';
import GroundLinkModal from '../Alertas/Components/GroundLinkModal.vue';
import MessageDialog from '../Alertas/Components/MessageDialog.vue';
import RecommendationCard from '../Alertas/Components/RecommendationCard.vue';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import type { Alert, NewMessage } from '../Alertas/Types/alerts';
import { useMissionStore } from '../Entorno/Stores/useMissionStore';
import { useGamificationStore } from '../Gamificacion/Stores/useGamificationStore';
import HealthScore from '../Evaluacion/Components/HealthScore.vue';
import NoScore from '../Evaluacion/Components/NoScore.vue';
import { useAssessmentStore } from '../Evaluacion/Stores/useAssessmentStore';
import { usePsychStore } from '../Evaluacion/Stores/usePsychStore';
import HeartbeatLine from '../Identidad/Components/Acceso/HeartbeatLine.vue';
import RoutineCard from '../Recordatorios/Components/RoutineCard.vue';
import { useRemindersStore } from '../Recordatorios/Stores/useRemindersStore';
import type { Reminder } from '../Recordatorios/Types/reminders';
import BodyViewer from './Components/BodyViewer.vue';
import BodyPartPanel from './Components/BodyPartPanel.vue';
import HomeGreeting from './Components/HomeGreeting.vue';
import HomeSkeleton from './Components/HomeSkeleton.vue';
import TodayPlan from './Components/TodayPlan.vue';
import VitalsGrid from './Components/VitalsGrid.vue';
import { useBodyStore } from './Stores/useBodyStore';
import { buildTodayPlan } from './Utils/todayPlan';

/**
 * Inicio del astronauta (/inicio): cuerpo (M3) + puntaje (M2) + alertas (M5)
 * + rutina (M8), con la misión de M6. El principal compone; cada módulo
 * pone sus piezas. ⚠️ Por ahora, todos con datos de prueba (ver sus stores).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Inicio', href: inicio() }] },
});

const auth = useAuthStore();
const body = useBodyStore();
const assessment = useAssessmentStore();
const alerts = useAlertsStore();
const reminders = useRemindersStore();
const mission = useMissionStore();
const gamification = useGamificationStore();
const psych = usePsychStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const panelOpen = ref(false);
const groundOpen = ref(false);
const sentAlert = ref<Alert | null>(null);
const compose = ref<{ open: boolean; kind: NewMessage['kind'] | null }>({
    open: false,
    kind: null,
});
const busyReminder = ref<number | null>(null);

function openZone(code: BodyZoneCode): void {
    body.select(code);
    panelOpen.value = true;
}

/** Las recomendaciones de la zona abierta (M5). */
const zoneRecommendations = computed(() =>
    alerts.recommendationsFor(
        body.selected && body.status ? body.status[body.selected].values : [],
    ),
);

/** SOS, ayuda o novedad: sale ya hacia Tierra y se ve el viaje. */
async function sendMessage(message: NewMessage): Promise<void> {
    const astronautId = auth.actor?.astronaut_id;

    if (!astronautId) {
        return;
    }

    sentAlert.value = await alerts.send(astronautId, message);
    groundOpen.value = true;
}

/** "Tu día": lo que falta medir, la rutina, los tests y la próxima insignia. */
const plan = computed(() =>
    buildTodayPlan({
        values: Object.values(body.status ?? {}).flatMap((zone) => zone.values),
        routine: {
            ...reminders.progress,
            next:
                reminders.reminders.find((item) => item.state !== 'done') ??
                null,
        },
        tests: {
            pending: psych.tests.filter((test) =>
                psych.isDue(
                    auth.actor?.astronaut_id ?? 0,
                    test,
                    mission.current?.day ?? 1,
                ),
            ).length,
            total: psych.tests.length,
        },
        badges: gamification.profile?.badges ?? [],
        links: {
            record: registrar(),
            routine: `${inicio().url}#rutina`,
            tests: psicologia(),
            badges: logros(),
        },
    }),
);

async function completeReminder(reminder: Reminder): Promise<void> {
    busyReminder.value = reminder.id;
    if (!(await reminders.markDone(reminder.id))) {
        busyReminder.value = null;

        return;
    }

    const { gained } = await gamification.award(
        auth.actor?.astronaut_id ?? 0,
        'reminder',
        `${reminder.id}-${mission.current?.day ?? 1}`,
        mission.current?.day ?? 1,
    );
    busyReminder.value = null;

    if (gained > 0) {
        toast.success(`¡Hecho! +${gained} puntos`, {
            description: reminder.title,
        });
    }
}

onMounted(async () => {
    const astronautId = auth.actor?.astronaut_id ?? null;

    if (astronautId === null) {
        toast.error('El Inicio es para la tripulación.');
        router.visit(dashboard());

        return;
    }

    await Promise.all([
        body.load(astronautId),
        assessment.load(astronautId),
        alerts.load(astronautId),
        reminders.load(astronautId),
        mission.load(),
        psych.load(),
    ]);
    await gamification.load(astronautId, mission.current?.day ?? 1);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Inicio" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <HomeSkeleton v-if="!ready || !body.status || !mission.current" />

        <template v-else>
            <section
                class="relative overflow-hidden rounded-2xl border bg-linear-to-br from-sky-500/10 via-card to-card p-6 sm:p-8"
                data-reveal
            >
                <HeartbeatLine
                    class="pointer-events-none absolute top-5 -right-6 hidden w-80 opacity-70 sm:block"
                />
                <HomeGreeting
                    :name="auth.actor?.name ?? ''"
                    :mission="mission.current"
                />
            </section>

            <div v-if="alerts.active" data-reveal>
                <AlertBanner
                    :alert="alerts.active"
                    @details="
                        openZone(alerts.active.indicator ? 'heart' : 'brain')
                    "
                />
            </div>

            <div data-reveal>
                <TodayPlan :items="plan" />
            </div>

            <section class="flex flex-col gap-3" data-reveal>
                <h2 class="text-sm font-semibold text-muted-foreground">
                    ¿Necesitas a Tierra?
                </h2>
                <AstronautComms
                    @choose="compose = { open: true, kind: $event }"
                />
            </section>

            <div
                class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]"
            >
                <section
                    class="relative overflow-hidden rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <div
                        class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_40%,rgb(14_165_233/0.12),transparent_65%)]"
                    />
                    <div class="relative">
                        <h2 class="text-lg font-semibold">Tu cuerpo</h2>
                        <p class="mb-4 text-sm text-muted-foreground">
                            Los órganos que laten necesitan tu atención.
                            Arrastra para girar y toca un órgano.
                        </p>
                        <BodyViewer
                            :parts="body.parts"
                            :status="body.status"
                            :selected="panelOpen ? body.selected : null"
                            @select="openZone"
                        />
                    </div>
                </section>

                <div class="flex flex-col gap-6">
                    <section
                        class="rounded-2xl border bg-card p-4 sm:p-6"
                        data-reveal
                    >
                        <HealthScore
                            v-if="assessment.current"
                            :assessment="assessment.current"
                        />
                        <NoScore v-else />
                    </section>
                    <section
                        id="rutina"
                        class="scroll-mt-4 rounded-2xl border bg-card p-4 sm:p-6"
                        data-reveal
                    >
                        <RoutineCard
                            :busy-id="busyReminder"
                            @done="completeReminder"
                        />
                    </section>
                </div>
            </div>

            <section class="rounded-2xl border bg-card p-4 sm:p-6" data-reveal>
                <h2 class="mb-4 text-lg font-semibold">Últimos valores</h2>
                <VitalsGrid :status="body.status" @select="openZone" />
            </section>
        </template>

        <BodyPartPanel
            v-model:open="panelOpen"
            :part="body.selectedPart"
            :zone="
                body.selected && body.status ? body.status[body.selected] : null
            "
        >
            <template v-if="zoneRecommendations.length" #recommendation>
                <div class="flex flex-col gap-3">
                    <RecommendationCard
                        v-for="item in zoneRecommendations"
                        :key="item.title"
                        :recommendation="item"
                    />
                </div>
            </template>
        </BodyPartPanel>

        <MessageDialog
            v-model:open="compose.open"
            :kind="compose.kind"
            :submit="sendMessage"
        />
        <GroundLinkModal v-model:open="groundOpen" :alert="sentAlert" />
    </div>
</template>
