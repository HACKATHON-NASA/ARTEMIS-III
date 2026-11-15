<script setup lang="ts">
import { BellRing, RadioTower, Rocket, Users } from '@lucide/vue';
import StatCard from './StatCard.vue';

/**
 * El resumen de la misión en cuatro cifras.
 */
defineProps<{
    stats: {
        activeUsers: number;
        totalUsers: number;
        crew: number;
        onlineDevices: number;
        totalDevices: number;
        pendingAlerts: number;
    };
}>();
</script>

<template>
    <section
        class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"
        aria-label="Resumen"
        data-reveal
    >
        <StatCard
            label="Usuarios activos"
            :value="stats.activeUsers"
            :total="stats.totalUsers"
            hint="Pueden iniciar sesión ahora"
            :icon="Users"
            tone="sky"
        />
        <StatCard
            label="Tripulación a bordo"
            :value="stats.crew"
            hint="Astronautas de la misión"
            :icon="Rocket"
            tone="indigo"
        />
        <StatCard
            label="Dispositivos en línea"
            :value="stats.onlineDevices"
            :total="stats.totalDevices"
            hint="Enviaron datos en los últimos 5 min"
            :icon="RadioTower"
            tone="blue"
        />
        <StatCard
            label="Alertas por confirmar"
            :value="stats.pendingAlerts"
            hint="Esperan respuesta desde Tierra"
            :icon="BellRing"
            tone="amber"
        />
    </section>
</template>
