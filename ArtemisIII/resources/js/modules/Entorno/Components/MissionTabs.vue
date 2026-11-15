<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { MissionFilter } from '../Types/missions';

const { t } = useI18n();

/**
 * Filtrar las misiones por estado, con cuántas hay en cada uno.
 */
defineProps<{ counts: Record<MissionFilter, number> }>();

const filter = defineModel<MissionFilter>({ required: true });

const TABS: MissionFilter[] = ['active', 'planned', 'finished', 'all'];
</script>

<template>
    <div
        role="tablist"
        :aria-label="t('Entorno.tabs.label')"
        class="inline-flex w-full flex-wrap gap-1 rounded-xl border bg-muted/40 p-1 sm:w-auto"
    >
        <button
            v-for="tab in TABS"
            :key="tab"
            type="button"
            role="tab"
            :aria-selected="filter === tab"
            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap text-muted-foreground transition hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none sm:flex-none"
            :class="{
                'bg-card text-foreground shadow-sm': filter === tab,
            }"
            @click="filter = tab"
        >
            {{ t(`Entorno.tabs.${tab}`) }}
            <span
                class="rounded-full px-1.5 text-xs tabular-nums"
                :class="
                    filter === tab
                        ? 'bg-sky-500/15 text-sky-700 dark:text-sky-300'
                        : 'bg-muted'
                "
            >
                {{ counts[tab] }}
            </span>
        </button>
    </div>
</template>
