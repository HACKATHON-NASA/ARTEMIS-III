import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api } from '@/lib/api';
import type { EnvironmentData } from '../Types/environment';
import { applyEnvironmentFacts } from '../Utils/environmentFacts';
import { applyEnvironmentInfo } from '../Utils/environments';

/**
 * Los 4 entornos de GET /api/v1/environments (M6). Se cargan una vez y sus
 * números llenan ENVIRONMENTS y ENVIRONMENT_FACTS, que usan las pantallas.
 * Los cargan useMissionStore y useMissionsStore antes de mostrar nada.
 */
export const useEnvironmentsStore = defineStore('environments', () => {
    const list = ref<EnvironmentData[]>([]);
    let pending: Promise<void> | null = null;

    function load(): Promise<void> {
        pending ??= api<EnvironmentData[]>('/environments')
            .then((data) => {
                list.value = data;
                applyEnvironmentInfo(data);
                applyEnvironmentFacts(data);
            })
            .catch((error: unknown) => {
                pending = null;
                throw error;
            });

        return pending;
    }

    return { list, load };
});
