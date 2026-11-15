<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    BellRing,
    BookOpen,
    ChartLine,
    HeartPulse,
    Rocket,
    ShieldCheck,
    SquarePlus,
    Trophy,
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
    SidebarTrigger,
} from '@/components/ui/sidebar';
import SidebarXp from '@/modules/Gamificacion/Components/SidebarXp.vue';
import { useLiveAlerts } from '@/modules/Alertas/Composables/useLiveAlerts';
import { useAlertsStore } from '@/modules/Alertas/Stores/useAlertsStore';
import {
    alertas,
    dashboard,
    logros,
    evolucion,
    guia,
    inicio,
    misiones,
    registrar,
    salud,
    tripulacion,
    usuarios,
} from '@/routes';
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
    ...(auth.can('measurements.write.own') && auth.actor?.astronaut_id
        ? [{ title: 'Registrar datos', href: registrar(), icon: SquarePlus }]
        : []),
    // Solo quien va en la nave: Tierra ve la historia en Tripulación y el Panel.
    ...(auth.actor?.astronaut_id
        ? [
              { title: 'Mi evolución', href: evolucion(), icon: ChartLine },
              { title: 'Logros', href: logros(), icon: Trophy },
          ]
        : []),
    ...(auth.can('alerts.read')
        ? [
              {
                  title: 'Alertas',
                  href: alertas(),
                  icon: BellRing,
                  badge: alerts.pendingTotal,
              },
          ]
        : []),
    { title: 'Guía de salud', href: guia(), icon: BookOpen },
]);

/** El astronauta cuida su salud; Tierra y los médicos monitorean a la tripulación. */
const mainLabel = computed(() =>
    auth.actor?.astronaut_id && !canSeeCrew.value ? 'Mi salud' : 'Monitoreo',
);

const adminItems = computed<NavItem[]>(() => [
    ...(auth.can('missions.manage')
        ? [{ title: 'Misiones', href: misiones(), icon: Rocket }]
        : []),
    ...(auth.can('users.manage')
        ? [{ title: 'Usuarios', href: usuarios(), icon: ShieldCheck }]
        : []),
]);

const homeLink = computed(() =>
    auth.actor?.astronaut_id
        ? inicio()
        : canSeeCrew.value
          ? salud()
          : dashboard(),
);

// Alertas en vivo en cualquier pantalla: avisa a quien atiende (Tierra y el
// médico de la tripulación) cuando llega algo nuevo.
useLiveAlerts(() => canSeeCrew.value);

// Contador de alertas sin confirmar: de toda la tripulación o, para el
// astronauta, solo las suyas.
onMounted(() => {
    if (canSeeCrew.value) {
        void alerts.loadPendingTotal(null);
    } else if (auth.actor?.astronaut_id) {
        void alerts.loadPendingTotal(auth.actor.astronaut_id);
    }
});
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <!-- Logo y, al lado, el botón de abrir/cerrar el menú (antes iba en
                 una barra arriba que gastaba pantalla). Cerrado, van apilados. -->
            <div
                class="flex items-center gap-1 group-data-[collapsible=icon]:flex-col"
            >
                <SidebarMenu class="min-w-0 flex-1">
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" as-child>
                            <Link :href="homeLink">
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
                <SidebarTrigger
                    class="hidden shrink-0 text-muted-foreground hover:text-foreground md:inline-flex"
                    title="Abrir o cerrar el menú"
                />
            </div>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="missionItems" :label="mainLabel" />
            <NavMain
                v-if="adminItems.length"
                :items="adminItems"
                label="Gestión"
            />
        </SidebarContent>

        <SidebarFooter>
            <SidebarXp
                v-if="auth.actor?.astronaut_id"
                :astronaut-id="auth.actor.astronaut_id"
            />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
