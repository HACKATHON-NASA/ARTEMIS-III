<script setup lang="ts">
import { ref } from 'vue';
import spaceImage from '../../../../images/fondos/espacio-principal.webp';
import { usePointerParallax } from '../Composables/usePointerParallax';

/**
 * El fondo: astronauta, Tierra y Luna. Capa lejana del paralaje (se mueve
 * poco). Las sombras oscurecen la derecha (tarjeta) y la esquina de la marca.
 */
const layer = ref<HTMLElement | null>(null);

usePointerParallax(layer, 22);
</script>

<template>
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div ref="layer" class="absolute -inset-8" data-intro="space">
            <img
                :src="spaceImage"
                alt=""
                class="size-full object-cover object-[28%_center]"
                fetchpriority="high"
                decoding="async"
            />
        </div>

        <div
            class="absolute inset-0 bg-linear-to-l from-[#020611]/85 via-[#020611]/25 to-transparent"
        />
        <div
            class="absolute inset-0 bg-linear-to-b from-[#020611]/70 via-transparent to-[#020611]/60"
        />
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_55%,rgba(2,6,17,0.65)_100%)]"
        />
    </div>
</template>
