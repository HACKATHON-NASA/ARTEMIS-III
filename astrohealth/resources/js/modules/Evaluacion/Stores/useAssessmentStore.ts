import { defineStore } from 'pinia';
import { ref } from 'vue';
import { mockAssessmentFor } from '@/mocks/crewHealth';
import type { Assessment } from '../Types/assessment';

/**
 * El puntaje y el estado general (M2): de un astronauta (`current`) y de
 * toda la tripulación (`crew`, para la vista de Tripulación).
 *
 * ⚠️ HOY con datos de prueba compartidos (@/mocks/crewHealth). Cuando exista
 * GET /api/v1/astronauts/{id}/assessment, las acciones pasan a usar `api()`.
 */
const wait = (ms = 250) => new Promise((resolve) => setTimeout(resolve, ms));

export const useAssessmentStore = defineStore('assessment', () => {
    const current = ref<Assessment | null>(null);
    const crew = ref<Assessment[]>([]);

    async function load(astronautId: number): Promise<void> {
        await wait();
        current.value = mockAssessmentFor(astronautId);
    }

    async function loadCrew(astronautIds: number[]): Promise<void> {
        await wait();
        crew.value = astronautIds.map((id) => mockAssessmentFor(id));
    }

    return { current, crew, load, loadCrew };
});
