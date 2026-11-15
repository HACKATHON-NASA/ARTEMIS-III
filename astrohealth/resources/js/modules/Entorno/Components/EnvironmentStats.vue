<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { formatNumber } from '@/lib/i18n';
import { Gauge, Radiation, RadioTower } from '@lucide/vue';
import { computed } from 'vue';
import type { EnvironmentInfo } from '../Utils/environments';
import { EARTH_DOSE_PER_DAY, formatDelay } from '../Utils/environmentFacts';
import type { EnvironmentFacts } from '../Utils/environmentFacts';

const { t } = useI18n();

/**
 * Lo que cambia para el cuerpo en el entorno: la gravedad (y cuánto
 * pesaría una persona de 70 kg), el retraso de un mensaje con Tierra y la
 * radiación de fondo comparada con la de la Tierra.
 */
const props = defineProps<{ info: EnvironmentInfo; facts: EnvironmentFacts }>();

const gravityPct = computed(() => Math.round(props.facts.gravityG * 100));
const weight = computed(() => Math.round(70 * props.facts.gravityG));
const timesEarth = computed(() =>
    Math.round(props.info.radiationBase / EARTH_DOSE_PER_DAY),
);
</script>

<template>
    <div class="grid gap-4 md:grid-cols-3">
        <article class="rounded-2xl border bg-card p-5">
            <p
                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
            >
                <Gauge class="size-4 text-sky-500" />
                {{ t('Entorno.stats.gravity') }}
            </p>
            <p class="mt-3 text-3xl font-semibold tabular-nums">
                {{ info.gravity }}
            </p>
            <div
                class="mt-3 h-2 overflow-hidden rounded-full bg-muted"
                role="img"
                :aria-label="
                    t('Entorno.stats.gravityAria', { pct: gravityPct })
                "
            >
                <div
                    class="h-full rounded-full bg-linear-to-r from-sky-400 to-blue-600 transition-[width] duration-700"
                    :style="{ width: `${Math.max(2, gravityPct)}%` }"
                />
            </div>
            <i18n-t
                keypath="Entorno.stats.gravityText"
                tag="p"
                class="mt-2 text-xs text-muted-foreground"
            >
                <template #pct>{{ gravityPct }}</template>
                <template #weight>
                    <strong class="text-foreground">{{ weight }} kg</strong>
                </template>
            </i18n-t>
        </article>

        <article class="rounded-2xl border bg-card p-5">
            <p
                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
            >
                <RadioTower class="size-4 text-sky-500" />
                {{ t('Entorno.stats.delay') }}
            </p>
            <p class="mt-3 text-3xl font-semibold tabular-nums">
                {{ formatDelay(facts.delaySeconds) }}
            </p>
            <i18n-t
                keypath="Entorno.stats.delayText"
                tag="p"
                class="mt-2 text-xs text-muted-foreground"
            >
                <template #roundTrip>
                    <strong class="text-foreground">{{
                        formatDelay(facts.delaySeconds * 2)
                    }}</strong>
                </template>
            </i18n-t>
        </article>

        <article class="rounded-2xl border bg-card p-5">
            <p
                class="flex items-center gap-2 text-sm font-medium text-muted-foreground"
            >
                <Radiation class="size-4 text-sky-500" />
                {{ t('Entorno.stats.radiation') }}
            </p>
            <p class="mt-3 text-3xl font-semibold tabular-nums">
                {{ formatNumber(info.radiationBase) }}
                <span class="text-base font-normal text-muted-foreground">{{
                    t('Entorno.stats.perDay')
                }}</span>
            </p>
            <i18n-t
                keypath="Entorno.stats.radiationText"
                tag="p"
                class="mt-2 text-xs text-muted-foreground"
            >
                <template #times>
                    <strong class="text-foreground">{{
                        t('Entorno.stats.times', { n: timesEarth })
                    }}</strong>
                </template>
            </i18n-t>
        </article>
    </div>
</template>
