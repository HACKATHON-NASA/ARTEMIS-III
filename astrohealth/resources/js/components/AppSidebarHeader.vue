<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';

/**
 * Barra superior SOLO en el celular: ahí el menú va escondido y este botón
 * es la única forma de abrirlo. En escritorio no hay barra (no gasta
 * pantalla): el botón va junto al logo, dentro del menú (AppSidebar).
 */
</script>

<template>
    <header
        class="flex h-12 shrink-0 items-center gap-2 border-b border-sidebar-border/70 px-4 md:hidden"
    >
        <SidebarTrigger class="-ml-1" />
        <AppLogo />
    </header>
</template>
