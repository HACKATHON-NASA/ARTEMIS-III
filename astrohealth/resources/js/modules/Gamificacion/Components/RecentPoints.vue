<script setup lang="ts">
import type { PointEntry } from '../Types/gamification';
import { POINTS, REASON_LABELS } from '../Utils/rules';

/**
 * Lo último que sumó puntos y, debajo, cómo se ganan (para que sea claro
 * qué hábito premia cada cosa).
 */
defineProps<{ recent: PointEntry[]; today: number }>();

const HOW: { label: string; points: number }[] = [
    {
        label: 'Registrar los 10 indicadores del día',
        points: POINTS.daily_complete,
    },
    { label: 'Racha de 7 días seguidos', points: POINTS.streak_7 },
    { label: 'Dormir 7 h o más', points: POINTS.sleep_7h },
    { label: 'Hacer 90 min de ejercicio', points: POINTS.exercise },
    { label: 'Cumplir un recordatorio', points: POINTS.reminder },
    { label: 'Hacer un test psicológico', points: POINTS.psych_test },
];
</script>

<template>
    <div class="flex flex-col gap-5">
        <ul class="flex flex-col gap-1.5">
            <li
                v-for="item in recent"
                :key="`${item.reason}-${item.reference}`"
                class="flex items-center justify-between gap-3 text-sm"
            >
                <span class="min-w-0 truncate">
                    {{ REASON_LABELS[item.reason] }}
                    <span class="text-xs text-muted-foreground">
                        · {{ item.day === today ? 'hoy' : `día ${item.day}` }}
                    </span>
                </span>
                <span
                    class="font-semibold text-sky-600 tabular-nums dark:text-sky-400"
                    >+{{ item.points }}</span
                >
            </li>
        </ul>
        <div class="rounded-xl bg-muted/50 p-3">
            <p
                class="mb-2 text-xs font-semibold text-muted-foreground uppercase"
            >
                Cómo se ganan
            </p>
            <ul class="flex flex-col gap-1 text-sm">
                <li
                    v-for="item in HOW"
                    :key="item.label"
                    class="flex justify-between gap-3"
                >
                    <span>{{ item.label }}</span>
                    <span class="font-medium tabular-nums"
                        >+{{ item.points }}</span
                    >
                </li>
            </ul>
        </div>
    </div>
</template>
