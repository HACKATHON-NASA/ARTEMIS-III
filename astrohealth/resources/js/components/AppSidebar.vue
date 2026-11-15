<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    BellRing,
    BookOpen,
    ChartLine,
    HeartPulse,
    ShieldCheck,
    SquarePlus,
    Users,
} from '@lucide/vue';
import { computed, onMounted } from 'vue';
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
import { useAlertsStore } from '@/modules/Alertas/Stores/useAlertsStore';
import { dashboard, inicio, salud, tripulacion, usuarios } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { NavItem } from '@/types';

/**
 * Menú lateral. Cada opción aparece según los permisos del actor
 * (ocultar no es proteger: cada pantalla y la API vuelven a comprobar).
 * Las opciones sin pantalla todavía salen como "Pronto".
 */
const auth = useAuthStore();
const alerts = useAlertsStore();

const canSeeCrew = computed(() => auth.can('health.read.any'));

const missionItems = computed<NavItem[]>(() => [
    ...(auth.actor?.astronaut_id
        ? [{ title: 'Inicio', href: inicio(), icon: HeartPulse }]
        : []),
    ...(canSeeCrew.value
        ? [
              { title: 'Panel de salud', href: salud(), icon: Activity },
              { title: 'Tripulación', href: tripulacion(), icon: Users },
          ]
        : []),
    { title: 'Registrar datos', href: '#', icon: SquarePlus, soon: true },
    { title: 'Historial', href: '#', icon: ChartLine, soon: true },
    {
        title: 'Alertas',
        href: '#',
        icon: BellRing,
        soon: true,
        badge: canSeeCrew.value ? alerts.pendingTotal : 0,
    },
    { title: 'Guía de salud', href: '#', icon: BookOpen, soon: true },
]);

const adminItems = computed<NavItem[]>(() =>
    auth.can('users.manage')
        ? [{ title: 'Usuarios', href: usuarios(), icon: ShieldCheck }]
        : [],
);

const homeLink = computed(() =>
    auth.actor?.astronaut_id
        ? inicio()
        : canSeeCrew.value
          ? salud()
          : dashboard(),
);

onMounted(() => {
    if (canSeeCrew.value) {
        void alerts.loadPendingTotal();
    }
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="homeLink">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="missionItems" />
            <NavMain
                v-if="adminItems.length"
                :items="adminItems"
                label="Administración"
            />
        </SidebarContent>

        <SidebarFooter>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
