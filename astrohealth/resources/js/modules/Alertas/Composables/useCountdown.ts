import { computed, onBeforeUnmount, ref, watch } from 'vue';
import type { Ref } from 'vue';

/**
 * Cuenta regresiva hasta una fecha ISO, en "mm:ss". Se detiene en 00:00.
 */
export function useCountdown(target: Ref<string | null>) {
    const now = ref(Date.now());
    let timer: ReturnType<typeof setInterval> | undefined;

    const remainingMs = computed(() =>
        target.value === null
            ? 0
            : Math.max(0, new Date(target.value).getTime() - now.value),
    );

    const label = computed(() => {
        const total = Math.ceil(remainingMs.value / 1000);
        const minutes = Math.floor(total / 60);
        const seconds = total % 60;

        return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    });

    watch(
        target,
        (value) => {
            clearInterval(timer);

            if (value !== null) {
                now.value = Date.now();
                timer = setInterval(() => (now.value = Date.now()), 1000);
            }
        },
        { immediate: true },
    );

    onBeforeUnmount(() => clearInterval(timer));

    return { remainingMs, label };
}
