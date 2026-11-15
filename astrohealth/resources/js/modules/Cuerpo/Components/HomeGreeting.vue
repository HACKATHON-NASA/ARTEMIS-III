<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { formatNumber } from '@/lib/i18n';
import { Gauge, RadioTower } from '@lucide/vue';
import { computed } from 'vue';

const { t } = useI18n();

/**
 * El saludo del Inicio: nombre, misión, día y el retraso con Tierra
 * (lo que tarda un mensaje en llegar al médico).
 */
const props = defineProps<{
    name: string;
    /** La misión actual (M6): nombre, entorno, día, gravedad y retraso. */
    mission: {
        name: string;
        environment: 'iss' | 'transit' | 'moon' | 'mars';
        day: number;
        day_count: number;
        gravity_g: number;
        ground_delay_seconds: number;
    };
}>();

const greeting = computed(() => {
    const hour = new Date().getHours();

    return hour < 12
        ? t('Cuerpo.greeting.morning')
        : hour < 19
          ? t('Cuerpo.greeting.afternoon')
          : t('Cuerpo.greeting.evening');
});

const firstName = computed(() => props.name.split(' ')[0]);
const delay = computed(() =>
    props.mission.ground_delay_seconds >= 60
        ? t('units.minutes', {
              value: Math.round(props.mission.ground_delay_seconds / 60),
          })
        : t('units.seconds', { value: props.mission.ground_delay_seconds }),
);
</script>

<template>
    <div>
        <p
            class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
        >
            {{
                t('Cuerpo.greeting.mission', {
                    mission: mission.name,
                    day: mission.day,
                    total: mission.day_count,
                })
            }}
        </p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
            {{ t('Cuerpo.greeting.hello', { greeting, name: firstName }) }}
        </h1>
        <p class="mt-2 text-muted-foreground">
            {{ t('Cuerpo.greeting.subtitle') }}
        </p>

        <ul class="mt-5 flex flex-wrap gap-2 text-sm">
            <li
                class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 backdrop-blur"
            >
                <span class="size-2 rounded-full bg-sky-500" />
                {{ t(`environments.${mission.environment}`) }}
            </li>
            <li
                class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 backdrop-blur"
            >
                <Gauge class="size-4 text-sky-500" />
                {{
                    t('Cuerpo.greeting.gravity', {
                        value: formatNumber(mission.gravity_g),
                    })
                }}
            </li>
            <li
                class="inline-flex items-center gap-2 rounded-full border bg-background/60 px-3 py-1 backdrop-blur"
            >
                <RadioTower class="size-4 text-sky-500" />
                {{ t('Cuerpo.greeting.earthAt', { delay }) }}
            </li>
        </ul>
    </div>
</template>
