<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { assessmentSummary } from '../Evaluacion/Utils/summary';
import { crewRoleLabel } from '../Entorno/Utils/environments';
import { Head, Link, router } from '@inertiajs/vue3';
import { ArrowLeft, BellRing, ClipboardPen } from '@lucide/vue';
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { getInitials } from '@/composables/useInitials';
import { useReveal } from '@/composables/useReveal';
import { alertas, dashboard, tripulacion } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { BodyZoneCode } from '@/types/health';
import AdviceComposer from '../Alertas/Components/AdviceComposer.vue';
import AdviceList from '../Alertas/Components/AdviceList.vue';
import RecommendationCard from '../Alertas/Components/RecommendationCard.vue';
import { useAdviceStore } from '../Alertas/Stores/useAdviceStore';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import IndicatorTrendCard from '../Dashboard/Components/Evolution/IndicatorTrendCard.vue';
import TrendBanner from '../Dashboard/Components/Evolution/TrendBanner.vue';
import { useMyEvolutionStore } from '../Dashboard/Stores/useMyEvolutionStore';
import { useCrewStore } from '../Entorno/Stores/useCrewStore';
import {
    ENVIRONMENT_FACTS,
    formatDelay,
} from '../Entorno/Utils/environmentFacts';
import MyTestsSummary from '../Evaluacion/Components/Psych/MyTestsSummary.vue';
import { useAssessmentStore } from '../Evaluacion/Stores/useAssessmentStore';
import { usePsychStore } from '../Evaluacion/Stores/usePsychStore';
import BodyViewer from './Components/BodyViewer.vue';
import ClinicalNotes from './Components/Notes/ClinicalNotes.vue';
import ZoneDialog from './Components/ZoneDialog.vue';
import { useBodyStore } from './Stores/useBodyStore';

const { t } = useI18n();

/**
 * Ficha del tripulante (/tripulacion/{id}): todo de una persona para el
 * médico. Su cuerpo, lo que viene empeorando, sus indicadores, sus tests y
 * las indicaciones que se le han dado (advice.send para escribir una) y,
 * para el equipo médico, sus notas clínicas privadas (clinical_notes.manage).
 * Los datos vienen de la API (ver los stores de cada módulo).
 */
const props = defineProps<{ astronautId: number }>();

defineOptions({
    layout: { breadcrumbs: [{ title: 'nav.crew', href: tripulacion() }] },
});

const auth = useAuthStore();
const crew = useCrewStore();
const body = useBodyStore();
const assessment = useAssessmentStore();
const evolution = useMyEvolutionStore();
const psych = usePsychStore();
const advice = useAdviceStore();
const alerts = useAlertsStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const composing = ref(false);
const zone = ref<{ open: boolean; code: BodyZoneCode | null }>({
    open: false,
    code: null,
});
let timer: ReturnType<typeof setInterval> | undefined;

const member = computed(() => crew.find(props.astronautId));
const day = computed(() => crew.mission?.current_day ?? 1);
const delay = computed(() =>
    formatDelay(
        ENVIRONMENT_FACTS[crew.mission?.environment ?? 'mars'].delaySeconds /
            alerts.speed,
    ),
);
const pending = computed(
    () => alerts.history.filter((alert) => !alert.acknowledged_at).length,
);
const status = computed(() =>
    body.status && zone.value.code ? body.status[zone.value.code] : null,
);
const part = computed(
    () => body.parts.find((item) => item.code === zone.value.code) ?? null,
);

async function sendAdvice(
    draft: Parameters<typeof advice.send>[2],
): Promise<void> {
    await advice.send(
        props.astronautId,
        auth.actor?.name ?? t('Cuerpo.member.medicalTeam'),
        draft,
    );
    toast.success(t('Cuerpo.member.adviceSent'), {
        description: t('Cuerpo.member.arrivesIn', { delay: delay.value }),
    });
}

onMounted(async () => {
    if (!auth.can('health.read.any')) {
        router.visit(dashboard());

        return;
    }

    await crew.load(auth.actor?.astronaut_id ?? null);
    const mission = crew.missionOf(props.astronautId);

    if (mission !== null) {
        crew.selectMission(mission);
    }

    await Promise.all([
        body.load(props.astronautId),
        assessment.load(props.astronautId),
        evolution.load(props.astronautId, day.value),
        psych.load(),
        advice.load(props.astronautId),
        alerts.loadHistory(props.astronautId),
    ]);
    ready.value = true;
    timer = setInterval(advice.tick, 1000);
    await nextTick();
    reveal();
});

onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <Head :title="member?.name ?? t('Cuerpo.member.crewMember')" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <Link
            :href="tripulacion()"
            class="inline-flex w-fit items-center gap-1.5 text-sm text-muted-foreground hover:text-foreground"
        >
            <ArrowLeft class="size-4" /> {{ t('Cuerpo.member.back') }}
        </Link>

        <template v-if="!ready || !member">
            <Skeleton class="h-32 rounded-2xl" />
            <Skeleton class="h-96 rounded-2xl" />
        </template>

        <template v-else>
            <header
                class="flex flex-col gap-4 rounded-2xl border bg-linear-to-br from-sky-500/10 via-card to-card p-6 sm:flex-row sm:items-center"
                data-reveal
            >
                <span
                    class="flex size-16 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-xl font-semibold text-white"
                    aria-hidden="true"
                >
                    {{ getInitials(member.name) }}
                </span>
                <div class="min-w-0 flex-1">
                    <h1
                        class="text-2xl font-semibold tracking-tight sm:text-3xl"
                    >
                        {{ member.name }}
                    </h1>
                    <p class="text-muted-foreground">
                        {{
                            t('Cuerpo.member.subtitle', {
                                role: crewRoleLabel(member.crew_role),
                                mission: crew.mission?.name ?? '',
                                day,
                            })
                        }}
                    </p>
                    <p class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                        <strong class="text-2xl tabular-nums">{{
                            assessment.current?.score ?? '—'
                        }}</strong>
                        <span class="text-muted-foreground">/100</span>
                        <StatusBadge
                            :status="assessment.current?.status ?? null"
                            size="md"
                        />
                        <span class="text-muted-foreground">{{
                            assessment.current
                                ? assessmentSummary(
                                      assessment.current.body_status,
                                  )
                                : t('Cuerpo.member.noMeasurements')
                        }}</span>
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Button as-child variant="outline">
                        <Link :href="alertas()"
                            ><BellRing />
                            {{
                                t('Cuerpo.member.alerts', { n: pending })
                            }}</Link
                        >
                    </Button>
                    <Button
                        v-if="auth.can('advice.send')"
                        class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                        @click="composing = true"
                    >
                        <ClipboardPen /> {{ t('Cuerpo.member.newAdvice') }}
                    </Button>
                </div>
            </header>

            <div data-reveal>
                <TrendBanner
                    :items="evolution.worsening"
                    :who="member.name.split(' ')[0]"
                />
            </div>

            <div
                class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]"
            >
                <section
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <h2 class="text-lg font-semibold">
                        {{ t('Cuerpo.member.theirBody') }}
                    </h2>
                    <p class="mb-3 text-sm text-muted-foreground">
                        {{ t('Cuerpo.member.theirBodyHint') }}
                    </p>
                    <BodyViewer
                        v-if="body.status"
                        :parts="body.parts"
                        :status="body.status"
                        :selected="zone.open ? zone.code : null"
                        @select="zone = { open: true, code: $event }"
                    />
                </section>
                <section
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <h2 class="mb-3 text-lg font-semibold">
                        {{ t('Cuerpo.member.doctorAdvice') }}
                    </h2>
                    <AdviceList :items="advice.items" :now="advice.now" />
                </section>
            </div>

            <div v-if="auth.can('clinical_notes.manage')" data-reveal>
                <ClinicalNotes
                    :astronaut-id="astronautId"
                    :author="auth.actor?.name ?? t('Cuerpo.member.medicalTeam')"
                />
            </div>

            <section class="flex flex-col gap-3" data-reveal>
                <h2 class="text-lg font-semibold">
                    {{ t('Cuerpo.member.theirIndicators') }}
                </h2>
                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                    <IndicatorTrendCard
                        v-for="history in evolution.histories"
                        :key="history.code"
                        :history="history"
                        :selected="false"
                    />
                </div>
            </section>

            <section class="flex flex-col gap-3" data-reveal>
                <h2 class="text-lg font-semibold">
                    {{ t('Cuerpo.member.theirTests') }}
                </h2>
                <MyTestsSummary
                    :tests="psych.tests"
                    :history="(testId) => psych.history(astronautId, testId)"
                    :is-due="(test) => psych.isDue(astronautId, test, day)"
                    :own="false"
                />
            </section>
        </template>

        <ZoneDialog
            v-model:open="zone.open"
            :astronaut-name="member?.name ?? ''"
            :part="part"
            :zone="status"
        >
            <template
                v-if="status && alerts.recommendationsFor(status.values).length"
                #recommendation
            >
                <div class="flex flex-col gap-3">
                    <RecommendationCard
                        v-for="item in alerts.recommendationsFor(status.values)"
                        :key="item.title"
                        :recommendation="item"
                    />
                </div>
            </template>
        </ZoneDialog>
        <AdviceComposer
            v-model:open="composing"
            :astronaut-name="member?.name ?? ''"
            :delay="delay"
            :submit="sendAdvice"
        />
    </div>
</template>
