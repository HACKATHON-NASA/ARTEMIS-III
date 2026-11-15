import { defineStore } from 'pinia';
import { ref } from 'vue';
import { api, ApiError } from '@/lib/api';
import type { Assessment } from '../Types/assessment';

/**
 * El puntaje y el estado general (M2): de un astronauta (`current`) y de
 * toda la tripulación (`crew`, para la vista de Tripulación).
 *
 * GET /api/v1/astronauts/{id}/assessment. El backend lo recalcula cada vez
 * que se guardan mediciones; un 404 (EVALUATION_NO_DATA) significa que esa
 * persona todavía no tiene datos.
 */
async function latestOf(astronautId: number): Promise<Assessment | null> {
    try {
        return await api<Assessment>(`/astronauts/${astronautId}/assessment`);
    } catch (error) {
        if (error instanceof ApiError && error.status === 404) {
            return null;
        }

        throw error;
    }
}

export const useAssessmentStore = defineStore('assessment', () => {
    const current = ref<Assessment | null>(null);
    const crew = ref<Assessment[]>([]);

    async function load(astronautId: number): Promise<void> {
        current.value = await latestOf(astronautId);
    }

    async function loadCrew(astronautIds: number[]): Promise<void> {
        const list = await Promise.all(astronautIds.map((id) => latestOf(id)));
        crew.value = list.filter((item) => item !== null);
    }

    return { current, crew, load, loadCrew };
});
