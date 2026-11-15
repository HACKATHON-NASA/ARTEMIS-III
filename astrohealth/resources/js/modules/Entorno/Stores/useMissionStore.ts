import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '@/lib/api';
import type { CurrentMission, EnvironmentCode } from '../Types/mission';
import { useEnvironmentsStore } from './useEnvironmentsStore';

/**
 * La misión actual (M6): GET /api/v1/missions/current. Para tripulantes y
 * médico de vuelo es la suya; para administración, la primera en curso.
 *
 * changeEnvironment() es PATCH /missions/{id}/environment (mission.update;
 * el médico, solo la suya). El backend publica MissionEnvironmentChanged
 * para que se vuelvan a evaluar las mediciones con los umbrales del entorno.
 */
export const useMissionStore = defineStore('mission', () => {
    const current = ref<CurrentMission | null>(null);
    const environments = useEnvironmentsStore();

    async function load(): Promise<void> {
        const [mission] = await Promise.all([
            api<CurrentMission>('/missions/current'),
            environments.load(),
        ]);
        current.value = mission;
    }

    async function changeEnvironment(code: EnvironmentCode): Promise<void> {
        if (current.value === null) {
            return;
        }

        current.value = await api<CurrentMission>(
            `/missions/${current.value.id}/environment`,
            { method: 'PATCH', body: { environment: code } },
        );
    }

    return { current, load, changeEnvironment };
});
