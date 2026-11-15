<script setup lang="ts">
import type { EnvironmentRisk } from '../Utils/environmentFacts';

/**
 * Los riesgos que se destacan en el entorno y qué parte del cuerpo afectan.
 */
defineProps<{ risks: EnvironmentRisk[] }>();
</script>

<template>
    <ul class="grid gap-3 sm:grid-cols-2">
        <li
            v-for="risk in risks"
            :key="risk.title"
            class="flex gap-3 rounded-2xl border bg-card p-4"
        >
            <span
                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-sky-500/12 text-sky-600 dark:text-sky-300"
            >
                <component :is="risk.icon" class="size-5" />
            </span>
            <div>
                <p class="font-semibold">{{ risk.title }}</p>
                <p class="text-xs font-medium text-sky-600 dark:text-sky-400">
                    {{ risk.organ }}
                </p>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ risk.body }}
                </p>
            </div>
        </li>
    </ul>
</template>
