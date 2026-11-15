import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type {
    Astronaut,
    Mission,
    MissionFormData,
    MissionView,
} from '../Types/missions';
import { endDate, missionStatus } from '../Utils/missionDates';
import { mockAstronauts, mockMissions } from '../Utils/missionsMockData';

/**
 * Las misiones y sus tripulantes (M6, vista de administración). Lo usan la
 * pantalla Misiones y el formulario de usuarios (cada persona de la nave o
 * médico de vuelo va asociada a una misión).
 *
 * ⚠️ HOY con datos de prueba en memoria (se pierden al recargar). Cuando
 * existan GET/POST /api/v1/missions y POST/PATCH /api/v1/astronauts, el
 * interior de estas acciones pasa a usar `api()`; los componentes no cambian.
 */
const wait = (ms = 350) => new Promise((resolve) => setTimeout(resolve, ms));

export type AstronautData = Omit<Astronaut, 'id'>;

export const useMissionsStore = defineStore('missions', () => {
    const missions = ref<Mission[]>([]);
    const astronauts = ref<Astronaut[]>([]);
    const loaded = ref(false);

    /** Cada misión con su estado, su día y su tripulación. Las en curso primero. */
    const views = computed<MissionView[]>(() => {
        const order = { active: 0, planned: 1, finished: 2 } as const;

        return missions.value
            .map((mission) => {
                const { status, currentDay } = missionStatus(
                    mission.start_date,
                    mission.day_count,
                );

                return {
                    ...mission,
                    status,
                    current_day: currentDay,
                    end_date: endDate(mission.start_date, mission.day_count),
                    crew: astronauts.value.filter(
                        (astronaut) => astronaut.mission_id === mission.id,
                    ),
                };
            })
            .sort(
                (a, b) =>
                    order[a.status] - order[b.status] ||
                    b.start_date.localeCompare(a.start_date),
            );
    });

    function find(id: number | null): MissionView | null {
        return views.value.find((mission) => mission.id === id) ?? null;
    }

    async function load(): Promise<void> {
        if (loaded.value) {
            return;
        }

        await wait();
        missions.value = mockMissions();
        astronauts.value = mockAstronauts();
        loaded.value = true;
    }

    async function createMission(data: MissionFormData): Promise<Mission> {
        await wait();
        const mission: Mission = {
            id: Math.max(0, ...missions.value.map((item) => item.id)) + 1,
            name: data.name.trim(),
            environment: data.environment,
            start_date: data.start_date,
            day_count: data.day_count,
            objective: data.objective.trim(),
        };
        missions.value.push(mission);

        return mission;
    }

    /** Crea el tripulante (al crear un usuario de la nave). */
    async function addAstronaut(data: AstronautData): Promise<Astronaut> {
        await wait(150);
        const astronaut: Astronaut = {
            id: Math.max(0, ...astronauts.value.map((item) => item.id)) + 1,
            ...data,
            name: data.name.trim(),
        };
        astronauts.value.push(astronaut);

        return astronaut;
    }

    /** Cambia el nombre, el cargo o la misión de un tripulante. */
    async function updateAstronaut(
        id: number,
        data: AstronautData,
    ): Promise<Astronaut | null> {
        await wait(150);
        const astronaut = astronauts.value.find((item) => item.id === id);

        if (astronaut) {
            Object.assign(astronaut, { ...data, name: data.name.trim() });
        }

        return astronaut ?? null;
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
