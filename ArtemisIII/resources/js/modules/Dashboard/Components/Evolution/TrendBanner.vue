<script setup lang="ts">
import { CircleCheck, TrendingDown, TrendingUp } from '@lucide/vue';
import type { IndicatorHistory } from '../../Stores/useMyEvolutionStore';
import { formatValue, indicatorMeta } from '../../Utils/indicatorMeta';

/**
 * Los avisos de tendencia (M4): lo que viene empeorando esta semana, aunque
 * todavía esté en verde. Tocar uno lo abre en la gráfica. Si nada empeora,
 * lo dice (también es una buena noticia).
 */
withDefaults(
    defineProps<{
        items: IndicatorHistory[];
        /** Nombre del tripulante cuando lo ve el médico (si no, "Tu …"). */
        who?: string | null;
    }>(),
    { who: null },
);

defineEmits<{ select: [code: IndicatorHistory['code']] }>();
</script>

<template>
    <section
        v-if="items.length"
        class="flex flex-col gap-2 rounded-2xl border border-[var(--status-yellow)]/40 bg-[var(--status-yellow)]/8 p-4 sm:p-5"
        role="status"
    >
        <p class="text-sm font-semibold">Ojo con esto esta semana</p>
        <button
            v-for="item in items"
            :key="item.code"
            type="button"
            class="flex items-center gap-3 rounded-lg px-2 py-1.5 text-left text-sm transition hover:bg-background/60"
            @click="$emit('select', item.code)"
        >
            <component
                :is="
                    item.trend?.direction === 'down' ? TrendingDown : TrendingUp
                "
                class="size-4 shrink-0 text-[var(--status-red)]"
            />
            <span>
                <template v-if="who"
                    >{{ indicatorMeta(item.code).label }} de {{ who }}</template
                ><template v-else
                    >Tu
                    {{ indicatorMeta(item.code).label.toLowerCase() }}</template
                >
                viene
                {{ item.trend?.direction === 'down' ? 'bajando' : 'subiendo' }}:
                de
                <strong class="tabular-nums">{{
                    formatValue(item.trend?.from ?? 0, item.code)
                }}</strong>
                a
                <strong class="tabular-nums">{{
                    formatValue(item.trend?.to ?? 0, item.code)
                }}</strong>
                {{ indicatorMeta(item.code).unit }}.
            </span>
        </button>
    </section>
    <section
        v-else
        class="flex items-center gap-3 rounded-2xl border border-[var(--status-green)]/30 bg-[var(--status-green)]/8 p-4 text-sm"
    >
        <CircleCheck class="size-5 shrink-0 text-[var(--status-green)]" />
        {{
            who
                ? `Nada viene empeorando esta semana en ${who}.`
                : 'Nada viene empeorando esta semana. ¡Sigue así!'
        }}
    </section>
</template>
