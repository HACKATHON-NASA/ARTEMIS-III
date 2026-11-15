<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Plus, Rocket } from '@lucide/vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, misiones } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import HeartbeatLine from '../Identidad/Components/Acceso/HeartbeatLine.vue';
import MissionCard from './Components/MissionCard.vue';
import MissionFormDialog from './Components/MissionFormDialog.vue';
import MissionTabs from './Components/MissionTabs.vue';
import { useMissionStore } from './Stores/useMissionStore';
import { useMissionsStore } from './Stores/useMissionsStore';
import type { MissionFilter, MissionFormData } from './Types/missions';

/**
 * Misiones (/admin/misiones, M6): administración crea las misiones y ve en
 * qué va cada una y quiénes la tripulan. Los tripulantes se crean en
 * Usuarios, asociados a una misión. Solo con el permiso missions.manage.
 * ⚠️ Por ahora con datos de prueba (ver Stores/useMissionsStore.ts).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Misiones', href: misiones() }] },
});

const auth = useAuthStore();
const store = useMissionsStore();
/** La misión actual (la que siguen el Panel de salud y la tripulación). */
const current = useMissionStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const dialog = ref(false);
// Lo que importa es lo que está pasando: se abre en "En curso".
const filter = ref<MissionFilter>('active');

const counts = computed(() => ({
    all: store.views.length,
    active: store.views.filter((m) => m.status === 'active').length,
    planned: store.views.filter((m) => m.status === 'planned').length,
    finished: store.views.filter((m) => m.status === 'finished').length,
}));

const visible = computed(() =>
    store.views.filter(
        (mission) => filter.value === 'all' || mission.status === filter.value,
    ),
);

const inSpace = computed(() =>
    store.views
        .filter((mission) => mission.status === 'active')
        .reduce((total, mission) => total + mission.crew.length, 0),
);

async function create(data: MissionFormData): Promise<void> {
    const mission = await store.createMission(data);
    // Se muestra la pestaña donde quedó la misión nueva.
    filter.value = store.find(mission.id)?.status ?? 'all';
    toast.success(`Misión ${mission.name} creada`, {
        description: 'Ahora asóciale tripulantes desde Usuarios.',
    });
}

onMounted(async () => {
    if (!auth.can('missions.manage')) {
        toast.error('Esa vista es solo para administración.');
        router.visit(dashboard());

        return;
    }

    await Promise.all([store.load(), current.load()]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Misiones" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="relative overflow-hidden rounded-2xl border bg-linear-to-br from-sky-500/10 via-card to-card p-6 sm:p-8"
            data-reveal
        >
            <HeartbeatLine
                class="pointer-events-none absolute top-5 -right-6 hidden w-80 opacity-70 sm:block"
            />
            <p
                class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
            >
                Centro de control
            </p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
                Misiones
            </h1>
            <p class="mt-2 max-w-xl text-muted-foreground">
                Crea las misiones y sigue cada una. Los tripulantes se crean en
                Usuarios y se asocian a su misión.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <Button
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_20px_-8px_rgb(14_165_233/0.7)] hover:brightness-110"
                    @click="dialog = true"
                >
                    <Plus />
                    Nueva misión
                </Button>
                <span
                    v-if="ready"
                    class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 text-sm backdrop-blur"
                >
                    <Rocket class="size-4 text-sky-500" />
                    <strong class="tabular-nums">{{ inSpace }}</strong>
                    <span class="text-muted-foreground">
                        tripulantes en el espacio
                    </span>
                </span>
            </div>
        </header>

        <template v-if="!ready">
            <Skeleton class="h-10 w-80 rounded-xl" />
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <Skeleton v-for="n in 3" :key="n" class="h-96 rounded-2xl" />
            </div>
        </template>

        <template v-else>
            <div data-reveal>
                <MissionTabs v-model="filter" :counts="counts" />
            </div>

            <TransitionGroup
                v-if="visible.length"
                tag="div"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
                data-reveal
                move-class="transition duration-300"
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="translate-y-2 opacity-0"
            >
                <MissionCard
                    v-for="mission in visible"
                    :key="mission.id"
                    :mission="mission"
                    :current="mission.id === current.current?.id"
                />
            </TransitionGroup>

            <p
                v-else
                class="rounded-2xl border border-dashed p-10 text-center text-sm text-muted-foreground"
            >
                No hay misiones en este estado.
            </p>
        </template>

        <MissionFormDialog
            v-model:open="dialog"
            :taken-names="store.missions.map((mission) => mission.name)"
            :submit="create"
        />
    </div>
</template>
