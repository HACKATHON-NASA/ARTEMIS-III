<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AlertFilter } from '../Utils/alertState';

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

const TABS: { value: AlertFilter; label: string }[] = [
    { value: 'open', label: 'Sin atender' },
    { value: 'urgent', label: 'Urgentes' },
    { value: 'acknowledged', label: 'Atendidas' },
    { value: 'all', label: 'Todas' },
];
</script>

<template>
    <div
        class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"
    >
        <div
            role="tablist"
            aria-label="Filtrar por estado"
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

        <Select
            v-if="members"
            :model-value="astronaut === null ? 'all' : astronaut.toString()"
            @update:model-value="
                astronaut = $event === 'all' ? null : Number($event)
            "
        >
            <SelectTrigger
                class="w-full sm:w-64"
                aria-label="Filtrar por tripulante"
            >
                <SelectValue />
            </SelectTrigger>
            <SelectContent>
                <SelectItem value="all">Toda la tripulación</SelectItem>
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
