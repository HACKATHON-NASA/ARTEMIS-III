<script setup lang="ts">
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { MaybeStatus } from '@/types/health';

/**
 * Una mini gráfica de línea (sin ejes) para ver de un vistazo cómo viene un
 * valor. El último punto lleva el color de su estado. La usan varios
 * módulos (norma: lo compartido va en components/).
 */
const props = withDefaults(
    defineProps<{ values: number[]; status?: MaybeStatus; height?: number }>(),
    { status: null, height: 36 },
);

const WIDTH = 120;

const geometry = computed(() => {
    const values = props.values;

    if (values.length < 2) {
        return null;
    }

    const min = Math.min(...values);
    const max = Math.max(...values);
    const span = max - min || 1;
    const pad = 3;
    const points = values.map((value, index) => [
        (index / (values.length - 1)) * (WIDTH - pad * 2) + pad,
        props.height - pad - ((value - min) / span) * (props.height - pad * 2),
    ]);

    return {
        path: points
            .map(
                ([x, y], i) =>
                    `${i ? 'L' : 'M'}${x.toFixed(1)},${y.toFixed(1)}`,
            )
            .join(''),
        last: points[points.length - 1],
    };
});
</script>

<template>
    <svg
        v-if="geometry"
        :viewBox="`0 0 ${WIDTH} ${height}`"
        :height="height"
        preserveAspectRatio="none"
        class="w-full overflow-visible"
        aria-hidden="true"
    >
        <path
            :d="geometry.path"
            fill="none"
            class="stroke-sky-500"
            stroke-width="1.75"
            stroke-linejoin="round"
            stroke-linecap="round"
            vector-effect="non-scaling-stroke"
        />
        <circle
            :cx="geometry.last[0]"
            :cy="geometry.last[1]"
            r="3"
            :fill="statusMeta(status).color"
            class="stroke-card"
            stroke-width="1.5"
        />
    </svg>
</template>
