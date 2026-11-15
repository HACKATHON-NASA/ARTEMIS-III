<script setup lang="ts">
import { onMounted, ref, watch } from 'vue';
import type { Component } from 'vue';
import { countTo } from '../../Composables/useAdminMotion';

/**
 * Una cifra del resumen. El número cuenta hasta su valor al aparecer.
 */
const props = defineProps<{
    label: string;
    value: number;
    total?: number;
    hint: string;
    icon: Component;
    tone: 'sky' | 'blue' | 'indigo' | 'amber';
}>();

const shown = ref(0);

onMounted(() => countTo(shown, props.value));
watch(
    () => props.value,
    (value) => countTo(shown, value, 0.5),
);

const TONES = {
    sky: 'from-sky-400/25 to-sky-500/5 text-sky-600 dark:text-sky-300',
    blue: 'from-blue-500/25 to-blue-500/5 text-blue-600 dark:text-blue-300',
    indigo: 'from-indigo-400/25 to-indigo-500/5 text-indigo-600 dark:text-indigo-300',
    amber: 'from-amber-400/25 to-amber-500/5 text-amber-600 dark:text-amber-300',
};
</script>

<template>
    <article
        class="group relative overflow-hidden rounded-2xl border bg-card p-5 transition duration-300 hover:-translate-y-0.5 hover:border-sky-500/40 hover:shadow-[0_12px_32px_-16px_rgb(14_165_233/0.45)]"
    >
        <div
            class="pointer-events-none absolute inset-x-6 top-0 h-px bg-linear-to-r from-transparent via-sky-400/50 to-transparent opacity-0 transition duration-300 group-hover:opacity-100"
        />
        <div class="flex items-start justify-between gap-3">
            <p class="text-sm font-medium text-muted-foreground">
                {{ label }}
            </p>
            <span
                class="flex size-10 items-center justify-center rounded-xl bg-linear-to-br"
                :class="TONES[tone]"
            >
                <component :is="icon" class="size-5" />
            </span>
        </div>
        <p class="mt-3 text-3xl font-semibold tracking-tight tabular-nums">
            {{ shown }}
            <span
                v-if="total !== undefined"
                class="text-lg font-normal text-muted-foreground"
                >/ {{ total }}</span
            >
        </p>
        <p class="mt-1 text-xs text-muted-foreground">{{ hint }}</p>
    </article>
</template>
