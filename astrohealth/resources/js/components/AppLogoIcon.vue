<script setup lang="ts">
import logoColor from '../../images/marca/logo-color.png';
import logoNegro from '../../images/marca/logo-negro.png';

/**
 * El logo de AstroHealth. En tema claro va el negro; en oscuro, el de color
 * (tiene partes blancas que solo se ven sobre fondo oscuro).
 */
</script>

<template>
    <span class="relative inline-block">
        <img
            :src="logoNegro"
            alt="AstroHealth"
            class="size-full object-contain dark:hidden"
        />
        <img
            :src="logoColor"
            alt="AstroHealth"
            class="hidden size-full object-contain dark:block"
        />
    </span>
</template>
