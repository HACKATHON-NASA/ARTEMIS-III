<script setup lang="ts">
import { ref } from 'vue';
import { useTilt3d } from '../Composables/useTilt3d';
import AstronautHelmetIcon from './AstronautHelmetIcon.vue';

/**
 * La tarjeta de vidrio. Dos capas a propósito:
 * - la de afuera (data-intro="card") es la que anima la entrada y la salida;
 * - la de adentro se inclina en 3D con el puntero (useTilt3d).
 * Así las dos animaciones no se pisan.
 */
const tilt = ref<HTMLElement | null>(null);

useTilt3d(tilt);
</script>

<template>
    <div
        class="w-full max-w-[540px] justify-self-center lg:col-start-2 lg:justify-self-end"
        data-intro="card"
    >
        <div ref="tilt" class="card-glass relative rounded-[28px] p-8 sm:p-12">
            <div
                class="card-glare pointer-events-none absolute inset-0 rounded-[28px]"
            />
            <div
                class="pointer-events-none absolute inset-x-10 top-0 h-px bg-linear-to-r from-transparent via-sky-300/60 to-transparent"
            />

            <div class="relative">
                <div class="flex flex-col items-center text-center">
                    <AstronautHelmetIcon />
                    <h2
                        class="mt-4 text-3xl font-bold tracking-tight text-white sm:text-4xl"
                    >
                        Bienvenido/a
                    </h2>
                    <p class="mt-2 text-base text-slate-300">
                        Inicia sesión para acceder al sistema
                    </p>
                </div>

                <div class="mt-9">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Sin transform-style: preserve-3d: en Chromium anula el backdrop-filter. */
.card-glass {
    background:
        linear-gradient(160deg, rgb(30 58 110 / 0.35), rgb(2 6 23 / 0.55) 60%),
        rgb(2 6 23 / 0.35);
    border: 1px solid rgb(148 190 255 / 0.18);
    box-shadow:
        0 40px 90px -30px rgb(0 0 0 / 0.9),
        0 0 0 1px rgb(255 255 255 / 0.03) inset,
        0 0 60px -20px rgb(56 152 255 / 0.35);
    backdrop-filter: blur(22px) saturate(140%);
}

.card-glare {
    background: radial-gradient(
        520px circle at var(--glare-x, 50%) var(--glare-y, 0%),
        rgb(125 190 255 / 0.13),
        transparent 45%
    );
}
</style>
