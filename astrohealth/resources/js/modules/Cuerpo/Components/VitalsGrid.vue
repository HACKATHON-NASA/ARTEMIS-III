<script setup lang="ts">
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { BodyZoneCode } from '@/types/health';
import type { BodyStatus, ZoneValue } from '../Types/body';
import { INDICATOR_NAMES } from '../Utils/indicators';

/**
 * Los últimos valores de todos los indicadores, en tarjetas pequeñas.
 * Clic → abre la zona del cuerpo a la que pertenece.
 */
const props = defineProps<{ status: BodyStatus }>();
const emit = defineEmits<{ select: [code: BodyZoneCode] }>();

const items = computed(() =>
    (
        Object.entries(props.status) as [
            BodyZoneCode,
            BodyStatus[BodyZoneCode],
        ][]
    ).flatMap(([zone, data]) =>
        data.values.map((value: ZoneValue) => ({ zone, value })),
    ),
);
</script>

<template>
    <ul class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
        <li v-for="{ zone, value } in items" :key="value.indicator">
            <button
                type="button"
                class="group w-full rounded-xl border bg-card p-3 text-left transition hover:-translate-y-0.5 hover:border-sky-500/40 focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
                @click="emit('select', zone)"
            >
                <span class="flex items-center justify-between gap-2">
                    <span class="truncate text-xs text-muted-foreground">
                        {{ INDICATOR_NAMES[value.indicator] }}
                    </span>
                    <span
                        class="size-2 shrink-0 rounded-full"
                        :style="{
                            backgroundColor: statusMeta(value.status).color,
                            boxShadow: `0 0 8px ${statusMeta(value.status).color}`,
                        }"
                        :aria-label="statusMeta(value.status).label"
                    />
                </span>
                <span class="mt-1 block text-xl font-semibold tabular-nums">
                    {{ value.value }}
                    <span class="text-xs font-normal text-muted-foreground">
                        {{ value.unit }}
                    </span>
                </span>
            </button>
        </li>
    </ul>
</template>
