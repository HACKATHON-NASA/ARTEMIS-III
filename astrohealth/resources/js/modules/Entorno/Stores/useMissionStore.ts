import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { CurrentMission } from '../Types/mission';

/**
 * La misión actual (M6).
 *
 * ⚠️ HOY con datos de prueba. Cuando exista GET /api/v1/missions/current,
 * load() pasa a usar `api()` de @/lib/api.
 */
const MOCK_MISSION: CurrentMission = {
    id: 1,
    name: 'Ares-1',
    environment: 'mars',
    environment_label: 'Rumbo a Marte',
    gravity_g: 0.38,
    ground_delay_seconds: 1200,
    day: 24,
    day_count: 30,
};

export const useMissionStore = defineStore('mission', () => {
    const current = ref<CurrentMission | null>(null);

    async function load(): Promise<void> {
        await new Promise((resolve) => setTimeout(resolve, 200));
        current.value = { ...MOCK_MISSION };
    }

    return { current, load };
});
