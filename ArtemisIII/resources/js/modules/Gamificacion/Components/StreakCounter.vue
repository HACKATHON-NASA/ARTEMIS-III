<script setup lang="ts">
import gsap from 'gsap';
import { ref, watch } from 'vue';

/**
 * 🔥 La racha: días seguidos con el registro completo. Late una vez cuando
 * sube (sin animación con "reducir movimiento").
 */
const props = defineProps<{ days: number }>();

const flame = ref<HTMLElement | null>(null);

watch(
    () => props.days,
    (now, before) => {
        if (
            flame.value &&
            now > before &&
            !window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
            gsap.fromTo(
                flame.value,
                { scale: 1 },
                {
                    scale: 1.35,
                    duration: 0.25,
                    yoyo: true,
                    repeat: 1,
                    ease: 'power2.out',
                },
            );
        }
    },
);
</script>

<template>
    <div class="flex items-center gap-3">
        <span ref="flame" class="text-4xl" aria-hidden="true">🔥</span>
        <div>
            <p class="text-2xl leading-none font-semibold tabular-nums">
                {{ days }}
                <span class="text-base font-normal text-muted-foreground">{{
                    days === 1 ? 'día' : 'días'
                }}</span>
            </p>
            <p class="mt-1 text-xs text-muted-foreground">
                de racha con el registro completo
            </p>
        </div>
    </div>
</template>
