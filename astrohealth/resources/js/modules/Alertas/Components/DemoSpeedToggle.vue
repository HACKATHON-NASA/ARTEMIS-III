<script setup lang="ts">
import { Gauge } from '@lucide/vue';

/**
 * Solo para la demo: el retraso real con Marte es de 20 minutos; con ×60 las
 * alertas NUEVAS llegan a Tierra (y las respuestas a la nave) en 20 segundos.
 * Se recuerda en este navegador para todas las ventanas.
 */
defineProps<{ speed: number }>();
defineEmits<{ change: [speed: number] }>();
</script>

<template>
    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs transition"
        :class="
            speed > 1
                ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                : 'text-muted-foreground hover:text-foreground'
        "
        :aria-pressed="speed > 1"
        title="Solo para la demo: acelera el retraso de las alertas nuevas"
        @click="$emit('change', speed > 1 ? 1 : 60)"
    >
        <Gauge class="size-3.5" />
        {{
            speed > 1
                ? 'Demo ×60: el retraso de Marte dura 20 s'
                : 'Retraso real (20 min) · activar demo ×60'
        }}
    </button>
</template>
