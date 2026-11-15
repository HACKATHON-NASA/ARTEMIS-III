<script setup lang="ts">
import nasaLogo from '../../../../../images/marca/nasa-logo.webp';
import { BRAND } from '@/lib/brand';

/**
 * La marca: logo de la NASA + el nombre + el lema.
 * Cada hijo directo entra por separado en la animación (data-intro="brand").
 */
</script>

<template>
    <header class="flex items-center gap-5 sm:gap-7" data-intro="brand">
        <div class="shrink-0">
            <img
                :src="nasaLogo"
                alt="NASA"
                class="brand-logo size-20 object-contain sm:size-28"
            />
        </div>

        <div>
            <h1
                class="text-4xl font-semibold tracking-tight text-white sm:text-5xl"
            >
                {{ BRAND.first
                }}<span class="text-sky-400">{{ BRAND.accent }}</span>
            </h1>
            <p
                class="mt-2 max-w-xs text-base leading-snug text-slate-200/90 sm:text-xl"
            >
                Cuidamos la salud de quienes exploran más allá de la Tierra
            </p>
        </div>
    </header>
</template>

<style scoped>
.brand-logo {
    filter: drop-shadow(0 0 18px rgb(56 152 255 / 0.35));
    animation: brand-glow 6s ease-in-out infinite;
}

@keyframes brand-glow {
    50% {
        filter: drop-shadow(0 0 28px rgb(56 152 255 / 0.6));
    }
}

@media (prefers-reduced-motion: reduce) {
    .brand-logo {
        animation: none;
    }
}
</style>
