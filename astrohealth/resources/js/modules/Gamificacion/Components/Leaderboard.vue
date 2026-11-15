<script setup lang="ts">
import { getInitials } from '@/composables/useInitials';
import type { LeaderRow } from '../Types/gamification';

/**
 * El ranking de la tripulación por puntos. Resalta a quien está viendo.
 */
defineProps<{ rows: LeaderRow[]; me: number | null }>();

const MEDALS = ['🥇', '🥈', '🥉'];
</script>

<template>
    <ol class="divide-y">
        <li
            v-for="(row, index) in rows"
            :key="row.astronaut_id"
            class="flex items-center gap-3 px-4 py-3 sm:px-6"
            :class="{ 'bg-sky-500/8': row.astronaut_id === me }"
        >
            <span
                class="w-7 text-center text-lg"
                :aria-label="`Puesto ${index + 1}`"
            >
                {{ MEDALS[index] ?? index + 1 }}
            </span>
            <span
                class="flex size-9 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-xs font-semibold text-white"
                aria-hidden="true"
            >
                {{ getInitials(row.name) }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="truncate text-sm font-semibold">
                    {{ row.name }}
                    <span
                        v-if="row.astronaut_id === me"
                        class="text-xs font-normal text-sky-600 dark:text-sky-400"
                        >(tú)</span
                    >
                </p>
                <p class="text-xs text-muted-foreground">
                    Nivel {{ row.level }} · 🔥 {{ row.streak_days }} ·
                    {{ row.badges }} insignias
                </p>
            </div>
            <span class="font-semibold tabular-nums">{{ row.points }}</span>
        </li>
    </ol>
</template>
