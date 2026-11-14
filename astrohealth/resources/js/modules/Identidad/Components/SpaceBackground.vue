<script setup lang="ts">
import spaceSmall from '../../../../images/fondos/espacio-principal-960.webp';
import spaceLarge from '../../../../images/fondos/espacio-principal.webp';

/**
 * El fondo: astronauta, Tierra y Luna, quieto y a pantalla completa en
 * cualquier tamaño. Las sombras oscurecen donde van el formulario y la marca,
 * según la orientación. En el celular carga la imagen liviana (srcset).
 */
</script>

<template>
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <img
            :src="spaceLarge"
            :srcset="`${spaceSmall} 960w, ${spaceLarge} 1600w`"
            sizes="100vw"
            alt=""
            class="size-full object-cover object-[30%_center] portrait:object-[36%_35%]"
            fetchpriority="high"
            decoding="async"
            data-intro="space"
        />

        <!-- Horizontal: el formulario va a la derecha. Vertical: va abajo. -->
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
