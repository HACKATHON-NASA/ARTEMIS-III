<script setup lang="ts">
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { Recommendation } from '../Types/alerts';

/**
 * Qué hacer, paso a paso, ante un valor en amarillo o rojo.
 */
const props = defineProps<{ recommendation: Recommendation }>();

const steps = computed(() =>
    props.recommendation.action_text.split('\n').filter(Boolean),
);
const color = computed(() => statusMeta(props.recommendation.status).color);
</script>

<template>
    <div
        class="rounded-xl border p-4"
        :style="{
            borderColor: `color-mix(in srgb, ${color} 35%, transparent)`,
        }"
    >
        <p class="font-semibold" :style="{ color }">
            {{ recommendation.title }}
        </p>
        <ol class="mt-3 flex flex-col gap-2.5">
            <li
                v-for="(step, index) in steps"
                :key="step"
                class="flex items-start gap-3 text-sm"
            >
                <span
                    class="flex size-6 shrink-0 items-center justify-center rounded-full text-xs font-semibold text-white"
                    :style="{ backgroundColor: color }"
                >
                    {{ index + 1 }}
                </span>
                <span class="pt-0.5">{{ step }}</span>
            </li>
        </ol>
    </div>
</template>
