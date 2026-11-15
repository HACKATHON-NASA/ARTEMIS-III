<script setup lang="ts">
import type { MissionFilter } from '../Types/missions';

/**
 * Filtrar las misiones por estado, con cuántas hay en cada uno.
 */
defineProps<{ counts: Record<MissionFilter, number> }>();

const filter = defineModel<MissionFilter>({ required: true });

const TABS: { value: MissionFilter; label: string }[] = [
    { value: 'all', label: 'Todas' },
    { value: 'active', label: 'En curso' },
    { value: 'planned', label: 'Planificadas' },
    { value: 'finished', label: 'Terminadas' },
];
</script>

<template>
    <div
        role="tablist"
        aria-label="Filtrar misiones por estado"
        class="inline-flex w-full flex-wrap gap-1 rounded-xl border bg-muted/40 p-1 sm:w-auto"
    >
        <button
            v-for="tab in TABS"
            :key="tab.value"
            type="button"
            role="tab"
            :aria-selected="filter === tab.value"
            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium text-muted-foreground transition hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none sm:flex-none"
            :class="{
                'bg-card text-foreground shadow-sm': filter === tab.value,
            }"
            @click="filter = tab.value"
        >
            {{ tab.label }}
            <span
                class="rounded-full px-1.5 text-xs tabular-nums"
                :class="
                    filter === tab.value
                        ? 'bg-sky-500/15 text-sky-700 dark:text-sky-300'
                        : 'bg-muted'
                "
            >
                {{ counts[tab.value] }}
            </span>
        </button>
    </div>
</template>
