<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Head, router } from '@inertiajs/vue3';
import {
    CheckCheck,
    ClipboardPen,
    Clock,
    Inbox,
    TriangleAlert,
} from '@lucide/vue';
import {
    computed,
    nextTick,
    onBeforeUnmount,
    onMounted,
    ref,
    watch,
} from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, indicaciones, tripulante } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import SummaryStat from '../Dashboard/Components/Panel/SummaryStat.vue';
import MissionPicker from '../Entorno/Components/MissionPicker.vue';
import MissionStrip from '../Entorno/Components/MissionStrip.vue';
import NoCrew from '../Entorno/Components/NoCrew.vue';
import { useCrewStore } from '../Entorno/Stores/useCrewStore';
import {
    ENVIRONMENT_FACTS,
    formatDelay,
} from '../Entorno/Utils/environmentFacts';
import AdviceComposer from './Components/AdviceComposer.vue';
import AdviceFilters from './Components/AdviceFilters.vue';
import AdviceList from './Components/AdviceList.vue';
import { useAdviceStore } from './Stores/useAdviceStore';
import { useAlertsStore } from './Stores/useAlertsStore';
import type { AdviceCategory, AdviceDraft } from './Types/advice';
import { adviceState } from './Utils/adviceState';
import type { AdviceFilter } from './Utils/adviceState';

const { t } = useI18n();

/**
 * Indicaciones (/indicaciones, M5): el historial de lo que los médicos le
 * han indicado a la tripulación de una misión, a quién, cuándo y si ya lo
 * entendieron. Médicos y administración lo ven; advice.send escribe.
 * Los datos vienen de la API (ver Stores/useAdviceStore.ts).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'nav.advice', href: indicaciones() }] },
});

const auth = useAuthStore();
const crew = useCrewStore();
const store = useAdviceStore();
const alerts = useAlertsStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const composing = ref(false);
const filter = ref<AdviceFilter>('all');
const astronaut = ref<number | null>(null);
const category = ref<AdviceCategory | null>(null);
let timer: ReturnType<typeof setInterval> | undefined;

const missionId = computed({
    get: () => crew.missionId,
    set: (id) => id !== null && crew.selectMission(id),
});

const delay = computed(() =>
    formatDelay(
        ENVIRONMENT_FACTS[crew.mission?.environment ?? 'mars'].delaySeconds /
            alerts.speed,
    ),
);

/** Por tripulante y tema (sin el estado: así las pestañas muestran cuántas hay de cada uno). */
const scoped = computed(() =>
    store.history.filter(
        (item) =>
            (astronaut.value === null ||
                item.astronaut_id === astronaut.value) &&
            (category.value === null || item.category === category.value),
    ),
);

const counts = computed(() => {
    const of = (state: AdviceFilter) =>
        scoped.value.filter(
            (item) => state === 'all' || adviceState(item, store.now) === state,
        ).length;

    return {
        all: of('all'),
        traveling: of('traveling'),
        unread: of('unread'),
        read: of('read'),
    };
});

const visible = computed(() =>
    scoped.value.filter(
        (item) =>
            filter.value === 'all' ||
            adviceState(item, store.now) === filter.value,
    ),
);

const importantUnread = computed(
    () =>
        store.history.filter(
            (item) => item.important && adviceState(item, store.now) !== 'read',
        ).length,
);
const readPct = computed(() =>
    store.history.length
        ? Math.round((counts.value.read / Math.max(1, counts.value.all)) * 100)
        : 0,
);

function nameOf(id: number): string {
    return crew.find(id)?.name ?? t('Dashboard.crewMember');
}

async function send(draft: AdviceDraft, to: number | null): Promise<void> {
    if (to === null) {
        return;
    }

    await store.send(
        to,
        auth.actor?.name ?? t('Cuerpo.member.medicalTeam'),
        draft,
    );
    toast.success(t('Alertas.advicePage.sentTo', { name: nameOf(to) }), {
        description: t('Alertas.advicePage.arrivesIn', { delay: delay.value }),
    });
}

watch(
    () => crew.missionId,
    () => {
        astronaut.value = null;
        void store.loadForCrew(crew.ids);
    },
);

onMounted(async () => {
    if (!auth.can('health.read.any')) {
        router.visit(dashboard());

        return;
    }

    await crew.load(auth.actor?.astronaut_id ?? null);
    await store.loadForCrew(crew.ids);
    ready.value = true;
    timer = setInterval(store.tick, 1000);
    await nextTick();
    reveal();
});

onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <Head :title="t('Alertas.advicePage.pageTitle')" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            data-reveal
        >
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    {{ crew.mission?.name ?? t('Alertas.advicePage.mission') }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('Alertas.advicePage.title') }}
                </h1>
                <p class="mt-2 max-w-2xl text-muted-foreground">
                    {{ t('Alertas.advicePage.subtitle') }}
                </p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <MissionPicker
                    v-if="crew.mission"
                    v-model="missionId"
                    :missions="crew.choices"
                    :locked="crew.locked"
                />
                <Button
                    v-if="auth.can('advice.send') && crew.members.length"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                    @click="composing = true"
                >
                    <ClipboardPen /> {{ t('Alertas.advicePage.newAdvice') }}
                </Button>
            </div>
        </header>

        <template v-if="!ready">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Skeleton v-for="n in 4" :key="n" class="h-32 rounded-2xl" />
            </div>
            <Skeleton class="h-96 rounded-2xl" />
        </template>

        <template v-else-if="crew.mission">
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
                        :label="t('Alertas.advicePage.traveling')"
                        :value="counts.traveling"
                        :hint="t('Alertas.advicePage.travelingHint')"
                        :icon="Clock"
                        tone="sky"
                    />
                    <SummaryStat
                        :label="t('Alertas.advicePage.unread')"
                        :value="counts.unread"
                        :hint="t('Alertas.advicePage.unreadHint')"
                        :icon="Inbox"
                        :tone="counts.unread ? 'red' : 'green'"
                    />
                    <SummaryStat
                        :label="t('Alertas.advicePage.read')"
                        :value="counts.read"
                        :total="counts.all"
                        :hint="
                            t('Alertas.advicePage.readHint', { pct: readPct })
                        "
                        :icon="CheckCheck"
                        tone="green"
                    />
                    <SummaryStat
                        :label="t('Alertas.advicePage.importantUnread')"
                        :value="importantUnread"
                        :hint="t('Alertas.advicePage.importantHint')"
                        :icon="TriangleAlert"
                        :tone="importantUnread ? 'red' : 'green'"
                    />
                </section>

                <div data-reveal>
                    <AdviceFilters
                        v-model:filter="filter"
                        v-model:astronaut="astronaut"
                        v-model:category="category"
                        :counts="counts"
                        :members="crew.members"
                    />
                </div>

                <section
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <AdviceList
                        :items="visible"
                        :now="store.now"
                        :name-of="nameOf"
                        :href-of="(id) => tripulante(id)"
                        :empty="t('Alertas.advicePage.empty')"
                    />
                </section>
            </template>
        </template>

        <AdviceComposer
            v-model:open="composing"
            astronaut-name=""
            :delay="delay"
            :members="crew.members"
            :submit="send"
        />
    </div>
</template>
