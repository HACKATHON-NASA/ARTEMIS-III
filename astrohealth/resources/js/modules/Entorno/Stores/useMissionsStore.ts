import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api } from '@/lib/api';
import type {
    Astronaut,
    Mission,
    MissionFormData,
    MissionView,
} from '../Types/missions';
import { useEnvironmentsStore } from './useEnvironmentsStore';

/**
 * Las misiones y sus tripulantes (M6). Lo usan la pantalla Misiones, el
 * formulario de usuarios y la tripulación de cada pantalla.
 *
 * GET /api/v1/missions trae cada misión con su estado, su día y su
 * tripulación, ya ordenadas (las en curso primero). Administración ve
 * todas; los demás, solo la suya. Crear: POST /missions. Tripulantes:
 * POST/PATCH /astronauts (missions.manage).
 */
export type AstronautData = Omit<Astronaut, 'id'>;

export const useMissionsStore = defineStore('missions', () => {
    const views = ref<MissionView[]>([]);
    const loaded = ref(false);
    const environments = useEnvironmentsStore();

    const missions = computed<Mission[]>(() =>
        views.value.map(
            ({ id, name, environment, start_date, day_count, objective }) => ({
                id,
                name,
                environment,
                start_date,
                day_count,
                objective,
            }),
        ),
    );

    const astronauts = computed<Astronaut[]>(() =>
        views.value.flatMap((mission) => mission.crew),
    );

    function find(id: number | null): MissionView | null {
        return views.value.find((mission) => mission.id === id) ?? null;
    }

    async function load(force = false): Promise<void> {
        if (loaded.value && !force) {
            return;
        }

        const [list] = await Promise.all([
            api<MissionView[]>('/missions'),
            environments.load(),
        ]);
        views.value = list;
        loaded.value = true;
    }

    async function createMission(data: MissionFormData): Promise<Mission> {
        const created = await api<MissionView>('/missions', {
            method: 'POST',
            body: { ...data, name: data.name.trim() },
        });
        // Volver a pedir la lista: el backend decide el orden y el estado.
        await load(true);

        return created;
    }

    /** Pone el tripulante en la misión que corresponde (y lo saca de otra). */
    function place(astronaut: Astronaut): void {
        for (const mission of views.value) {
            mission.crew = mission.crew.filter(
                (member) => member.id !== astronaut.id,
            );

            if (mission.id === astronaut.mission_id) {
                mission.crew.push(astronaut);
            }
        }
    }

    /** Crea el tripulante (al crear un usuario de la nave). */
    async function addAstronaut(data: AstronautData): Promise<Astronaut> {
        const astronaut = await api<Astronaut>('/astronauts', {
            method: 'POST',
            body: data,
        });
        place(astronaut);

        return astronaut;
    }

    /** Cambia el nombre, el cargo o la misión de un tripulante. */
    async function updateAstronaut(
        id: number,
        data: AstronautData,
    ): Promise<Astronaut> {
        const astronaut = await api<Astronaut>(`/astronauts/${id}`, {
            method: 'PATCH',
            body: data,
        });
        place(astronaut);

        return astronaut;
    }

    return {
        missions,
        astronauts,
        views,
        find,
        load,
        createMission,
        addAstronaut,
        updateAstronaut,
    };
});
