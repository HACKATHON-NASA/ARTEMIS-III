<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, salud, tripulacion } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import { useAssessmentStore } from '../Evaluacion/Stores/useAssessmentStore';
import CrewCard from './Components/CrewCard.vue';
import MissionPicker from './Components/MissionPicker.vue';
import MissionStrip from './Components/MissionStrip.vue';
import NoCrew from './Components/NoCrew.vue';
import { useCrewStore } from './Stores/useCrewStore';

/**
 * Tripulación (/tripulacion): la tripulación de UNA misión, cada uno con su
 * puntaje y estado, y el acceso a su cuerpo en el Panel de salud. Médicos y
 * administración eligen la misión; quien va en la nave ve la suya.
 * ⚠️ Por ahora con datos de prueba (ver los stores de M6 y M2).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Tripulación', href: tripulacion() }] },
});

const auth = useAuthStore();
const crew = useCrewStore();
const assessment = useAssessmentStore();
const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const loadingCrew = ref(false);

/** Cada tripulante con su evaluación (M2), o sin ella si aún no tiene datos. */
const cards = computed(() =>
    crew.members.map((member) => ({
        member,
        result:
            assessment.crew.find((item) => item.astronaut_id === member.id) ??
            null,
    })),
);

const missionId = computed({
    get: () => crew.missionId,
    set: (id) => id !== null && crew.selectMission(id),
});

async function loadAssessments(): Promise<void> {
    loadingCrew.value = true;
    await assessment.loadCrew(crew.ids);
    loadingCrew.value = false;
}

// Al cambiar de misión, la evaluación de su tripulación.
watch(
    () => crew.missionId,
    (id, previous) => {
        if (previous !== null) {
            void loadAssessments();
        }
    },
);

onMounted(async () => {
    if (!auth.can('health.read.any')) {
        toast.error(
            'La vista de tripulación es para médicos y administración.',
        );
        router.visit(dashboard());

        return;
    }

    await crew.load(auth.actor?.astronaut_id ?? null);
    await loadAssessments();
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Tripulación" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            data-reveal
        >
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    {{ crew.mission?.name ?? 'Misión' }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    Tripulación
                </h1>
                <p class="mt-2 text-muted-foreground">
                    Quién va en la misión y cómo está cada uno hoy.
                </p>
            </div>
            <MissionPicker
                v-if="ready"
                v-model="missionId"
                :missions="crew.choices"
                :locked="crew.locked"
            />
        </header>

        <template v-if="!ready">
            <Skeleton class="h-12 rounded-2xl" />
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Skeleton v-for="n in 4" :key="n" class="h-64 rounded-2xl" />
            </div>
        </template>

        <template v-else-if="crew.mission">
            <div data-reveal>
                <MissionStrip :mission="crew.mission" />
            </div>

            <NoCrew
                v-if="crew.members.length === 0"
                :mission-name="crew.mission.name"
                :can-manage="auth.can('users.manage')"
            />

            <div
                v-else
                class="grid gap-4 transition-opacity sm:grid-cols-2 xl:grid-cols-4"
                :class="{ 'opacity-50': loadingCrew }"
            >
                <div
                    v-for="{ member, result } in cards"
                    :key="member.id"
                    data-reveal
                >
                    <CrewCard
                        :name="member.name"
                        :crew-role="member.crew_role"
                        :score="result?.score ?? null"
                        :status="result?.status ?? null"
                        :summary="
                            result?.summary ?? 'Todavía no registra mediciones.'
                        "
                        :href="salud({ query: { astronaut: member.id } })"
                    />
                </div>
            </div>
        </template>
    </div>
</template>
