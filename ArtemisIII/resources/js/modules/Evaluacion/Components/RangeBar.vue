<script setup lang="ts">
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { Threshold } from '../Types/threshold';

/**
 * Los rangos de un indicador en una barra: rojo · amarillo · verde ·
 * amarillo · rojo, con los números en cada corte. Los colores son los del
 * semáforo (--status-*).
 */
const props = defineProps<{
    threshold: Threshold;
    /** Rango posible del indicador (para no salirse de lo real). */
    min: number;
    max: number;
    decimals: number;
}>();

const domain = computed(() => {
    const { yellow_min: low, yellow_max: high } = props.threshold;
    const pad = (high - low || 1) * 0.25;

    return [Math.max(props.min, low - pad), Math.min(props.max, high + pad)];
});

const pct = (value: number) => {
    const [lo, hi] = domain.value;

    return ((Math.min(hi, Math.max(lo, value)) - lo) / (hi - lo || 1)) * 100;
};

const segments = computed(() => {
    const t = props.threshold;
    const [lo, hi] = domain.value;
    const cuts: [number, number, 'red' | 'yellow' | 'green'][] = [
        [lo, t.yellow_min, 'red'],
        [t.yellow_min, t.green_min, 'yellow'],
        [t.green_min, t.green_max, 'green'],
        [t.green_max, t.yellow_max, 'yellow'],
        [t.yellow_max, hi, 'red'],
    ];

    return cuts
        .map(([from, to, status]) => ({
            status,
            left: pct(from),
            width: pct(to) - pct(from),
        }))
        .filter((segment) => segment.width > 0.5);
});

const ticks = computed(() => {
    const t = props.threshold;
    const values = [
        ...new Set([t.yellow_min, t.green_min, t.green_max, t.yellow_max]),
    ];

    return values
        .filter((value) => value > domain.value[0] && value < domain.value[1])
        .map((value) => ({
            value,
            left: pct(value),
            label: value.toLocaleString('es', {
                maximumFractionDigits: props.decimals,
            }),
        }));
});
</script>

<template>
    <div
        class="pt-1 pb-5"
        role="img"
        :aria-label="`Verde de ${threshold.green_min} a ${threshold.green_max}; amarillo de ${threshold.yellow_min} a ${threshold.yellow_max}; fuera de eso, rojo`"
    >
        <div class="relative h-3 overflow-hidden rounded-full bg-muted">
            <span
                v-for="segment in segments"
                :key="`${segment.status}-${segment.left}`"
                class="absolute inset-y-0 border-x border-card"
                :style="{
                    left: `${segment.left}%`,
                    width: `${segment.width}%`,
                    background: statusMeta(segment.status).color,
                }"
            />
        </div>
        <div class="relative">
            <span
                v-for="tick in ticks"
                :key="tick.value"
                class="absolute top-1 -translate-x-1/2 text-[11px] text-muted-foreground tabular-nums"
                :style="{ left: `${tick.left}%` }"
            >
                {{ tick.label }}
            </span>
        </div>
    </div>
</template>
