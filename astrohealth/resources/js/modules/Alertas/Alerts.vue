<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { BellOff } from '@lucide/vue';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { alertas, dashboard } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import MissionPicker from '../Entorno/Components/MissionPicker.vue';
import MissionStrip from '../Entorno/Components/MissionStrip.vue';
import { useCrewStore } from '../Entorno/Stores/useCrewStore';
import AlertFilters from './Components/AlertFilters.vue';
import AlertItem from './Components/AlertItem.vue';
import GroundLinkModal from './Components/GroundLinkModal.vue';
import { useAlertsStore } from './Stores/useAlertsStore';
import type { Alert } from './Types/alerts';
import { matchesFilter } from './Utils/alertState';
import type { AlertFilter } from './Utils/alertState';

/**
 * Centro de alertas (/alertas, M5): el historial con su recorrido
 * nave → Tierra. El astronauta ve las suyas y las envía; médicos y
 * administración eligen la misión y ven las de su tripulación, y Tierra las
 * confirma.
 * ⚠️ Por ahora con datos de prueba (ver Stores/useAlertsStore.ts).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Alertas', href: alertas() }] },
});

const auth = useAuthStore();
const store = useAlertsStore();
const crew = useCrewStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const filter = ref<AlertFilter>('all');
const astronaut = ref<number | null>(null);
const busyId = ref<number | null>(null);
const ground = ref<{ open: boolean; alert: Alert | null }>({
    open: false,
    alert: null,
});

const seesCrew = computed(() => auth.can('health.read.any'));
const ownAstronaut = computed(() => auth.actor?.astronaut_id ?? null);

const missionId = computed({
    get: () => crew.missionId,
    set: (id) => id !== null && crew.selectMission(id),
});

/** Las alertas de la tripulación de la misión elegida. */
const missionHistory = computed(() =>
    seesCrew.value
        ? store.history.filter((alert) => crew.ids.includes(alert.astronaut_id))
        : store.history,
);

const counts = computed(() => {
    const keys: AlertFilter[] = ['all', 'pending', 'sent', 'acknowledged'];

    return Object.fromEntries(
        keys.map((key) => [
            key,
            missionHistory.value.filter((alert) => matchesFilter(alert, key))
                .length,
        ]),
    ) as Record<AlertFilter, number>;
});

const visible = computed(() =>
    missionHistory.value.filter((alert) => matchesFilter(alert, filter.value)),
);

function nameOf(id: number): string | null {
    return seesCrew.value ? (crew.find(id)?.name ?? null) : null;
}

/** El astronauta envía las suyas; el médico de la tripulación, cualquiera. */
function canSend(alert: Alert): boolean {
    return (
        auth.can('alerts.send') &&
        (seesCrew.value || alert.astronaut_id === ownAstronaut.value)
    );
}

async function send(alert: Alert): Promise<void> {
    busyId.value = alert.id;
    const sent = await store.sendToGround(alert.id);
    busyId.value = null;
    ground.value = { open: true, alert: sent };
}

async function acknowledge(alert: Alert): Promise<void> {
    busyId.value = alert.id;
    await store.acknowledge(alert.id);
    busyId.value = null;
    toast.success('Alerta confirmada desde Tierra', {
        description: alert.message,
    });
}

watch(astronaut, (id) => void store.loadHistory(id));
// Otra misión: se vuelve a "toda la tripulación" de esa misión.
watch(
    () => crew.missionId,
    () => {
        astronaut.value = null;
    },
);

onMounted(async () => {
    if (!auth.can('alerts.read')) {
        router.visit(dashboard());

        return;
    }

    await crew.load(ownAstronaut.value);

    await store.loadHistory(seesCrew.value ? null : ownAstronaut.value);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Alertas" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header data-reveal>
            <p
                class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
            >
                Enlace con Tierra
            </p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
                Centro de alertas
            </h1>
            <p class="mt-2 max-w-2xl text-muted-foreground">
                Cada valor en rojo o en amarillo y su recorrido: de la nave a la
                Tierra, hasta que el médico de vuelo la confirma. En Marte, un
                mensaje tarda unos 20 minutos.
            </p>
        </header>

        <div
            v-if="crew.mission"
            class="flex flex-col gap-3 lg:flex-row lg:items-end"
            data-reveal
        >
            <MissionStrip :mission="crew.mission" class="flex-1" />
            <MissionPicker
                v-if="seesCrew && !crew.locked"
                v-model="missionId"
                :missions="crew.choices"
                :locked="crew.locked"
            />
        </div>

        <div data-reveal>
            <AlertFilters
                v-model:filter="filter"
                v-model:astronaut="astronaut"
                :counts="counts"
                :members="seesCrew ? crew.members : undefined"
            />
        </div>

        <div v-if="!ready" class="flex flex-col gap-3">
            <Skeleton v-for="n in 3" :key="n" class="h-36 rounded-2xl" />
        </div>

        <TransitionGroup
            v-else-if="visible.length"
            tag="ul"
            class="flex flex-col gap-3"
            data-reveal
            move-class="transition duration-300"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="translate-y-2 opacity-0"
        >
            <AlertItem
                v-for="alert in visible"
                :key="alert.id"
                :alert="alert"
                :astronaut-name="nameOf(alert.astronaut_id)"
                :recommendation="
                    store.recommendationFor(alert.indicator, alert.level)
                "
                :can-send="canSend(alert)"
                :can-acknowledge="auth.can('alerts.acknowledge')"
                :busy="busyId === alert.id"
                @send="send(alert)"
                @acknowledge="acknowledge(alert)"
            />
        </TransitionGroup>

        <div
            v-else
            class="flex flex-col items-center gap-2 rounded-2xl border border-dashed p-10 text-center"
            data-reveal
        >
            <BellOff class="size-8 text-muted-foreground" />
            <p class="font-medium">No hay alertas en este filtro</p>
            <p class="text-sm text-muted-foreground">
                Cuando un valor salga en rojo o amarillo, aparece aquí.
            </p>
        </div>

        <GroundLinkModal v-model:open="ground.open" :alert="ground.alert" />
    </div>
</template>
