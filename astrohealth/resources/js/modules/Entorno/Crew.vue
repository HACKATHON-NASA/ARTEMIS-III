<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, salud, tripulacion } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import { useAssessmentStore } from '../Evaluacion/Stores/useAssessmentStore';
import CrewCard from './Components/CrewCard.vue';
import { useCrewStore } from './Stores/useCrewStore';

/**
 * Tripulación (/tripulacion): cada astronauta con su puntaje y estado, y el
 * acceso a su cuerpo en el Panel de salud. Para médicos y administración.
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

/** Cada tripulante con su evaluación (M2). */
const cards = computed(() =>
    crew.members.flatMap((member) => {
        const result = assessment.crew.find(
            (item) => item.astronaut_id === member.id,
        );

        return result ? [{ member, result }] : [];
    }),
);

onMounted(async () => {
    if (!auth.can('health.read.any')) {
        toast.error(
            'La vista de tripulación es para médicos y administración.',
        );
        router.visit(dashboard());

        return;
    }

    await crew.load();
    await assessment.loadCrew(crew.ids);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Tripulación" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header data-reveal>
            <p
                class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
            >
                Ares-1
            </p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
                Tripulación
            </h1>
            <p class="mt-2 text-muted-foreground">
                Quién está a bordo y cómo está cada uno hoy.
            </p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <template v-if="!ready">
                <Skeleton v-for="n in 4" :key="n" class="h-64 rounded-2xl" />
            </template>
            <template v-else>
                <div
                    v-for="{ member, result } in cards"
                    :key="member.id"
                    data-reveal
                >
                    <CrewCard
                        :name="member.name"
                        :crew-role="member.crew_role"
                        :score="result.score"
                        :status="result.status"
                        :summary="result.summary"
                        :href="salud({ query: { astronaut: member.id } })"
                    />
                </div>
            </template>
        </div>
    </div>
</template>
