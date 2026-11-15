import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { CrewMember } from '../Types/crew';
import type { MissionView } from '../Types/missions';
import { useMissionStore } from './useMissionStore';
import { useMissionsStore } from './useMissionsStore';

/**
 * La tripulación de UNA misión (M6): la que se está mirando en Tripulación,
 * Panel de salud y Alertas. La misión elegida se comparte entre esas
 * pantallas (si administración elige Selene-2, las tres muestran Selene-2).
 *
 * Quien va en la nave (tiene astronaut_id) queda fijo en su propia misión.
 * Los demás empiezan en la misión actual y pueden cambiarla. El Panel de
 * salud pide `activeOnly`: allí solo se monitorean las misiones en curso.
 *
 * Sale de la API a través de useMissionsStore (GET /api/v1/missions y
 * GET /api/v1/astronauts?mission={id}).
 */
export const useCrewStore = defineStore('crew', () => {
    const missions = useMissionsStore();
    const current = useMissionStore();

    const missionId = ref<number | null>(null);
    /** true si la persona va en la nave: no puede ver otras misiones. */
    const locked = ref(false);
    /** true = solo se pueden elegir misiones en curso (Panel de salud). */
    const activeOnly = ref(false);

    const mission = computed<MissionView | null>(() =>
        missions.find(missionId.value),
    );

    const members = computed<CrewMember[]>(() =>
        (mission.value?.crew ?? []).map(({ id, name, crew_role }) => ({
            id,
            name,
            crew_role,
        })),
    );

    const ids = computed(() => members.value.map((member) => member.id));

    /** Las misiones que se pueden elegir (todas, o solo la suya). */
    const choices = computed<MissionView[]>(() =>
        locked.value && mission.value
            ? [mission.value]
            : missions.views.filter(
                  (item) => !activeOnly.value || item.status === 'active',
              ),
    );

    function canChoose(id: number | null): boolean {
        return choices.value.some((item) => item.id === id);
    }

    function find(id: number): CrewMember | null {
        return members.value.find((member) => member.id === id) ?? null;
    }

    /** La misión de un tripulante (de cualquier misión). */
    function missionOf(astronautId: number): number | null {
        return (
            missions.astronauts.find((item) => item.id === astronautId)
                ?.mission_id ?? null
        );
    }

    /**
     * Carga las misiones y fija cuál se mira: la del tripulante `own` (si va
     * en la nave) o, si aún no hay ninguna elegida, la misión actual.
     */
    async function load(
        own: number | null = null,
        options: { activeOnly?: boolean } = {},
    ): Promise<void> {
        await Promise.all([missions.load(), current.load()]);
        activeOnly.value = options.activeOnly ?? false;

        if (own !== null && missionOf(own) !== null) {
            missionId.value = missionOf(own);
            locked.value = true;

            return;
        }

        locked.value = false;

        // Si la elegida no vale aquí (p. ej. una terminada en el Panel de
        // salud): la misión actual o la primera que se pueda elegir.
        if (!canChoose(missionId.value)) {
            const fallback = current.current?.id ?? null;
            missionId.value = canChoose(fallback)
                ? fallback
                : (choices.value[0]?.id ?? null);
        }
    }

    function selectMission(id: number): void {
        if (!locked.value && canChoose(id)) {
            missionId.value = id;
        }
    }

    return {
        missionId,
        locked,
        mission,
        members,
        ids,
        choices,
        find,
        missionOf,
        load,
        selectMission,
    };
});
