<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Brain, ChartColumn, HeartPulse } from '@lucide/vue';
import { computed } from 'vue';
import { guia, psicologia, psicologiaTripulacion } from '@/routes';

/**
 * Las tres partes de la Guía de salud: lo físico (rangos y qué hacer), lo
 * psicológico (los tests) y los resultados de la tripulación (solo para
 * quien ve la salud de todos).
 */
const props = defineProps<{
    active: 'physical' | 'psych' | 'results';
    showResults: boolean;
}>();

const tabs = computed(() => [
    { key: 'physical', label: 'Salud física', icon: HeartPulse, href: guia() },
    {
        key: 'psych',
        label: 'Guía psicológica',
        icon: Brain,
        href: psicologia(),
    },
    ...(props.showResults
        ? [
              {
                  key: 'results',
                  label: 'Resultados de la tripulación',
                  icon: ChartColumn,
                  href: psicologiaTripulacion(),
              },
          ]
        : []),
]);
</script>

<template>
    <nav
        aria-label="Partes de la guía"
        class="inline-flex w-full flex-wrap gap-1 rounded-xl border bg-muted/40 p-1 sm:w-auto"
    >
        <Link
            v-for="tab in tabs"
            :key="tab.key"
            :href="tab.href"
            :aria-current="active === tab.key ? 'page' : undefined"
            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg px-3 py-1.5 text-sm font-medium whitespace-nowrap transition sm:flex-none"
            :class="
                active === tab.key
                    ? 'bg-card text-foreground shadow-sm'
                    : 'text-muted-foreground hover:text-foreground'
            "
        >
            <component :is="tab.icon" class="size-4" />
            {{ tab.label }}
        </Link>
    </nav>
</template>
