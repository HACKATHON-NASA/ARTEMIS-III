<script setup lang="ts">
import StatusBadge from '@/components/StatusBadge.vue';
import type { MaybeStatus } from '@/types/health';
import type { ZoneValue } from '../Types/body';
import { INDICATOR_NAMES } from '../Utils/indicators';

/**
 * El globo de una zona: su nombre, su estado y el valor que más pesa
 * (el peor de sus indicadores).
 */
defineProps<{
    name: string;
    status: MaybeStatus;
    value: ZoneValue | null;
    x: number;
    y: number;
}>();
</script>

<template>
    <div
        class="pointer-events-none absolute z-10 w-max max-w-56 -translate-x-1/2 -translate-y-[calc(100%+10px)] rounded-xl border bg-popover/95 px-3 py-2 text-sm shadow-xl backdrop-blur"
        :style="{ left: `${x}%`, top: `${y}%` }"
        role="tooltip"
    >
        <div class="flex items-center gap-2">
            <span class="font-medium">{{ name }}</span>
            <StatusBadge :status="status" />
        </div>
        <p v-if="value" class="mt-1 text-muted-foreground">
            {{ INDICATOR_NAMES[value.indicator] }}:
            <strong class="text-foreground tabular-nums">
                {{ value.value }} {{ value.unit }}
            </strong>
        </p>
        <p v-else class="mt-1 text-muted-foreground">Sin mediciones todavía</p>
    </div>
</template>
