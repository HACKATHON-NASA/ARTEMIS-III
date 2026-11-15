<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    Activity,
    BellRing,
    BookOpen,
    ChartLine,
    ClipboardPen,
    Globe,
    HeartPulse,
    Rocket,
    ShieldCheck,
    SquarePlus,
    Trophy,
    Users,
} from '@lucide/vue';
import { computed, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';
import LanguageSwitch from '@/components/LanguageSwitch.vue';
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
import { useReminderClock } from '@/modules/Recordatorios/Composables/useReminderClock';
import { useLiveAlerts } from '@/modules/Alertas/Composables/useLiveAlerts';
import { useAlertsStore } from '@/modules/Alertas/Stores/useAlertsStore';
import {
    alertas,
    dashboard,
    indicaciones,
    logros,
    mision,
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
 * Las opciones sin pantalla todavía salen como "Pronto". Los títulos son
 * claves de i18n (NavMain los traduce).
 */
const auth = useAuthStore();
const alerts = useAlertsStore();
const { t } = useI18n();

const canSeeCrew = computed(() => auth.can('health.read.any'));

const missionItems = computed<NavItem[]>(() => [
    ...(auth.actor?.astronaut_id
        ? [{ title: 'nav.home', href: inicio(), icon: HeartPulse }]
        : []),
    ...(canSeeCrew.value
        ? [
              { title: 'nav.healthPanel', href: salud(), icon: Activity },
              { title: 'nav.crew', href: tripulacion(), icon: Users },
              {
                  title: 'nav.advice',
                  href: indicaciones(),
                  icon: ClipboardPen,
              },
          ]
        : []),
    ...(auth.can('measurements.write.own') && auth.actor?.astronaut_id
        ? [{ title: 'nav.record', href: registrar(), icon: SquarePlus }]
        : []),
    // Solo quien va en la nave: Tierra ve la historia en Tripulación y el Panel.
    ...(auth.actor?.astronaut_id
        ? [
              { title: 'nav.myEvolution', href: evolucion(), icon: ChartLine },
              { title: 'nav.achievements', href: logros(), icon: Trophy },
              { title: 'nav.myMission', href: mision(), icon: Globe },
          ]
        : []),
    // El médico de vuelo puede ver y cambiar el entorno de la misión.
    ...(!auth.actor?.astronaut_id &&
    auth.can('mission.update') &&
    !auth.can('users.manage')
        ? [{ title: 'nav.currentMission', href: mision(), icon: Globe }]
        : []),
    ...(auth.can('alerts.read')
        ? [
              {
                  title: 'nav.alerts',
                  href: alertas(),
                  icon: BellRing,
                  badge: alerts.pendingTotal,
              },
          ]
        : []),
    { title: 'nav.healthGuide', href: guia(), icon: BookOpen },
]);

/** El astronauta cuida su salud; Tierra y los médicos monitorean a la tripulación. */
const mainLabel = computed(() =>
    t(
        auth.actor?.astronaut_id && !canSeeCrew.value
            ? 'nav.myHealth'
            : 'nav.monitoring',
    ),
);

const adminItems = computed<NavItem[]>(() => [
    ...(auth.can('missions.manage')
        ? [{ title: 'nav.missions', href: misiones(), icon: Rocket }]
        : []),
    ...(auth.can('users.manage')
        ? [{ title: 'nav.users', href: usuarios(), icon: ShieldCheck }]
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

// La rutina avisa a su hora en cualquier pantalla (solo quien va en la nave).
useReminderClock(() => auth.actor?.astronaut_id ?? null);

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
        <!-- Fondo espacial siempre oscuro (estilos en app.css, "Sidebar
             espacial"): "dark" da a todo el menú los colores del tema
             oscuro y "sidebar-space" su paleta. display: contents, así
             no cambia el diseño. -->
        <div class="sidebar-space dark contents">
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
                        :title="t('nav.toggleMenu')"
                    />
                </div>
            </SidebarHeader>

            <SidebarContent>
                <NavMain :items="missionItems" :label="mainLabel" />
                <NavMain
                    v-if="adminItems.length"
                    :items="adminItems"
                    :label="t('nav.management')"
                />
            </SidebarContent>

            <SidebarFooter>
                <div
                    class="flex justify-center px-2 group-data-[collapsible=icon]:hidden"
                >
                    <LanguageSwitch dark />
                </div>
                <SidebarXp
                    v-if="auth.actor?.astronaut_id"
                    :astronaut-id="auth.actor.astronaut_id"
                />
                <NavUser />
            </SidebarFooter>
        </div>
    </Sidebar>
    <slot />
</template>
