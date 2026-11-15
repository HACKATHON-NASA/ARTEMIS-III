<script setup lang="ts">
import { computed } from 'vue';
import { needsAttention, statusMeta } from '@/lib/status';
import type { MaybeStatus } from '@/types/health';
import type { ZoneGeometry } from '../Utils/zones';

/**
 * Una zona del cuerpo: se pinta con el color de su estado y, si está en
 * amarillo o rojo, late (un signo vital). Se puede usar con el teclado:
 * Tab la enfoca y Enter abre su panel.
 */
const props = defineProps<{
    zone: ZoneGeometry;
    name: string;
    status: MaybeStatus;
    selected: boolean;
}>();

const emit = defineEmits<{ select: []; hover: [active: boolean] }>();

const meta = computed(() => statusMeta(props.status));
const pulses = computed(() => needsAttention(props.status));
</script>

<template>
    <g
        role="button"
        tabindex="0"
        class="zone cursor-pointer outline-none"
        :class="{ 'zone--pulse': pulses, 'zone--selected': selected }"
        :style="{ '--zone-color': meta.color }"
        :aria-label="`${name}: ${meta.label}`"
        :aria-pressed="selected"
        @click="emit('select')"
        @keydown.enter.prevent="emit('select')"
        @keydown.space.prevent="emit('select')"
        @mouseenter="emit('hover', true)"
        @mouseleave="emit('hover', false)"
        @focus="emit('hover', true)"
        @blur="emit('hover', false)"
    >
        <circle
            :cx="zone.hit.cx"
            :cy="zone.hit.cy"
            :r="zone.hit.r"
            fill="transparent"
        />
        <g class="zone__shape">
            <template v-for="(shape, index) in zone.shapes" :key="index">
                <ellipse
                    v-if="shape.kind === 'ellipse'"
                    :cx="shape.cx"
                    :cy="shape.cy"
                    :rx="shape.rx"
                    :ry="shape.ry"
                />
                <path v-else :d="shape.d" />
            </template>
        </g>
    </g>
</template>

<style scoped>
.zone__shape {
    fill: var(--zone-color);
    fill-opacity: 0.45;
    stroke: var(--zone-color);
    stroke-width: 1.5;
    filter: drop-shadow(0 0 6px var(--zone-color));
    transform-box: fill-box;
    transform-origin: center;
    transition:
        fill-opacity 0.25s ease,
        stroke-width 0.25s ease;
}

.zone:hover .zone__shape,
.zone:focus-visible .zone__shape,
.zone--selected .zone__shape {
    fill-opacity: 0.75;
    stroke-width: 2.5;
}

.zone:focus-visible .zone__shape {
    stroke: #e0f2fe;
}

.zone--pulse .zone__shape {
    animation: zone-pulse 1.6s ease-in-out infinite;
}

@keyframes zone-pulse {
    50% {
        fill-opacity: 0.8;
        transform: scale(1.08);
    }
}

@media (prefers-reduced-motion: reduce) {
    .zone--pulse .zone__shape {
        animation: none;
    }
}
</style>
