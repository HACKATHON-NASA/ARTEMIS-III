<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { alertMessage } from './Utils/alertText';
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
import AstronautComms from './Components/AstronautComms.vue';
import AttendDialog from './Components/AttendDialog.vue';
import DemoSpeedToggle from './Components/DemoSpeedToggle.vue';
import GroundLinkModal from './Components/GroundLinkModal.vue';
import MessageDialog from './Components/MessageDialog.vue';
import { useAlertsStore } from './Stores/useAlertsStore';
import type { Alert, NewMessage } from './Types/alerts';
import { matchesFilter } from './Utils/alertState';
import type { AlertFilter } from './Utils/alertState';

const { t } = useI18n();

/**
 * Centro de alertas (/alertas, M5): aquí llega SOLO todo lo de la salud de
 * la tripulación (valores fuera de rango, SOS, pedidos de ayuda y novedades)
 * y se actualiza en vivo (useLiveAlerts, desde el menú). El astronauta habla
 * con Tierra con sus tres botones; Tierra atiende y responde.
 * ⚠️ Por ahora con datos de prueba (ver Stores/useAlertsStore.ts).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'nav.alerts', href: alertas() }] },
});

const auth = useAuthStore();
const store = useAlertsStore();
const crew = useCrewStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const filter = ref<AlertFilter>('open');
const astronaut = ref<number | null>(null);
const compose = ref<{ open: boolean; kind: NewMessage['kind'] | null }>({
    open: false,
    kind: null,
});
const ground = ref<{ open: boolean; alert: Alert | null }>({
    open: false,
    alert: null,
});
const attend = ref<{ open: boolean; alert: Alert | null }>({
    open: false,
    alert: null,
});

const seesCrew = computed(() => auth.can('health.read.any'));
const ownAstronaut = computed(() => auth.actor?.astronaut_id ?? null);
const canMessage = computed(
    () => auth.can('alerts.send') && ownAstronaut.value !== null,
);

const missionId = computed({
    get: () => crew.missionId,
    set: (id) => id !== null && crew.selectMission(id),
});

function urgency(alert: Alert): number {
    return alert.acknowledged_at === null
        ? alert.kind === 'sos'
            ? 2
            : alert.level === 'red'
              ? 1
              : 0
        : -1;
}

/** Las de la tripulación de la misión elegida; las urgentes sin atender, arriba. */
const missionHistory = computed(() =>
    (seesCrew.value
        ? store.history.filter((alert) => crew.ids.includes(alert.astronaut_id))
        : store.history
    )
        .slice()
        .sort(
            (a, b) =>
                urgency(b) - urgency(a) ||
                b.created_at.localeCompare(a.created_at),
        ),
);

const counts = computed(() => {
    const keys: AlertFilter[] = ['open', 'urgent', 'acknowledged', 'all'];

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

async function sendMessage(message: NewMessage): Promise<void> {
    if (ownAstronaut.value === null) {
        return;
    }

    const alert = await store.send(ownAstronaut.value, message);
    filter.value = 'open';
    ground.value = { open: true, alert };
}

async function reply(text: string): Promise<void> {
    if (!attend.value.alert) {
        return;
    }

    const done = await store.acknowledge(attend.value.alert.id, text);
    toast.success(t('Alertas.alertsPage.attended'), {
        description: t('Alertas.alertsPage.replySent', {
            message: done ? alertMessage(done, nameOf(done.astronaut_id)) : '',
        }),
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
    <Head :title="t('Alertas.alertsPage.pageTitle')" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between"
            data-reveal
        >
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    {{ t('Alertas.alertsPage.eyebrow') }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('Alertas.alertsPage.title') }}
                </h1>
                <p class="mt-2 max-w-2xl text-muted-foreground">
                    {{ t('Alertas.alertsPage.subtitle') }}
                </p>
            </div>
            <DemoSpeedToggle :speed="store.speed" @change="store.setSpeed" />
        </header>

        <section v-if="canMessage" class="flex flex-col gap-3" data-reveal>
            <h2 class="text-lg font-semibold">
                {{ t('Alertas.alertsPage.talkToEarth') }}
            </h2>
            <AstronautComms @choose="compose = { open: true, kind: $event }" />
        </section>

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
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="-translate-y-2 opacity-0"
        >
            <AlertItem
                v-for="alert in visible"
                :key="alert.id"
                :alert="alert"
                :astronaut-name="nameOf(alert.astronaut_id)"
                :recommendation="
                    store.recommendationFor(alert.indicator, alert.level)
                "
                :can-attend="auth.can('alerts.acknowledge')"
                @attend="attend = { open: true, alert }"
            />
        </TransitionGroup>

        <div
            v-else
            class="flex flex-col items-center gap-2 rounded-2xl border border-dashed p-10 text-center"
            data-reveal
        >
            <BellOff class="size-8 text-muted-foreground" />
            <p class="font-medium">{{ t('Alertas.alertsPage.emptyTitle') }}</p>
            <p class="text-sm text-muted-foreground">
                {{ t('Alertas.alertsPage.emptyHint') }}
            </p>
        </div>

        <MessageDialog
            v-model:open="compose.open"
            :kind="compose.kind"
            :submit="sendMessage"
        />
        <GroundLinkModal v-model:open="ground.open" :alert="ground.alert" />
        <AttendDialog
            v-model:open="attend.open"
            :alert="attend.alert"
            :submit="reply"
        />
    </div>
</template>
