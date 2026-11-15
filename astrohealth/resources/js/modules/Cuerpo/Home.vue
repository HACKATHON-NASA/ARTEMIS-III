<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, inicio, registrar } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { BodyZoneCode } from '@/types/health';
import AlertBanner from '../Alertas/Components/AlertBanner.vue';
import GroundLinkModal from '../Alertas/Components/GroundLinkModal.vue';
import RecommendationCard from '../Alertas/Components/RecommendationCard.vue';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import { useMissionStore } from '../Entorno/Stores/useMissionStore';
import HealthScore from '../Evaluacion/Components/HealthScore.vue';
import { useAssessmentStore } from '../Evaluacion/Stores/useAssessmentStore';
import HeartbeatLine from '../Identidad/Components/Acceso/HeartbeatLine.vue';
import ReminderList from '../Recordatorios/Components/ReminderList.vue';
import { useRemindersStore } from '../Recordatorios/Stores/useRemindersStore';
import type { Reminder } from '../Recordatorios/Types/reminders';
import BodyViewer from './Components/BodyViewer.vue';
import BodyPartPanel from './Components/BodyPartPanel.vue';
import HomeGreeting from './Components/HomeGreeting.vue';
import VitalsGrid from './Components/VitalsGrid.vue';
import { useBodyStore } from './Stores/useBodyStore';

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

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const panelOpen = ref(false);
const groundOpen = ref(false);
const sending = ref(false);
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

async function sendToGround(): Promise<void> {
    if (!alerts.active) {
        return;
    }

    sending.value = true;
    await alerts.sendToGround(alerts.active.id);
    sending.value = false;
    groundOpen.value = true;
}

async function completeReminder(reminder: Reminder): Promise<void> {
    busyReminder.value = reminder.id;
    const points = await reminders.markDone(reminder.id);
    busyReminder.value = null;

    if (points > 0) {
        toast.success(`¡Hecho! +${points} puntos`, {
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
    ]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Inicio" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <template v-if="!ready || !body.status || !mission.current">
            <Skeleton class="h-44 rounded-2xl" />
            <div
                class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]"
            >
                <Skeleton class="h-[560px] rounded-2xl" />
                <Skeleton class="h-[560px] rounded-2xl" />
            </div>
        </template>

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
                    :mission-name="mission.current.name"
                    :environment-label="mission.current.environment_label"
                    :day="mission.current.day"
                    :day-count="mission.current.day_count"
                    :gravity="mission.current.gravity_g"
                    :delay-seconds="mission.current.ground_delay_seconds"
                />
            </section>

            <div v-if="alerts.active" data-reveal>
                <AlertBanner
                    :alert="alerts.active"
                    :sending="sending"
                    @details="openZone('heart')"
                    @send="sendToGround"
                />
            </div>

            <div
                class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]"
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
                        <div v-else class="py-6 text-center">
                            <p class="font-semibold">Aún no tienes puntaje</p>
                            <p class="mt-1 text-sm text-muted-foreground">
                                Registra tus primeras mediciones y aquí verás tu
                                estado general.
                            </p>
                            <Link
                                :href="registrar()"
                                class="mt-4 inline-flex rounded-lg bg-linear-to-r from-sky-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:brightness-110"
                            >
                                Registrar datos
                            </Link>
                        </div>
                    </section>
                    <section
                        class="rounded-2xl border bg-card p-4 sm:p-6"
                        data-reveal
                    >
                        <ReminderList
                            :reminders="reminders.reminders"
                            :done="reminders.progress.done"
                            :total="reminders.progress.total"
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

        <GroundLinkModal v-model:open="groundOpen" :alert="alerts.active" />
    </div>
</template>
