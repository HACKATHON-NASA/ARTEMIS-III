<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Gauge } from '@lucide/vue';

const { t } = useI18n();

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
        :title="t('Alertas.speed.title')"
        @click="$emit('change', speed > 1 ? 1 : 60)"
    >
        <Gauge class="size-3.5" />
        {{ speed > 1 ? t('Alertas.speed.on') : t('Alertas.speed.off') }}
    </button>
</template>
