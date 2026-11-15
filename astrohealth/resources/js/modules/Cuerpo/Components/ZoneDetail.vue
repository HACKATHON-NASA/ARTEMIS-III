<script setup lang="ts">
import { ExternalLink, Orbit } from '@lucide/vue';
import { statusMeta } from '@/lib/status';
import type { BodyPart, ZoneStatus } from '../Types/body';
import { INDICATOR_NAMES } from '../Utils/indicators';

/**
 * El detalle de una zona en 3 bloques: estado y valores, cómo la afecta el
 * espacio y qué hacer. "Qué hacer" es un slot: la pantalla pone ahí la
 * recomendación de M5. Lo usan el panel lateral (astronauta) y el Panel de
 * salud (al lado del cuerpo).
 */
defineProps<{ part: BodyPart; zone: ZoneStatus }>();
</script>

<template>
    <div class="flex flex-col gap-6">
        <section>
            <h3
                class="mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
            >
                Estado actual
            </h3>
            <ul v-if="zone.values.length" class="divide-y rounded-xl border">
                <li
                    v-for="item in zone.values"
                    :key="item.indicator"
                    class="flex items-center justify-between gap-3 px-4 py-3"
                >
                    <span class="flex items-center gap-2 text-sm">
                        <span
                            class="size-2 rounded-full"
                            :style="{
                                backgroundColor: statusMeta(item.status).color,
                            }"
                        />
                        {{ INDICATOR_NAMES[item.indicator] }}
                    </span>
                    <span class="font-semibold tabular-nums">
                        {{ item.value }}
                        <span
                            class="text-xs font-normal text-muted-foreground"
                            >{{ item.unit }}</span
                        >
                    </span>
                </li>
            </ul>
            <p
                v-else
                class="rounded-xl border border-dashed p-4 text-sm text-muted-foreground"
            >
                Esta zona no tiene un sensor propio todavía. Si hay síntomas, se
                registran y se avisa al médico.
            </p>
        </section>

        <section>
            <h3
                class="mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
            >
                Cómo lo afecta el espacio
            </h3>
            <div
                class="rounded-xl border bg-linear-to-br from-sky-500/10 to-transparent p-4"
            >
                <p
                    class="mb-2 inline-flex items-center gap-1.5 text-xs font-medium text-sky-700 dark:text-sky-300"
                >
                    <Orbit class="size-3.5" />
                    Peligro NASA HRP: {{ part.hrp_hazard }}
                </p>
                <p class="text-sm leading-relaxed">
                    {{ part.space_risk_text }}
                </p>
                <a
                    href="https://www.nasa.gov/hrp/risks/"
                    target="_blank"
                    rel="noopener"
                    class="mt-3 inline-flex items-center gap-1 text-xs text-sky-600 hover:underline dark:text-sky-400"
                >
                    Fuente: NASA Human Research Program
                    <ExternalLink class="size-3" />
                </a>
            </div>
        </section>

        <section>
            <h3
                class="mb-2 text-xs font-semibold tracking-wider text-muted-foreground uppercase"
            >
                Qué hacer
            </h3>
            <slot name="recommendation">
                <p class="text-sm text-muted-foreground">
                    Todo en orden. Seguir con la rutina.
                </p>
            </slot>
        </section>
    </div>
</template>
