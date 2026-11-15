<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { BellRing, ClipboardCheck, ClipboardList } from '@lucide/vue';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, salud } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import RecommendationCard from '../Alertas/Components/RecommendationCard.vue';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import MissionPicker from '../Entorno/Components/MissionPicker.vue';
import MissionStrip from '../Entorno/Components/MissionStrip.vue';
import NoCrew from '../Entorno/Components/NoCrew.vue';
import { useCrewStore } from '../Entorno/Stores/useCrewStore';
import HealthScore from '../Evaluacion/Components/HealthScore.vue';
import { useAssessmentStore } from '../Evaluacion/Stores/useAssessmentStore';
import AstronautPicker from './Components/AstronautPicker.vue';
import BodyViewer from './Components/BodyViewer.vue';
import SummaryStat from './Components/SummaryStat.vue';
import ZoneDetailCard from './Components/ZoneDetailCard.vue';
import { useBodyStore } from './Stores/useBodyStore';

/**
 * Panel de salud (/salud): médicos y administración eligen la misión y un
 * tripulante, y ven su estado, sus alertas y su cuerpo en 3D con el detalle
 * del órgano al lado. Se puede abrir con ?astronaut=ID (desde Tripulación):
 * entonces se elige sola la misión de ese tripulante.
 * ⚠️ Por ahora con datos de prueba (ver los stores de cada módulo).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Panel de salud', href: salud() }] },
});

const auth = useAuthStore();
const crew = useCrewStore();
const body = useBodyStore();
const assessment = useAssessmentStore();
const alerts = useAlertsStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const astronautId = ref<number | null>(null);
const loading = ref(true);

const missionId = computed({
    get: () => crew.missionId,
    set: (id) => id !== null && crew.selectMission(id),
});

const member = computed(() =>
    astronautId.value ? crew.find(astronautId.value) : null,
);
const pendingAlerts = computed(
    () =>
        alerts.alerts.filter((alert) => alert.acknowledged_at === null).length,
);
const zone = computed(() =>
    body.selected && body.status ? body.status[body.selected] : null,
);
const recommendations = computed(() =>
    alerts.recommendationsFor(zone.value?.values ?? []),
);

async function loadAstronaut(id: number | null): Promise<void> {
    if (id === null) {
        loading.value = false;

        return;
    }

    loading.value = true;
    await Promise.all([body.load(id), assessment.load(id), alerts.load(id)]);
    body.select(body.mostUrgent);
    loading.value = false;
    window.history.replaceState(
        window.history.state,
        '',
        `${salud().url}?astronaut=${id}`,
    );
}

// Al cambiar de tripulante (la primera carga la hace onMounted).
let ready = false;
watch(astronautId, (id) => {
    if (ready) {
        void loadAstronaut(id);
    }
});

// Al cambiar de misión: su primer tripulante (o ninguno si no tiene).
watch(
    () => crew.missionId,
    (id, previous) => {
        if (previous !== null) {
            astronautId.value = crew.ids[0] ?? null;
        }
    },
);

onMounted(async () => {
    if (!auth.can('health.read.any')) {
        toast.error('El Panel de salud es para médicos y administración.');
        router.visit(dashboard());

        return;
    }

    await crew.load(auth.actor?.astronaut_id ?? null);
    const fromUrl = Number(
        new URLSearchParams(window.location.search).get('astronaut'),
    );
    const urlMission = crew.missionOf(fromUrl);

    if (urlMission !== null) {
        crew.selectMission(urlMission);
    }

    const first = crew.find(fromUrl) ? fromUrl : (crew.ids[0] ?? null);
    astronautId.value = first;
    await loadAstronaut(first);
    ready = true;
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
                    {{ crew.mission?.name ?? 'Tripulación' }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    Panel de salud
                </h1>
                <p class="mt-2 text-muted-foreground">
                    El estado de cada tripulante, órgano por órgano.
                </p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row">
                <MissionPicker
                    v-if="crew.mission"
                    v-model="missionId"
                    :missions="crew.choices"
                    :locked="crew.locked"
                />
                <AstronautPicker
                    v-if="crew.members.length"
                    v-model="astronautId"
                    :members="crew.members"
                />
            </div>
        </header>

        <div v-if="crew.mission" data-reveal>
            <MissionStrip :mission="crew.mission" />
        </div>

        <NoCrew
            v-if="crew.mission && crew.members.length === 0"
            :mission-name="crew.mission.name"
            :can-manage="auth.can('users.manage')"
        />

        <template v-else-if="loading || !body.status">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Skeleton class="h-44 rounded-2xl xl:col-span-2" />
                <Skeleton class="h-44 rounded-2xl" />
                <Skeleton class="h-44 rounded-2xl" />
            </div>
            <Skeleton class="h-[640px] rounded-2xl" />
        </template>

        <template v-else>
            <section
                class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
                data-reveal
            >
                <div class="rounded-2xl border bg-card p-5 sm:col-span-2">
                    <p class="mb-3 text-sm text-muted-foreground">
                        {{ member?.name }} · {{ member?.crew_role }}
                    </p>
                    <HealthScore
                        v-if="assessment.current"
                        :assessment="assessment.current"
                    />
                    <div v-else class="flex items-center gap-4">
                        <span
                            class="flex size-14 shrink-0 items-center justify-center rounded-2xl bg-muted text-muted-foreground"
                        >
                            <ClipboardList class="size-6" />
                        </span>
                        <div>
                            <p class="font-semibold">Sin datos todavía</p>
                            <p class="text-sm text-muted-foreground">
                                Su puntaje aparece cuando registre sus primeras
                                mediciones.
                            </p>
                        </div>
                    </div>
                </div>
                <SummaryStat
                    label="Alertas sin atender"
                    :value="pendingAlerts"
                    :hint="
                        pendingAlerts
                            ? 'Esperan respuesta desde Tierra'
                            : 'Nada pendiente'
                    "
                    :icon="BellRing"
                    :tone="pendingAlerts ? 'red' : 'green'"
                />
                <SummaryStat
                    label="Mediciones de hoy"
                    :value="body.measuredToday.done"
                    :total="body.measuredToday.total"
                    hint="Indicadores medidos en las últimas 24 h"
                    :icon="ClipboardCheck"
                    tone="sky"
                />
            </section>

            <section
                class="relative overflow-hidden rounded-2xl border bg-card p-4 sm:p-6"
                data-reveal
            >
                <h2 class="text-lg font-semibold">Mapa del cuerpo</h2>
                <p class="mb-4 text-sm text-muted-foreground">
                    Toca un órgano para ver su función, sus riesgos en el
                    espacio y las últimas mediciones.
                </p>
                <div
                    class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]"
                >
                    <BodyViewer
                        :parts="body.parts"
                        :status="body.status"
                        :selected="body.selected"
                        @select="body.select"
                    />
                    <ZoneDetailCard
                        v-if="body.selectedPart && zone"
                        :part="body.selectedPart"
                        :zone="zone"
                    >
                        <template v-if="recommendations.length" #recommendation>
                            <div class="flex flex-col gap-3">
                                <RecommendationCard
                                    v-for="item in recommendations"
                                    :key="item.title"
                                    :recommendation="item"
                                />
                            </div>
                        </template>
                    </ZoneDetailCard>
                </div>
            </section>
        </template>
    </div>
</template>
