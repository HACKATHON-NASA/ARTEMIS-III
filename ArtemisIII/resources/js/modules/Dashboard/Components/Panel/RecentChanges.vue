<script setup lang="ts">
import { ArrowRight, TrendingDown, TrendingUp } from '@lucide/vue';
import { statusMeta } from '@/lib/status';
import type { HealthChange } from '../../Types/crewTrends';
import { formatValue, indicatorMeta } from '../../Utils/indicatorMeta';

/**
 * Lo que cambió de color en las últimas 72 horas: lo que empeoró primero
 * (aunque todavía no haya alerta roja) y lo que se recuperó.
 */
defineProps<{ changes: HealthChange[] }>();
</script>

<template>
    <section class="rounded-2xl border bg-card">
        <header class="border-b p-4 sm:px-6">
            <h2 class="flex items-center gap-2 font-semibold">
                <TrendingDown class="size-4 text-sky-500" />
                Cambios en las últimas 72 h
            </h2>
        </header>
        <ul v-if="changes.length" class="divide-y">
            <li
                v-for="item in changes"
                :key="`${item.astronaut_id}-${item.indicator}`"
                class="flex items-center gap-3 px-4 py-3 text-sm sm:px-6"
            >
                <component
                    :is="item.worse ? TrendingDown : TrendingUp"
                    class="size-4 shrink-0"
                    :class="
                        item.worse
                            ? 'text-[var(--status-red)]'
                            : 'text-[var(--status-green)]'
                    "
                />
                <div class="min-w-0 flex-1">
                    <p class="truncate">
                        <strong>{{ item.name.split(' ')[0] }}</strong> ·
                        {{ indicatorMeta(item.indicator).label }}
                    </p>
                    <p
                        class="flex flex-wrap items-center gap-1.5 text-xs text-muted-foreground tabular-nums"
                    >
                        <span
                            class="size-1.5 rounded-full"
                            :style="{
                                background: statusMeta(item.from.status).color,
                            }"
                        />
                        {{ formatValue(item.from.value, item.indicator) }}
                        <ArrowRight class="size-3" />
                        <span
                            class="size-1.5 rounded-full"
                            :style="{
                                background: statusMeta(item.to.status).color,
                            }"
                        />
                        {{ formatValue(item.to.value, item.indicator) }}
                        {{ indicatorMeta(item.indicator).unit }} ·
                        {{ item.worse ? 'empeoró' : 'se recuperó' }}
                        ({{ statusMeta(item.to.status).label }})
                    </p>
                </div>
            </li>
        </ul>
        <p v-else class="p-6 text-center text-sm text-muted-foreground">
            Sin cambios de color en estos días.
        </p>
    </section>
</template>
