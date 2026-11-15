<script setup lang="ts">
import type { Component } from 'vue';

/**
 * Una cifra del resumen del Panel de salud (alertas, mediciones de hoy…).
 */
defineProps<{
    label: string;
    value: number;
    total?: number;
    hint: string;
    icon: Component;
    tone: 'sky' | 'red' | 'green';
}>();

const TONES = {
    sky: 'from-sky-400/25 to-sky-500/5 text-sky-600 dark:text-sky-300',
    red: 'from-red-400/25 to-red-500/5 text-red-600 dark:text-red-300',
    green: 'from-emerald-400/25 to-emerald-500/5 text-emerald-600 dark:text-emerald-300',
};
</script>

<template>
    <article
        class="group relative overflow-hidden rounded-2xl border bg-card p-5 transition duration-300 hover:-translate-y-0.5 hover:border-sky-500/40"
    >
        <div class="flex items-start justify-between gap-3">
            <p class="text-sm font-medium text-muted-foreground">{{ label }}</p>
            <span
                class="flex size-10 items-center justify-center rounded-xl bg-linear-to-br"
                :class="TONES[tone]"
            >
                <component :is="icon" class="size-5" />
            </span>
        </div>
        <p class="mt-3 text-3xl font-semibold tracking-tight tabular-nums">
            {{ value }}
            <span
                v-if="total !== undefined"
                class="text-lg font-normal text-muted-foreground"
                >/ {{ total }}</span
            >
        </p>
        <p class="mt-1 text-xs text-muted-foreground">{{ hint }}</p>
    </article>
</template>
