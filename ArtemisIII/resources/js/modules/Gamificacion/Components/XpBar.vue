<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ref, watch } from 'vue';

const { t } = useI18n();

/**
 * El nivel y la barra hacia el siguiente (cada 100 puntos). Cuando suben
 * los puntos, un "+N" flota y se desvanece sobre la barra.
 * `compact` = versión pequeña (menú lateral).
 */
const props = withDefaults(
    defineProps<{
        points: number;
        level: number;
        progress: number;
        compact?: boolean;
    }>(),
    { compact: false },
);

const floating = ref<{ id: number; value: number }[]>([]);

watch(
    () => props.points,
    (now, before) => {
        if (before !== undefined && now > before) {
            const id = Date.now();
            floating.value.push({ id, value: now - before });
            setTimeout(
                () =>
                    (floating.value = floating.value.filter(
                        (item) => item.id !== id,
                    )),
                1600,
            );
        }
    },
);
</script>

<template>
    <div class="relative" :class="compact ? 'text-xs' : ''">
        <div class="mb-1.5 flex items-baseline justify-between gap-2">
            <span class="font-semibold" :class="compact ? '' : 'text-lg'">{{
                t('Gamificacion.level', { n: level })
            }}</span>
            <span class="text-muted-foreground tabular-nums">{{
                t('Gamificacion.levelProgress', { progress, points })
            }}</span>
        </div>
        <div
            class="overflow-hidden rounded-full bg-muted"
            :class="compact ? 'h-1.5' : 'h-3'"
            role="progressbar"
            :aria-valuenow="progress"
            aria-valuemin="0"
            aria-valuemax="100"
            :aria-label="t('Gamificacion.levelAria', { level, progress })"
        >
            <div
                class="h-full origin-left rounded-full bg-linear-to-r from-sky-400 to-blue-600 transition-transform duration-700 ease-out"
                :style="{ transform: `scaleX(${progress / 100})` }"
            />
        </div>
        <TransitionGroup
            enter-active-class="transition duration-300"
            enter-from-class="translate-y-2 opacity-0"
            leave-active-class="transition duration-700"
            leave-to-class="-translate-y-6 opacity-0"
        >
            <span
                v-for="item in floating"
                :key="item.id"
                class="pointer-events-none absolute -top-2 right-0 rounded-full bg-sky-500 px-2 py-0.5 text-xs font-bold text-white shadow-lg"
            >
                +{{ item.value }}
            </span>
        </TransitionGroup>
    </div>
</template>
