<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { CircleCheck, TrendingDown, TrendingUp } from '@lucide/vue';
import type { IndicatorHistory } from '../../Stores/useMyEvolutionStore';
import { formatValue, indicatorMeta } from '../../Utils/indicatorMeta';

const { t } = useI18n();

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
        <p class="text-sm font-semibold">{{ t('Dashboard.banner.watch') }}</p>
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
                <i18n-t
                    :keypath="
                        item.trend?.direction === 'down'
                            ? 'Dashboard.banner.goingDown'
                            : 'Dashboard.banner.goingUp'
                    "
                    scope="global"
                >
                    <template #subject>{{
                        who
                            ? t('Dashboard.banner.ofWho', {
                                  indicator: indicatorMeta(item.code).label,
                                  name: who,
                              })
                            : t('Dashboard.banner.yours', {
                                  indicator: indicatorMeta(
                                      item.code,
                                  ).label.toLowerCase(),
                              })
                    }}</template>
                    <template #from>
                        <strong class="tabular-nums">{{
                            formatValue(item.trend?.from ?? 0, item.code)
                        }}</strong>
                    </template>
                    <template #to>
                        <strong class="tabular-nums">{{
                            formatValue(item.trend?.to ?? 0, item.code)
                        }}</strong>
                    </template>
                    <template #unit>{{
                        indicatorMeta(item.code).unit
                    }}</template>
                </i18n-t>
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
                ? t('Dashboard.banner.nothingWho', { name: who })
                : t('Dashboard.banner.nothing')
        }}
    </section>
</template>
