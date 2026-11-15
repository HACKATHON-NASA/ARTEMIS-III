<script setup lang="ts">
import { ArrowDownRight, ArrowRight, ArrowUpRight } from '@lucide/vue';
import { computed } from 'vue';
import Sparkline from '@/components/Sparkline.vue';
import StatusBadge from '@/components/StatusBadge.vue';
import type { IndicatorHistory } from '../../Stores/useMyEvolutionStore';
import { formatValue, indicatorMeta } from '../../Utils/indicatorMeta';

/**
 * Un indicador en "Mi evolución": el valor de hoy con su color, la flecha de
 * la semana (roja si empeora) y la mini gráfica de los últimos 14 días.
 * Tocarla lo abre en la gráfica grande.
 */
const props = defineProps<{ history: IndicatorHistory; selected: boolean }>();

defineEmits<{ select: [] }>();

const meta = computed(() => indicatorMeta(props.history.code));
const last = computed(() => props.history.points.at(-1) ?? null);
const arrow = computed(() =>
    props.history.trend?.direction === 'up'
        ? ArrowUpRight
        : props.history.trend?.direction === 'down'
          ? ArrowDownRight
          : ArrowRight,
);
</script>

<template>
    <button
        type="button"
        class="flex w-full flex-col gap-2 rounded-2xl border bg-card p-4 text-left transition hover:border-sky-500/40 focus-visible:ring-[3px] focus-visible:ring-sky-500/25 focus-visible:outline-none"
        :class="{ 'border-sky-500 ring-1 ring-sky-500': selected }"
        :aria-pressed="selected"
        @click="$emit('select')"
    >
        <div class="flex items-start justify-between gap-2">
            <p class="text-sm font-medium">{{ meta.label }}</p>
            <StatusBadge :status="last?.status ?? null" />
        </div>
        <div class="flex items-end justify-between gap-2">
            <p class="text-2xl font-semibold tabular-nums">
                {{ last ? formatValue(last.value, history.code) : '—' }}
                <span class="text-sm font-normal text-muted-foreground">{{
                    meta.unit
                }}</span>
            </p>
            <component
                :is="arrow"
                class="size-5"
                :class="
                    history.trend?.worse
                        ? 'text-[var(--status-red)]'
                        : history.trend?.direction === 'stable' ||
                            !history.trend
                          ? 'text-muted-foreground'
                          : 'text-[var(--status-green)]'
                "
                :aria-label="
                    history.trend?.direction === 'stable' || !history.trend
                        ? 'estable'
                        : history.trend.worse
                          ? 'empeorando'
                          : 'mejorando'
                "
            />
        </div>
        <Sparkline
            :values="history.points.slice(-14).map((p) => p.value)"
            :status="last?.status ?? null"
        />
    </button>
</template>
