<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { indicatorName } from '@/lib/indicators';
import { Head, router } from '@inertiajs/vue3';
import { BookOpen, Lock } from '@lucide/vue';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, guia } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { IndicatorCode } from '@/types/health';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import { useBodyStore } from '../Cuerpo/Stores/useBodyStore';
import { useMissionStore } from '../Entorno/Stores/useMissionStore';
import { ENVIRONMENTS } from '../Entorno/Utils/environments';
import { useMeasurementsStore } from '../Registro/Stores/useMeasurementsStore';
import GuideCard from './Components/GuideCard.vue';
import GuideTabs from './Components/GuideTabs.vue';
import GuideEditDialog from './Components/GuideEditDialog.vue';
import { useThresholdsStore } from './Stores/useThresholdsStore';
import type {
    GuideAdvice,
    GuideEdit,
    GuideEnvironment,
} from './Types/threshold';

const { t } = useI18n();

/**
 * Guía de salud (/guia, M2 + M5): cada indicador con sus rangos (verde,
 * amarillo, rojo) por entorno y qué hacer en amarillo y en rojo. Todos la
 * consultan; administración y médicos de vuelo (guide.manage) la editan.
 * ⚠️ Por ahora con datos de prueba guardados en este navegador.
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'nav.healthGuide', href: guia() }] },
});

const auth = useAuthStore();
const thresholds = useThresholdsStore();
const alerts = useAlertsStore();
const indicators = useMeasurementsStore();
const body = useBodyStore();
const mission = useMissionStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const environment = ref<GuideEnvironment>('mars');
const editing = ref<IndicatorCode | null>(null);
const dialogOpen = ref(false);

const canEdit = computed(() => auth.can('guide.manage'));
const envName = computed(
    () =>
        ENVIRONMENTS.find((env) => env.code === environment.value)?.name ?? '',
);

function adviceFor(code: IndicatorCode, level: 'yellow' | 'red'): GuideAdvice {
    const found = alerts.recommendationFor(code, level);

    return found
        ? {
              title: found.title,
              steps: found.action_text.split('\n').filter(Boolean),
          }
        : null;
}

/** Cada indicador con su nombre (M1), su zona (M3), sus rangos (M2) y qué hacer (M5). */
const cards = computed(() =>
    indicators.indicators.flatMap((item) => {
        const threshold = thresholds.find(item.code);

        return threshold
            ? [
                  {
                      item,
                      threshold,
                      decimals:
                          item.step < 1
                              ? String(item.step).split('.')[1].length
                              : 0,
                      zoneName:
                          body.parts.find(
                              (part) => part.code === item.body_part,
                          )?.name ?? '',
                      yellow: adviceFor(item.code, 'yellow'),
                      red: adviceFor(item.code, 'red'),
                  },
              ]
            : [];
    }),
);

const current = computed(
    () => cards.value.find((card) => card.item.code === editing.value) ?? null,
);

function edit(code: IndicatorCode): void {
    editing.value = code;
    dialogOpen.value = true;
}

async function save(change: GuideEdit): Promise<void> {
    const code = change.threshold.indicator;
    const levels = [
        ['yellow', change.yellow],
        ['red', change.red],
    ] as const;

    // Primero los rangos (el backend los valida); si fallan, no se toca lo demás.
    await thresholds.save(change.threshold);
    await Promise.all(
        levels.flatMap(([status, advice]) =>
            advice
                ? [
                      alerts.saveRecommendation({
                          indicator: code,
                          status,
                          title: advice.title,
                          action_text: advice.steps.join('\n'),
                          risk_ref:
                              alerts.recommendationFor(code, status)
                                  ?.risk_ref ?? null,
                      }),
                  ]
                : [],
        ),
    );
    toast.success(t('Evaluacion.guide.updated'), {
        description: t('Evaluacion.guide.updatedHint', {
            name: indicatorName(
                current.value?.item.code ?? '',
                current.value?.item.name ?? '',
            ),
            env: envName.value,
        }),
    });
}

watch(environment, (env) => void thresholds.load(env));

onMounted(async () => {
    if (!auth.can('crew.read')) {
        router.visit(dashboard());

        return;
    }

    await mission.load();
    environment.value = mission.current?.environment ?? 'mars';
    await Promise.all([
        thresholds.load(environment.value),
        indicators.loadIndicators(),
        body.loadCrew([]),
    ]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head :title="t('Evaluacion.guide.pageTitle')" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between"
            data-reveal
        >
            <div>
                <p
                    class="flex items-center gap-2 text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    <BookOpen class="size-3.5" />
                    {{ t('Evaluacion.guide.eyebrow') }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('Evaluacion.guide.title') }}
                </h1>
                <p class="mt-2 max-w-2xl text-muted-foreground">
                    {{ t('Evaluacion.guide.subtitle') }}
                </p>
                <p
                    v-if="!canEdit"
                    class="mt-2 flex items-center gap-1.5 text-xs text-muted-foreground"
                >
                    <Lock class="size-3.5" />
                    {{ t('Evaluacion.guide.onlyDoctors') }}
                </p>
            </div>
            <div
                class="inline-flex flex-wrap gap-1 rounded-xl border bg-muted/40 p-1"
                role="tablist"
                :aria-label="t('Evaluacion.guide.environment')"
            >
                <button
                    v-for="env in ENVIRONMENTS"
                    :key="env.code"
                    type="button"
                    role="tab"
                    :aria-selected="environment === env.code"
                    class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium transition"
                    :class="
                        environment === env.code
                            ? 'bg-card text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    @click="environment = env.code"
                >
                    <component :is="env.icon" class="size-4" />
                    {{ env.name }}
                </button>
            </div>
        </header>

        <div data-reveal>
            <GuideTabs
                active="physical"
                :show-results="auth.can('health.read.any')"
            />
        </div>

        <div v-if="!ready" class="grid gap-4 lg:grid-cols-2">
            <Skeleton v-for="n in 4" :key="n" class="h-64 rounded-2xl" />
        </div>

        <div v-else class="grid gap-4 lg:grid-cols-2" data-reveal>
            <GuideCard
                v-for="card in cards"
                :key="card.item.code"
                :name="indicatorName(card.item.code, card.item.name)"
                :unit="card.item.unit"
                :zone-name="card.zoneName"
                :min="card.item.min"
                :max="card.item.max"
                :decimals="card.decimals"
                :threshold="card.threshold"
                :yellow="card.yellow"
                :red="card.red"
                :can-edit="canEdit"
                @edit="edit(card.item.code)"
            />
        </div>

        <GuideEditDialog
            v-if="current"
            v-model:open="dialogOpen"
            :name="indicatorName(current.item.code, current.item.name)"
            :unit="current.item.unit"
            :environment-name="envName"
            :min="current.item.min"
            :max="current.item.max"
            :decimals="current.decimals"
            :threshold="current.threshold"
            :yellow="current.yellow"
            :red="current.red"
            :submit="save"
        />
    </div>
</template>
