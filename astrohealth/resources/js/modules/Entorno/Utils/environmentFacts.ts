import {
    Brain,
    Bone,
    Eye,
    Radiation,
    RadioTower,
    Users,
    Wind,
} from '@lucide/vue';
import type { Component } from 'vue';
import type { EnvironmentCode } from '../Types/mission';

/**
 * Lo que cambia para el cuerpo en cada entorno (ficha M6 §3): gravedad,
 * retraso con Tierra y los riesgos que se destacan, explicados en simple.
 * ⚠️ Cuando exista GET /api/v1/environments, los números vienen de
 * config/environments.php; los textos se basan en la NASA HRP.
 */
export type EnvironmentRisk = {
    icon: Component;
    title: string;
    body: string;
    organ: string;
};

export type EnvironmentFacts = {
    label: string;
    gravityG: number;
    delaySeconds: number;
    risks: EnvironmentRisk[];
};

/** Radiación de fondo en la Tierra: unos 2,4 mSv al año (≈ 0,0066 mSv/día). */
export const EARTH_DOSE_PER_DAY = 2.4 / 365;

const RISK = {
    microgravity: {
        icon: Bone,
        title: 'Microgravedad',
        organ: 'Huesos y músculos',
        body: 'Sin peso que cargar, los huesos pierden densidad y los músculos se atrofian. El ejercicio diario es la defensa.',
    },
    fluids: {
        icon: Eye,
        title: 'Gravedad alterada',
        organ: 'Corazón y ojos',
        body: 'Los líquidos suben hacia la cabeza: cambia la presión y puede inflamarse el nervio óptico (SANS).',
    },
    radiation: {
        icon: Radiation,
        title: 'Radiación',
        organ: 'Todo el cuerpo',
        body: 'Fuera del escudo magnético de la Tierra la dosis diaria sube: más riesgo de cáncer y daño en tejidos.',
    },
    isolation: {
        icon: Users,
        title: 'Aislamiento y encierro',
        organ: 'Mente',
        body: 'Pocas personas, espacio pequeño y lejos de casa: el sueño y el ánimo sufren. Hablar a tiempo ayuda.',
    },
    distance: {
        icon: RadioTower,
        title: 'Distancia de la Tierra',
        organ: 'Toda la tripulación',
        body: 'La ayuda de Tierra tarda: la tripulación debe resolver sola. Por eso cada uno vigila su salud.',
    },
    dust: {
        icon: Wind,
        title: 'Polvo lunar',
        organ: 'Pulmones y ojos',
        body: 'El regolito es fino y cortante: irrita los pulmones y los ojos si entra al módulo.',
    },
    stress: {
        icon: Brain,
        title: 'Ciclos de luz alterados',
        organ: 'Cerebro',
        body: '16 amaneceres al día en órbita desordenan el reloj del cuerpo y el sueño.',
    },
};

export const ENVIRONMENT_FACTS: Record<EnvironmentCode, EnvironmentFacts> = {
    iss: {
        label: 'En órbita terrestre',
        gravityG: 0,
        delaySeconds: 1,
        risks: [RISK.microgravity, RISK.stress, RISK.isolation],
    },
    transit: {
        label: 'En tránsito',
        gravityG: 0,
        delaySeconds: 300,
        risks: [
            RISK.radiation,
            RISK.microgravity,
            RISK.isolation,
            RISK.distance,
        ],
    },
    moon: {
        label: 'En la Luna',
        gravityG: 0.166,
        delaySeconds: 1.3,
        risks: [RISK.radiation, RISK.dust, RISK.fluids],
    },
    mars: {
        label: 'Rumbo a Marte',
        gravityG: 0.38,
        delaySeconds: 1200,
        risks: [RISK.distance, RISK.radiation, RISK.isolation, RISK.fluids],
    },
};

/** "1 s", "5 min", "20 min". */
export function formatDelay(seconds: number): string {
    return seconds >= 60
        ? `${Math.round(seconds / 60)} min`
        : `${seconds.toLocaleString('es')} s`;
}
