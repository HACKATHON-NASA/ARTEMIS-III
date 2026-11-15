<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';

/**
 * La prueba de reacción (PVT corto): 8 señales que aparecen después de una
 * espera al azar (2 a 5 s). Se toca el círculo o se pulsa Espacio lo más
 * rápido posible. Tocar antes de tiempo cuenta como "anticipo".
 */
const emit = defineEmits<{
    done: [result: { reactions: number[]; falseStarts: number }];
}>();

const TRIALS = 8;
const state = ref<'waiting' | 'go' | 'early' | 'shown'>('waiting');
const trial = ref(0);
const lastMs = ref<number | null>(null);
const reactions: number[] = [];
let falseStarts = 0;
let shownAt = 0;
let timer: ReturnType<typeof setTimeout> | undefined;

function schedule(): void {
    state.value = 'waiting';
    timer = setTimeout(
        () => {
            state.value = 'go';
            shownAt = performance.now();
        },
        2000 + Math.random() * 3000,
    );
}

function press(): void {
    if (state.value === 'waiting') {
        clearTimeout(timer);
        falseStarts++;
        state.value = 'early';
        timer = setTimeout(schedule, 900);

        return;
    }

    if (state.value !== 'go') {
        return;
    }

    lastMs.value = Math.round(performance.now() - shownAt);
    reactions.push(lastMs.value);
    trial.value++;
    state.value = 'shown';

    if (trial.value >= TRIALS) {
        timer = setTimeout(() => emit('done', { reactions, falseStarts }), 700);

        return;
    }

    timer = setTimeout(schedule, 700);
}

function onKey(event: KeyboardEvent): void {
    if (event.code === 'Space') {
        event.preventDefault();
        press();
    }
}

onMounted(() => {
    window.addEventListener('keydown', onKey);
    schedule();
});

onBeforeUnmount(() => {
    clearTimeout(timer);
    window.removeEventListener('keydown', onKey);
});
</script>

<template>
    <div class="flex flex-col items-center gap-4">
        <p class="text-xs text-muted-foreground tabular-nums">
            Señal {{ Math.min(trial + 1, TRIALS) }} de {{ TRIALS }}
        </p>
        <button
            type="button"
            class="flex size-56 items-center justify-center rounded-full border-4 text-sm font-medium transition-colors duration-75 select-none focus-visible:outline-none"
            :class="{
                'border-muted bg-muted/40 text-muted-foreground':
                    state === 'waiting',
                'border-sky-400 bg-sky-500 text-white shadow-[0_0_40px_rgb(14_165_233/0.6)]':
                    state === 'go',
                'border-[var(--status-yellow)] bg-[var(--status-yellow)]/15':
                    state === 'early',
                'border-muted bg-muted/40': state === 'shown',
            }"
            aria-live="polite"
            @pointerdown="press"
        >
            <span v-if="state === 'waiting'">Espera la señal…</span>
            <span v-else-if="state === 'go'" class="text-lg font-bold"
                >¡Ya!</span
            >
            <span v-else-if="state === 'early'">Muy pronto</span>
            <span v-else class="text-2xl font-semibold tabular-nums"
                >{{ lastMs }} ms</span
            >
        </button>
        <p class="text-xs text-muted-foreground">
            Toca el círculo o pulsa Espacio.
        </p>
    </div>
</template>
