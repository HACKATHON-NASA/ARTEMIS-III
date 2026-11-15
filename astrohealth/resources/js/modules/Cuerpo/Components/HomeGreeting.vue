<script setup lang="ts">
import { Gauge, RadioTower } from '@lucide/vue';
import { computed } from 'vue';

/**
 * El saludo del Inicio: nombre, misión, día y el retraso con Tierra
 * (lo que tarda un mensaje en llegar al médico).
 */
const props = defineProps<{
    name: string;
    missionName: string;
    environmentLabel: string;
    day: number;
    dayCount: number;
    gravity: number;
    delaySeconds: number;
}>();

const greeting = computed(() => {
    const hour = new Date().getHours();

    return hour < 12
        ? 'Buenos días'
        : hour < 19
          ? 'Buenas tardes'
          : 'Buenas noches';
});

const firstName = computed(() => props.name.split(' ')[0]);
const delay = computed(() =>
    props.delaySeconds >= 60
        ? `${Math.round(props.delaySeconds / 60)} min`
        : `${props.delaySeconds} s`,
);
</script>

<template>
    <div>
        <p
            class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
        >
            {{ missionName }} · Día {{ day }} de {{ dayCount }}
        </p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
            {{ greeting }}, {{ firstName }}
        </h1>
        <p class="mt-2 text-muted-foreground">
            Así está tu cuerpo hoy. Toca una zona para ver qué pasa y qué hacer.
        </p>

        <ul class="mt-5 flex flex-wrap gap-2 text-sm">
            <li
                class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 backdrop-blur"
            >
                <span class="size-2 rounded-full bg-sky-500" />
                {{ environmentLabel }}
            </li>
            <li
                class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 backdrop-blur"
            >
                <Gauge class="size-4 text-sky-500" />
                Gravedad {{ gravity }} g
            </li>
            <li
                class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 backdrop-blur"
            >
                <RadioTower class="size-4 text-sky-500" />
                Tierra a {{ delay }} de distancia
            </li>
        </ul>
    </div>
</template>
