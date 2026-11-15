<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { logros } from '@/routes';
import { useMissionStore } from '../../Entorno/Stores/useMissionStore';
import { useGamificationStore } from '../Stores/useGamificationStore';
import XpBar from './XpBar.vue';

/**
 * El nivel y la racha siempre a la vista, en el menú lateral (ficha M7:
 * "para que los puntos se noten en toda la app"). Lleva a Logros. Se oculta
 * con el menú cerrado.
 */
const props = defineProps<{ astronautId: number }>();

const store = useGamificationStore();
const mission = useMissionStore();

onMounted(async () => {
    if (!mission.current) {
        await mission.load();
    }

    if (store.profile?.astronaut_id !== props.astronautId) {
        await store.load(props.astronautId, mission.current?.day ?? 1);
    }
});
</script>

<template>
    <Link
        v-if="store.profile"
        :href="logros()"
        class="mx-2 mb-1 block rounded-xl border bg-sidebar-accent/40 p-3 transition group-data-[collapsible=icon]:hidden hover:border-sky-500/40"
        aria-label="Ver mis logros"
    >
        <XpBar
            compact
            :points="store.profile.points"
            :level="store.profile.level"
            :progress="store.profile.level_progress"
        />
        <p class="mt-2 text-xs text-muted-foreground">
            🔥 {{ store.profile.streak_days }} días de racha
        </p>
    </Link>
</template>
