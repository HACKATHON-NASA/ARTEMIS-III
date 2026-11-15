<script setup lang="ts">
import { CalendarDays, Users } from '@lucide/vue';
import { computed } from 'vue';
import type { MissionView } from '../Types/missions';
import { environmentInfo } from '../Utils/environments';
import { STATUS_LABELS, daysUntil, formatDate } from '../Utils/missionDates';

/**
 * Una franja con la misión que se está mirando: entorno, estado, en qué día
 * va y cuántos tripulantes tiene. Se repite en Tripulación, Panel de salud y
 * Alertas para que siempre se sepa de qué misión es lo que se ve.
 */
const props = defineProps<{ mission: MissionView }>();

const env = computed(() => environmentInfo(props.mission.environment));

const when = computed(() => {
    const mission = props.mission;

    if (mission.status === 'active') {
        return `Día ${mission.current_day} de ${mission.day_count}`;
    }

    if (mission.status === 'planned') {
        const days = daysUntil(mission.start_date);

        return `Empieza en ${days} ${days === 1 ? 'día' : 'días'}`;
    }

    return `Terminó el ${formatDate(mission.end_date)}`;
});
</script>

<template>
    <div
        class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-2xl border bg-linear-to-r to-transparent px-4 py-3 text-sm"
        :class="env.tint"
    >
        <span class="flex items-center gap-2 font-semibold">
            <component :is="env.icon" class="size-4" />
            {{ mission.name }}
        </span>
        <span class="text-muted-foreground">
            {{ env.name }} · {{ env.gravity }} · retraso {{ env.delay }}
        </span>
        <span
            class="rounded-full border bg-background/60 px-2 py-0.5 text-xs font-medium"
        >
            {{ STATUS_LABELS[mission.status] }}
        </span>
        <span class="flex items-center gap-1.5 text-muted-foreground">
            <CalendarDays class="size-4" />
            {{ when }}
        </span>
        <span class="flex items-center gap-1.5 text-muted-foreground">
            <Users class="size-4" />
            {{ mission.crew.length }}
            {{ mission.crew.length === 1 ? 'tripulante' : 'tripulantes' }}
        </span>
    </div>
</template>
