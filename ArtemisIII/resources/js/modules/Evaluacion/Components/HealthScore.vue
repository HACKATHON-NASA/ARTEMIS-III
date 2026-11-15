<script setup lang="ts">
import gsap from 'gsap';
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { statusMeta } from '@/lib/status';
import type { Assessment } from '../Types/assessment';

/**
 * El puntaje general 0–100 (M2): un anillo que se llena hasta el valor, con
 * el color del estado que calculó el backend. El número cuenta al aparecer.
 */
const props = defineProps<{ assessment: Assessment }>();

const RADIUS = 66;
const CIRCUMFERENCE = 2 * Math.PI * RADIUS;

const shown = ref(0);
const color = computed(() => statusMeta(props.assessment.status).color);
const offset = computed(() => CIRCUMFERENCE * (1 - shown.value / 100));

let tween: gsap.core.Tween | null = null;

function animateTo(score: number): void {
    tween?.kill();

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        shown.value = score;

        return;
    }

    const state = { value: shown.value };
    tween = gsap.to(state, {
        value: score,
        duration: 1.6,
        ease: 'power3.out',
        onUpdate: () => {
            shown.value = state.value;
        },
    });
}

onMounted(() => animateTo(props.assessment.score));
watch(() => props.assessment.score, animateTo);
onBeforeUnmount(() => tween?.kill());
</script>

<template>
    <div
        class="flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left"
    >
        <div class="relative size-40 shrink-0">
            <svg viewBox="0 0 160 160" class="size-full -rotate-90">
                <circle
                    cx="80"
                    cy="80"
                    :r="RADIUS"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="12"
                    class="text-muted"
                />
                <circle
                    cx="80"
                    cy="80"
                    :r="RADIUS"
                    fill="none"
                    :stroke="color"
                    stroke-width="12"
                    stroke-linecap="round"
                    :stroke-dasharray="CIRCUMFERENCE"
                    :stroke-dashoffset="offset"
                    :style="{ filter: `drop-shadow(0 0 6px ${color})` }"
                />
            </svg>
            <div
                class="absolute inset-0 flex flex-col items-center justify-center"
            >
                <span class="text-4xl font-semibold tabular-nums">
                    {{ Math.round(shown) }}
                </span>
                <span class="text-xs text-muted-foreground">de 100</span>
            </div>
        </div>

        <div class="flex flex-col items-center gap-2 sm:items-start">
            <p class="text-sm font-medium text-muted-foreground">
                Estado general
            </p>
            <StatusBadge :status="assessment.status" size="md" />
            <p class="max-w-xs text-sm">{{ assessment.summary }}</p>
        </div>
    </div>
</template>
