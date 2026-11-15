<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Head, router } from '@inertiajs/vue3';
import { Brain, Plus, ShieldCheck } from '@lucide/vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, psicologia } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import { useMissionStore } from '../Entorno/Stores/useMissionStore';
import { useGamificationStore } from '../Gamificacion/Stores/useGamificationStore';
import GuideTabs from './Components/GuideTabs.vue';
import PsychTakeDialog from './Components/Psych/PsychTakeDialog.vue';
import PsychTestCard from './Components/Psych/PsychTestCard.vue';
import PsychTestEditor from './Components/Psych/PsychTestEditor.vue';
import { usePsychStore } from './Stores/usePsychStore';
import type { PsychSubmission, PsychTest } from './Types/psych';

const { t } = useI18n();

/**
 * Guía psicológica (/guia/psicologica, M2): los tests de la tripulación.
 * El astronauta responde los que le tocan; el equipo médico (guide.manage)
 * los crea y edita. Si un test marca una respuesta de riesgo, sale sola una
 * alerta urgente a Tierra (M5).
 * ⚠️ Por ahora con datos de prueba guardados en este navegador.
 */
defineOptions({
    layout: {
        breadcrumbs: [{ title: 'nav.psychGuide', href: psicologia() }],
    },
});

const auth = useAuthStore();
const psych = usePsychStore();
const mission = useMissionStore();
const gamification = useGamificationStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const taking = ref<{ open: boolean; test: PsychTest | null }>({
    open: false,
    test: null,
});
const editing = ref<{ open: boolean; test: PsychTest | null }>({
    open: false,
    test: null,
});

const own = computed(() => auth.actor?.astronaut_id ?? null);
const canEdit = computed(() => auth.can('guide.manage'));
const today = computed(() => mission.current?.day ?? 1);

const pending = computed(() =>
    own.value === null
        ? []
        : psych.tests.filter((test) =>
              psych.isDue(own.value!, test, today.value),
          ),
);

async function submit(submission: PsychSubmission) {
    if (own.value === null || !taking.value.test) {
        return null;
    }

    // El backend puntúa, suma los +10 (M7) y, si hubo una respuesta de
    // riesgo, avisa a Tierra con urgencia (M5). Aquí solo se celebra.
    const result = await psych.submit(taking.value.test.id, submission);

    if (result) {
        await gamification.refresh(own.value);
    }

    return result;
}

async function saveTest(test: PsychTest): Promise<void> {
    const saved = await psych.saveTest(test);

    if (!saved) {
        return;
    }

    toast.success(
        test.id
            ? t('Evaluacion.psych.testUpdated')
            : t('Evaluacion.psych.testCreated'),
        {
            description: saved.name,
        },
    );
}

onMounted(async () => {
    if (!auth.can('crew.read')) {
        router.visit(dashboard());

        return;
    }

    await Promise.all([psych.load(), mission.load()]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head :title="t('Evaluacion.psych.pageTitle')" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header class="flex flex-col gap-4" data-reveal>
            <div>
                <p
                    class="flex items-center gap-2 text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    <Brain class="size-3.5" />
                    {{ t('Evaluacion.psych.eyebrow') }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('Evaluacion.psych.title') }}
                </h1>
                <p class="mt-2 max-w-2xl text-muted-foreground">
                    {{ t('Evaluacion.psych.subtitle') }}
                </p>
            </div>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <GuideTabs
                    active="psych"
                    :show-results="auth.can('health.read.any')"
                />
                <Button
                    v-if="canEdit"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                    @click="editing = { open: true, test: null }"
                >
                    <Plus /> {{ t('Evaluacion.psych.newTest') }}
                </Button>
            </div>
        </header>

        <section
            v-if="own !== null && ready"
            class="flex items-center gap-3 rounded-2xl border p-4"
            :class="pending.length ? 'border-sky-500/40 bg-sky-500/5' : ''"
            data-reveal
        >
            <ShieldCheck class="size-5 shrink-0 text-sky-500" />
            <p class="text-sm">
                <template v-if="pending.length">
                    {{
                        t(
                            'Evaluacion.psych.pending',
                            { n: pending.length },
                            pending.length,
                        )
                    }}
                </template>
                <template v-else>{{ t('Evaluacion.psych.upToDate') }}</template>
                <span class="block text-xs text-muted-foreground">
                    {{ t('Evaluacion.psych.private') }}
                </span>
            </p>
        </section>

        <div v-if="!ready" class="grid gap-4 lg:grid-cols-2">
            <Skeleton v-for="n in 4" :key="n" class="h-64 rounded-2xl" />
        </div>

        <div v-else class="grid gap-4 lg:grid-cols-2" data-reveal>
            <PsychTestCard
                v-for="test in psych.tests"
                :key="test.id"
                :test="test"
                :due="own === null ? null : psych.isDue(own, test, today)"
                :last="own === null ? null : psych.latest(own, test.id)"
                :can-edit="canEdit"
                @take="taking = { open: true, test }"
                @edit="editing = { open: true, test: psych.original(test.id) }"
            />
        </div>

        <PsychTakeDialog
            v-model:open="taking.open"
            :test="taking.test"
            :submit="submit"
        />
        <PsychTestEditor
            v-model:open="editing.open"
            :test="editing.test"
            :submit="saveTest"
        />
    </div>
</template>
