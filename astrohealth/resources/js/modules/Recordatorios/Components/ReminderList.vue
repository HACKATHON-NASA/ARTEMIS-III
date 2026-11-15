<script setup lang="ts">
import type { Reminder } from '../Types/reminders';
import ReminderItem from './ReminderItem.vue';

/**
 * La rutina del día con su avance (hechos / total).
 */
const props = defineProps<{
    reminders: Reminder[];
    done: number;
    total: number;
    busyId: number | null;
}>();

defineEmits<{ done: [reminder: Reminder] }>();

const percent = () =>
    props.total === 0 ? 0 : (props.done / props.total) * 100;
</script>

<template>
    <div>
        <div class="mb-3 flex items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">Tu rutina de hoy</h2>
            <span class="text-sm text-muted-foreground tabular-nums">
                {{ done }} de {{ total }}
            </span>
        </div>
        <div class="mb-4 h-1.5 overflow-hidden rounded-full bg-muted">
            <div
                class="h-full rounded-full bg-linear-to-r from-sky-400 to-blue-600 transition-[width] duration-700 ease-out"
                :style="{ width: `${percent()}%` }"
            />
        </div>
        <ul class="flex flex-col gap-2">
            <ReminderItem
                v-for="reminder in reminders"
                :key="reminder.id"
                :reminder="reminder"
                :busy="busyId === reminder.id"
                @done="$emit('done', reminder)"
            />
        </ul>
    </div>
</template>
