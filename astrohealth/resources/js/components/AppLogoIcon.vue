<script setup lang="ts">
import nasaLogo from '../../images/marca/nasa-logo.webp';

/**
 * El logo de la NASA (menú lateral y encabezado). Lleva su propio fondo
 * azul, así que sirve igual en tema claro y oscuro.
 */
</script>

<template>
    <span class="relative inline-block">
        <img :src="nasaLogo" alt="NASA" class="size-full object-contain" />
    </span>
</template>
