import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { Assessment } from '../Types/assessment';

/**
 * El puntaje y el estado general de un astronauta (M2).
 *
 * ⚠️ HOY con datos de prueba (el ejemplo de la ficha de M2). Cuando exista
 * GET /api/v1/astronauts/{id}/assessment, load() pasa a usar `api()`.
 */
function mockAssessment(astronautId: number): Assessment {
    return {
        astronaut_id: astronautId,
        score: 74,
        status: 'yellow',
        body_status: {
            heart: 'red',
            brain: 'yellow',
            lungs: 'green',
            immune: 'green',
            metabolism: 'green',
            bones: 'yellow',
            whole_body: 'green',
            eyes: null,
            ear: null,
        },
        summary: 'Corazón en estado crítico. Revisa la recomendación.',
        evaluated_at: new Date(Date.now() - 6 * 60 * 1000).toISOString(),
    };
}

export const useAssessmentStore = defineStore('assessment', () => {
    const current = ref<Assessment | null>(null);

    async function load(astronautId: number): Promise<void> {
        await new Promise((resolve) => setTimeout(resolve, 250));
        current.value = mockAssessment(astronautId);
    }

    return { current, load };
});
