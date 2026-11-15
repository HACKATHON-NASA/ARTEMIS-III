<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { formatNumber } from '@/lib/i18n';
import { Pencil } from '@lucide/vue';
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { GuideAdvice, Threshold } from '../Types/threshold';
import RangeBar from './RangeBar.vue';

const { t } = useI18n();

/**
 * Un indicador en la Guía de salud: su rango sano, la barra del semáforo y
 * qué hacer en amarillo y en rojo. Quien puede, lo edita.
 */
const props = defineProps<{
    name: string;
    unit: string;
    zoneName: string;
    min: number;
    max: number;
    decimals: number;
    threshold: Threshold;
    yellow: GuideAdvice;
    red: GuideAdvice;
    canEdit: boolean;
}>();

defineEmits<{ edit: [] }>();

const levels = computed(() => [
    { level: 'yellow' as const, advice: props.yellow },
    { level: 'red' as const, advice: props.red },
]);
</script>

<template>
    <article class="flex flex-col gap-4 rounded-2xl border bg-card p-5">
        <header class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-semibold">{{ name }}</h2>
                <p class="text-xs text-muted-foreground">
                    {{ zoneName }} · {{ unit }}
                </p>
            </div>
            <button
                v-if="canEdit"
                type="button"
                class="inline-flex shrink-0 items-center gap-1.5 rounded-lg border px-2.5 py-1 text-xs font-medium text-muted-foreground transition hover:border-sky-500/50 hover:text-foreground"
                :aria-label="t('Evaluacion.guide.editWho', { name })"
                @click="$emit('edit')"
            >
                <Pencil class="size-3.5" />
                {{ t('common.edit') }}
            </button>
        </header>

        <div>
            <p class="mb-2 text-sm">
                <i18n-t
                    keypath="Evaluacion.guide.healthyBetween"
                    scope="global"
                >
                    <template #min>
                        <strong class="tabular-nums">{{
                            formatNumber(threshold.green_min)
                        }}</strong>
                    </template>
                    <template #max>
                        <strong class="tabular-nums">{{
                            formatNumber(threshold.green_max)
                        }}</strong>
                    </template>
                    <template #unit>{{ unit }}</template>
                </i18n-t>
            </p>
            <RangeBar
                :threshold="threshold"
                :min="min"
                :max="max"
                :decimals="decimals"
            />
        </div>

        <div class="grid gap-3 sm:grid-cols-2">
            <div
                v-for="{ level, advice } in levels"
                :key="level"
                class="rounded-xl border p-3"
                :style="{
                    borderColor: `color-mix(in srgb, ${statusMeta(level).color} 35%, transparent)`,
                }"
            >
                <p class="flex items-center gap-1.5 text-xs font-semibold">
                    <span
                        class="size-2 rounded-full"
                        :style="{ background: statusMeta(level).color }"
                    />
                    {{
                        t('Evaluacion.guide.ifComesOut', {
                            status: statusMeta(level).label.toLowerCase(),
                        })
                    }}
                </p>
                <template v-if="advice">
                    <p class="mt-1.5 text-sm font-medium">{{ advice.title }}</p>
                    <ol
                        class="mt-1 list-decimal space-y-0.5 pl-4 text-xs text-muted-foreground"
                    >
                        <li v-for="step in advice.steps" :key="step">
                            {{ step }}
                        </li>
                    </ol>
                </template>
                <p v-else class="mt-1.5 text-xs text-muted-foreground italic">
                    {{ t('Evaluacion.guide.noSteps') }}
                </p>
            </div>
        </div>
    </article>
</template>
