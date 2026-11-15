import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { CrewMember } from '../Types/crew';

/**
 * La tripulación de la misión (M6).
 *
 * ⚠️ HOY con datos de prueba (los 4 astronautas de MissionSeeder). Cuando
 * exista GET /api/v1/astronauts, load() pasa a usar `api()`.
 */
const MOCK_CREW: CrewMember[] = [
    { id: 1, name: 'Elena Vargas', crew_role: 'Comandante' },
    { id: 2, name: 'Kenji Mori', crew_role: 'Ingeniero de vuelo' },
    { id: 3, name: 'Amara Okafor', crew_role: 'Especialista de misión' },
    { id: 4, name: 'Lucas Ferreira', crew_role: 'Médico de la tripulación' },
];

export const useCrewStore = defineStore('crew', () => {
    const members = ref<CrewMember[]>([]);

    const ids = computed(() => members.value.map((member) => member.id));

    function find(id: number): CrewMember | null {
        return members.value.find((member) => member.id === id) ?? null;
    }

    async function load(): Promise<void> {
        if (members.value.length > 0) {
            return;
        }

        await new Promise((resolve) => setTimeout(resolve, 150));
        members.value = MOCK_CREW;
    }

    return { members, ids, find, load };
});
