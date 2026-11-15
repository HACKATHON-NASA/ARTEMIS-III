<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight, CircleCheck } from '@lucide/vue';
import type { PlanItem } from '../Utils/todayPlan';

/**
 * "Tu día": cuatro tarjetas con lo que falta hoy y un botón para hacerlo.
 * Lo que ya está completo se marca en verde.
 */
defineProps<{ items: PlanItem[] }>();
</script>

<template>
    <section
        class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4"
        aria-label="Tu día"
    >
        <article
            v-for="item in items"
            :key="item.key"
            class="flex flex-col gap-3 rounded-2xl border bg-card p-4"
            :class="{ 'border-[var(--status-green)]/30': item.complete }"
        >
            <div class="flex items-center gap-2">
                <span
                    class="flex size-8 items-center justify-center rounded-lg"
                    :class="
                        item.complete
                            ? 'bg-[var(--status-green)]/15 text-[var(--status-green)]'
                            : 'bg-sky-500/12 text-sky-600 dark:text-sky-300'
                    "
                >
                    <CircleCheck v-if="item.complete" class="size-4" />
                    <component :is="item.icon" v-else class="size-4" />
                </span>
                <p class="text-sm font-semibold">{{ item.title }}</p>
                <span
                    class="ml-auto text-xs text-muted-foreground tabular-nums"
                >
                    {{ item.progress.done }}/{{ item.progress.total }}
                </span>
            </div>
            <div class="h-1.5 overflow-hidden rounded-full bg-muted">
                <div
                    class="h-full rounded-full transition-[width] duration-700"
                    :class="
                        item.complete
                            ? 'bg-[var(--status-green)]'
                            : 'bg-linear-to-r from-sky-400 to-blue-600'
                    "
                    :style="{
                        width: `${Math.min(100, (item.progress.done / Math.max(1, item.progress.total)) * 100)}%`,
                    }"
                />
            </div>
            <p class="min-h-10 text-sm text-muted-foreground">
                {{ item.detail }}
            </p>
            <Link
                v-if="!item.complete"
                :href="item.href"
                class="mt-auto inline-flex items-center gap-1 text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
                {{ item.action }} <ArrowRight class="size-4" />
            </Link>
        </article>
    </section>
</template>
