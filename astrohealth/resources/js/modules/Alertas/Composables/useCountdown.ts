import { computed, onBeforeUnmount, ref, watch } from 'vue';
import type { Ref } from 'vue';

/**
 * Cuenta regresiva hasta una fecha ISO, en "mm:ss". Se detiene en 00:00.
 *
 * `speed` (opcional) acelera SOLO lo que se muestra, para la demo
 * ("⏩ ×60": los 20 minutos de Marte pasan en 20 segundos). La fecha
 * guardada no cambia.
 */
export function useCountdown(target: Ref<string | null>, speed?: Ref<number>) {
    const now = ref(Date.now());
    /** Milisegundos "adelantados" por el modo demo. */
    const skipped = ref(0);
    let timer: ReturnType<typeof setInterval> | undefined;

    const remainingMs = computed(() =>
        target.value === null
            ? 0
            : Math.max(
                  0,
                  new Date(target.value).getTime() - now.value - skipped.value,
              ),
    );

    const label = computed(() => {
        const total = Math.ceil(remainingMs.value / 1000);
        const minutes = Math.floor(total / 60);
        const seconds = total % 60;

        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    });

    function tick(): void {
        const previous = now.value;
        now.value = Date.now();
        const factor = speed?.value ?? 1;

        if (factor > 1) {
            skipped.value += (now.value - previous) * (factor - 1);
        }
    }

    watch(
        target,
        (value) => {
            clearInterval(timer);
            skipped.value = 0;

            if (value !== null) {
                now.value = Date.now();
                timer = setInterval(tick, 250);
            }
        },
        { immediate: true },
    );

    onBeforeUnmount(() => clearInterval(timer));

    return { remainingMs, label, now };
}
