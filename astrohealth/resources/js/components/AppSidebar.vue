<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, ShieldCheck } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { admin, dashboard } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { NavItem } from '@/types';

/**
 * Menú lateral. Cada opción aparece según los permisos del actor
 * (ocultar no es proteger: cada pantalla y la API vuelven a comprobar).
 */
const auth = useAuthStore();

const mainNavItems = computed<NavItem[]>(() => [
    { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
    ...(auth.can('users.manage')
        ? [{ title: 'Administración', href: admin(), icon: ShieldCheck }]
        : []),
]);
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
