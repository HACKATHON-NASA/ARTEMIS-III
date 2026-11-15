<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { BellRing, RadioTower, Rocket, Users } from '@lucide/vue';
import StatCard from './StatCard.vue';

const { t } = useI18n();

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
        :aria-label="t('Identidad.admin.summary')"
        data-reveal
    >
        <StatCard
            :label="t('Identidad.admin.statActiveUsers')"
            :value="stats.activeUsers"
            :total="stats.totalUsers"
            :hint="t('Identidad.admin.statActiveUsersHint')"
            :icon="Users"
            tone="sky"
        />
        <StatCard
            :label="t('Identidad.admin.statCrew')"
            :value="stats.crew"
            :hint="t('Identidad.admin.statCrewHint')"
            :icon="Rocket"
            tone="indigo"
        />
        <StatCard
            :label="t('Identidad.admin.statDevices')"
            :value="stats.onlineDevices"
            :total="stats.totalDevices"
            :hint="t('Identidad.admin.statDevicesHint')"
            :icon="RadioTower"
            tone="blue"
        />
        <StatCard
            :label="t('Identidad.admin.statAlerts')"
            :value="stats.pendingAlerts"
            :hint="t('Identidad.admin.statAlertsHint')"
            :icon="BellRing"
            tone="amber"
        />
    </section>
</template>
