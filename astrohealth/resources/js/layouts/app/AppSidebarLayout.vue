<script setup lang="ts">
/**
 * El layout de la app: menú lateral + contenido. Sin barra superior en
 * escritorio (cada pantalla ya tiene su título); en el celular, una barra
 * mínima para abrir el menú. `breadcrumbs` queda por compatibilidad con las
 * pantallas que lo declaran.
 */
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import { Toaster } from '@/components/ui/sonner';
import type { BreadcrumbItem } from '@/types';

type Props = {
    breadcrumbs?: BreadcrumbItem[];
};

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar" class="min-w-0 overflow-x-clip">
            <AppSidebarHeader />
            <slot />
        </AppContent>
        <Toaster />
    </AppShell>
</template>
