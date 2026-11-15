<script setup lang="ts">
import gsap from 'gsap';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { statusMeta } from '@/lib/status';
import type { Flight } from '../Types/measurement';

/**
 * Los valores recién guardados "vuelan" desde su campo hasta su órgano
 * (GSAP): suben en arco, se encogen y se funden al llegar. Es el momento de
 * la demo de M1. Con "reducir movimiento", solo un fundido corto.
 */
const props = defineProps<{ flights: Flight[] }>();
const emit = defineEmits<{ landed: []; done: [] }>();

const chips = ref<HTMLElement[]>([]);
let timeline: gsap.core.Timeline | null = null;

onMounted(() => {
    const reduced = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;

    timeline = gsap.timeline({ onComplete: () => emit('done') });

    if (reduced) {
        timeline.to(chips.value, { opacity: 0, duration: 0.4, delay: 0.3 });
        timeline.call(() => emit('landed'), [], 0.3);

        return;
    }

    props.flights.forEach((flight, index) => {
        const chip = chips.value[index];
        const dx = flight.to.x - flight.from.x;
        const dy = flight.to.y - flight.from.y;
        const at = index * 0.07;

        timeline!
            .fromTo(
                chip,
                { opacity: 0, scale: 0.6 },
                { opacity: 1, scale: 1, duration: 0.25, ease: 'back.out(2)' },
                at,
            )
            .to(chip, { x: dx, duration: 0.85, ease: 'power1.inOut' }, at + 0.2)
            .to(
                chip,
                {
                    keyframes: [
                        { y: Math.min(dy, 0) - 70, ease: 'power2.out' },
                        { y: dy, ease: 'power2.in' },
                    ],
                    duration: 0.85,
                },
                at + 0.2,
            )
            .to(
                chip,
                { scale: 0.3, opacity: 0, duration: 0.25, ease: 'power2.in' },
                at + 0.85,
            );
    });

    timeline.call(() => emit('landed'), [], 0.95);
});

onBeforeUnmount(() => timeline?.kill());
</script>

<template>
    <Teleport to="body">
        <div class="pointer-events-none fixed inset-0 z-50" aria-hidden="true">
            <span
                v-for="flight in flights"
                :key="flight.id"
                ref="chips"
                class="absolute -translate-x-1/2 -translate-y-1/2 rounded-full px-3 py-1 text-sm font-semibold whitespace-nowrap text-white tabular-nums shadow-lg"
                :style="{
                    left: `${flight.from.x}px`,
                    top: `${flight.from.y}px`,
                    background: statusMeta(flight.status).color,
                    boxShadow: `0 0 24px ${statusMeta(flight.status).color}`,
                }"
            >
                {{ flight.label }}
            </span>
        </div>
    </Teleport>
</template>
