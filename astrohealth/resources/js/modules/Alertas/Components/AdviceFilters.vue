<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { AdviceCategory } from '../Types/advice';
import { ADVICE_CATEGORIES } from '../Utils/adviceMock';
import type { AdviceFilter } from '../Utils/adviceState';

/**
 * Los filtros del historial de indicaciones: en qué van (con su número),
 * a quién y de qué tema.
 */
defineProps<{
    counts: Record<AdviceFilter, number>;
    members: { id: number; name: string }[];
}>();

const filter = defineModel<AdviceFilter>('filter', { required: true });
const astronaut = defineModel<number | null>('astronaut', { required: true });
const category = defineModel<AdviceCategory | null>('category', {
    required: true,
});

const TABS: { value: AdviceFilter; label: string }[] = [
    { value: 'all', label: 'Todas' },
    { value: 'traveling', label: 'Viajando' },
    { value: 'unread', label: 'Sin leer' },
    { value: 'read', label: 'Entendidas' },
];
</script>

<template>
    <div class="flex flex-col gap-3">
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
                    class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap text-muted-foreground transition hover:text-foreground sm:flex-none"
                    :class="{
                        'bg-card text-foreground shadow-sm':
                            filter === tab.value,
                    }"
                    @click="filter = tab.value"
                >
                    {{ tab.label }}
                    <span
                        class="rounded-full bg-muted px-1.5 text-xs tabular-nums"
                        >{{ counts[tab.value] }}</span
                    >
                </button>
            </div>
            <Select
                :model-value="astronaut === null ? 'all' : astronaut.toString()"
                @update:model-value="
                    astronaut = $event === 'all' ? null : Number($event)
                "
            >
                <SelectTrigger
                    class="w-full sm:w-64"
                    aria-label="Filtrar por tripulante"
                    ><SelectValue
                /></SelectTrigger>
                <SelectContent>
                    <SelectItem value="all">Toda la tripulación</SelectItem>
                    <SelectItem
                        v-for="member in members"
                        :key="member.id"
                        :value="member.id.toString()"
                        >{{ member.name }}</SelectItem
                    >
                </SelectContent>
            </Select>
        </div>
        <div
            class="flex flex-wrap gap-1.5"
            role="group"
            aria-label="Filtrar por tema"
        >
            <button
                type="button"
                class="rounded-full border px-3 py-1 text-xs font-medium transition"
                :class="
                    category === null
                        ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                        : 'text-muted-foreground hover:text-foreground'
                "
                :aria-pressed="category === null"
                @click="category = null"
            >
                Todos los temas
            </button>
            <button
                v-for="(meta, code) in ADVICE_CATEGORIES"
                :key="code"
                type="button"
                class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium transition"
                :class="
                    category === code
                        ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                        : 'text-muted-foreground hover:text-foreground'
                "
                :aria-pressed="category === code"
                @click="category = code"
            >
                <component :is="meta.icon" class="size-3.5" />
                {{ meta.label }}
            </button>
        </div>
    </div>
</template>
