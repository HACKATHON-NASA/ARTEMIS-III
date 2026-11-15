<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AlertFilter } from '../Utils/alertState';

const { t } = useI18n();

/**
 * Los filtros del centro de alertas: el estado (pestañas con su número) y,
 * para médicos y administración, el tripulante.
 */
defineProps<{
    counts: Record<AlertFilter, number>;
    members?: { id: number; name: string }[];
}>();

const filter = defineModel<AlertFilter>('filter', { required: true });
const astronaut = defineModel<number | null>('astronaut', { default: null });

/** El texto de cada pestaña es una clave de i18n. */
const TABS: { value: AlertFilter; label: string }[] = [
    { value: 'open', label: 'Alertas.filters.open' },
    { value: 'urgent', label: 'Alertas.filters.urgent' },
    { value: 'acknowledged', label: 'Alertas.filters.acknowledged' },
    { value: 'all', label: 'Alertas.filters.all' },
];
</script>

<template>
    <div
        class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
    >
        <div
            role="tablist"
            :aria-label="t('Alertas.filters.byState')"
            class="inline-flex w-full flex-wrap gap-1 rounded-xl border bg-muted/40 p-1 sm:w-auto"
        >
            <button
                v-for="tab in TABS"
                :key="tab.value"
                type="button"
                role="tab"
                :aria-selected="filter === tab.value"
                class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap text-muted-foreground transition hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none sm:flex-none"
                :class="{
                    'bg-card text-foreground shadow-sm': filter === tab.value,
                }"
                @click="filter = tab.value"
            >
                {{ t(tab.label) }}
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

        <Select
            v-if="members"
            :model-value="astronaut === null ? 'all' : astronaut.toString()"
            @update:model-value="
                astronaut = $event === 'all' ? null : Number($event)
            "
        >
            <SelectTrigger
                class="w-full sm:w-64"
                :aria-label="t('Alertas.filters.byMember')"
            >
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                <SelectItem value="all">{{
                    t('Alertas.filters.wholeCrew')
                }}</SelectItem>
                <SelectItem
                    v-for="member in members"
                    :key="member.id"
                    :value="member.id.toString()"
                >
                    {{ member.name }}
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
