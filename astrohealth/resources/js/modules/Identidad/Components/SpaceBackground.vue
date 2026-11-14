<script setup lang="ts">
import { ref } from 'vue';
import spaceSmall from '../../../../images/fondos/espacio-principal-960.webp';
import spaceLarge from '../../../../images/fondos/espacio-principal.webp';
import { useAmbientDrift } from '../Composables/useAmbientDrift';
import { usePointerParallax } from '../Composables/usePointerParallax';

/**
 * El fondo: astronauta, Tierra y Luna, a pantalla completa en cualquier tamaño.
 *
 * Tres capas, cada una con un solo movimiento:
 * - la de afuera (data-intro="space"): la entrada y el paralaje con el puntero;
 * - la imagen: la deriva lenta de cámara (useAmbientDrift);
 * - las sombras: oscurecen donde va la tarjeta y la marca, según la orientación.
 *
 * El navegador elige la imagen liviana (960 px) en el celular (srcset).
 */
const layer = ref<HTMLElement | null>(null);
const image = ref<HTMLElement | null>(null);

usePointerParallax(layer, 22);
useAmbientDrift(image);
</script>

<template>
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div ref="layer" class="absolute -inset-8" data-intro="space">
            <img
                ref="image"
                :src="spaceLarge"
                :srcset="`${spaceSmall} 960w, ${spaceLarge} 1600w`"
                sizes="100vw"
                alt=""
                class="size-full object-cover object-[30%_center] portrait:object-[36%_35%]"
                fetchpriority="high"
                decoding="async"
            />
        </div>

        <!-- Horizontal: la tarjeta va a la derecha. Vertical: va abajo. -->
        <div
            class="absolute inset-0 bg-linear-to-l from-[#020611]/85 via-[#020611]/25 to-transparent portrait:bg-linear-to-t portrait:from-[#020611]/90 portrait:via-[#020611]/40"
        />
        <div
            class="absolute inset-0 bg-linear-to-b from-[#020611]/70 via-transparent to-[#020611]/60"
        />
        <div
            class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,transparent_55%,rgba(2,6,17,0.65)_100%)]"
        />
    </div>
</template>
