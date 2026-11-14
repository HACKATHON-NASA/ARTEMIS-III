<script setup lang="ts">
import { computed } from 'vue';
import type { MissionSummary } from '../../Types/admin';
import HeartbeatLine from '../Acceso/HeartbeatLine.vue';

/**
 * Cabecera: qué es esta vista y en qué va la misión (día y avance).
 * Lleva el electrocardiograma de la pantalla de acceso como firma visual.
 */
const props = defineProps<{ mission: MissionSummary | null }>();

const progress = computed(() =>
    props.mission === null
        ? 0
        : Math.round((props.mission.day / props.mission.day_count) * 100),
);
</script>

<template>
    <header
        class="relative overflow-hidden rounded-2xl border bg-linear-to-br from-sky-500/10 via-card to-card p-6 sm:p-8"
        data-reveal
    >
        <HeartbeatLine
            class="pointer-events-none absolute top-5 -right-6 hidden w-80 opacity-70 sm:block"
        />

        <p
            class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
        >
            Centro de control
        </p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
            Administración
        </h1>
        <p class="mt-2 max-w-xl text-muted-foreground">
            Usuarios, permisos y dispositivos que envían datos de la misión.
        </p>

        <div
            v-if="mission"
            class="mt-6 flex max-w-md flex-col gap-2 sm:flex-row sm:items-center sm:gap-4"
        >
            <span
                class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 text-sm whitespace-nowrap backdrop-blur"
            >
                <span class="size-2 rounded-full bg-sky-500" />
                <strong class="font-semibold">{{ mission.name }}</strong>
                <span class="text-muted-foreground">
                    · {{ mission.environment_label }}
                </span>
            </span>
            <div class="flex flex-1 items-center gap-3">
                <div
                    class="h-1.5 flex-1 overflow-hidden rounded-full bg-muted"
                    role="progressbar"
                    :aria-valuenow="mission.day"
                    :aria-valuemax="mission.day_count"
                    aria-label="Avance de la misión"
                >
                    <div
                        class="mission-bar h-full rounded-full bg-linear-to-r from-sky-400 to-blue-600"
                        :style="{ width: `${progress}%` }"
                    />
                </div>
                <span
                    class="text-xs whitespace-nowrap text-muted-foreground tabular-nums"
                >
                    Día {{ mission.day }} de {{ mission.day_count }}
                </span>
            </div>
        </div>
    </header>
</template>

<style scoped>
.mission-bar {
    animation: grow 1.2s cubic-bezier(0.22, 1, 0.36, 1) both;
    transform-origin: left;
}

@keyframes grow {
    from {
        transform: scaleX(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .mission-bar {
        animation: none;
    }
}
</style>
