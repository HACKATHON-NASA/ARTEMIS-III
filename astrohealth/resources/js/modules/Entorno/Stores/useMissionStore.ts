import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { CurrentMission, EnvironmentCode } from '../Types/mission';
import { ENVIRONMENT_FACTS } from '../Utils/environmentFacts';

/**
 * La misión actual (M6).
 *
 * ⚠️ HOY con datos de prueba. Cuando exista GET /api/v1/missions/current,
 * load() pasa a usar `api()`; changeEnvironment() será PATCH /missions/{id}
 * (permiso mission.update), que publica MissionEnvironmentChanged y hace que
 * M1 vuelva a evaluar las últimas mediciones con los umbrales del entorno.
 * El entorno elegido en la demo se recuerda en este navegador.
 */
const KEY = 'astrohealth.demo.mission.environment';

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

function withEnvironment(
    mission: CurrentMission,
    code: EnvironmentCode,
): CurrentMission {
    const facts = ENVIRONMENT_FACTS[code];

    return {
        ...mission,
        environment: code,
        environment_label: facts.label,
        gravity_g: facts.gravityG,
        ground_delay_seconds: facts.delaySeconds,
    };
}

function savedEnvironment(): EnvironmentCode | null {
    try {
        const value = localStorage.getItem(KEY);

        return value && value in ENVIRONMENT_FACTS
            ? (value as EnvironmentCode)
            : null;
    } catch {
        return null;
    }
}

export const useMissionStore = defineStore('mission', () => {
    const current = ref<CurrentMission | null>(null);

    async function load(): Promise<void> {
        await new Promise((resolve) => setTimeout(resolve, 200));
        const saved = savedEnvironment();
        current.value = saved
            ? withEnvironment(MOCK_MISSION, saved)
            : { ...MOCK_MISSION };
    }

    async function changeEnvironment(code: EnvironmentCode): Promise<void> {
        await new Promise((resolve) => setTimeout(resolve, 400));

        if (current.value) {
            current.value = withEnvironment(current.value, code);
        }

        try {
            localStorage.setItem(KEY, code);
        } catch {
            // Sin localStorage: el cambio dura hasta recargar.
        }
    }

    return { current, load, changeEnvironment };
});
