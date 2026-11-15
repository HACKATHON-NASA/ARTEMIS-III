<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { resultBand } from '../../Utils/psychText';
import { Link } from '@inertiajs/vue3';
import { Brain, Zap } from '@lucide/vue';
import Sparkline from '@/components/Sparkline.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { psicologia } from '@/routes';
import type { PsychResult, PsychTest } from '../../Types/psych';

const { t } = useI18n();

/**
 * Mis tests en "Mi evolución": cada test con mi último resultado, su nivel
 * y cómo ha ido en la misión. Si me toca hacerlo, lleva a la guía.
 */
withDefaults(
    defineProps<{
        tests: PsychTest[];
        history: (testId: number) => PsychResult[];
        isDue: (test: PsychTest) => boolean;
        /** false = lo ve el médico: "Pendiente esta semana", sin enlace. */
        own?: boolean;
    }>(),
    { own: true },
);
</script>

<template>
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <article
            v-for="test in tests"
            :key="test.id"
            class="flex flex-col gap-2 rounded-2xl border bg-card p-4"
        >
            <div class="flex items-center gap-2">
                <component
                    :is="test.kind === 'cognitive' ? Zap : Brain"
                    class="size-4 text-sky-500"
                />
                <p class="truncate text-sm font-medium">{{ test.name }}</p>
            </div>
            <template v-if="history(test.id).length">
                <div class="flex items-center justify-between gap-2">
                    <p class="text-2xl font-semibold tabular-nums">
                        {{ history(test.id).at(-1)!.score }}
                        <span
                            class="text-sm font-normal text-muted-foreground"
                            >{{ test.unit }}</span
                        >
                    </p>
                    <StatusBadge :status="history(test.id).at(-1)!.status" />
                </div>
                <p class="text-xs text-muted-foreground">
                    {{
                        t('Evaluacion.psych.bandDay', {
                            band: resultBand(test, history(test.id).at(-1)!),
                            day: history(test.id).at(-1)!.day,
                        })
                    }}
                </p>
                <Sparkline
                    :values="history(test.id).map((r) => r.score)"
                    :status="history(test.id).at(-1)!.status"
                />
            </template>
            <p v-else class="text-sm text-muted-foreground">
                {{ t('Evaluacion.psych.notYet') }}
            </p>
            <p
                v-if="isDue(test) && !own"
                class="mt-auto text-xs font-medium text-amber-600 dark:text-amber-300"
            >
                {{ t('Evaluacion.psych.pendingWeek') }}
            </p>
            <Link
                v-else-if="isDue(test)"
                :href="psicologia()"
                class="mt-auto text-xs font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
                {{ t('Evaluacion.psych.dueWeek') }}
            </Link>
        </article>
    </div>
</template>
