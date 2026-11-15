import {
    Brain,
    Bone,
    Eye,
    Radiation,
    RadioTower,
    Users,
    Wind,
} from '@lucide/vue';
import { markRaw, reactive } from 'vue';
import type { Component } from 'vue';
import type { EnvironmentData, RiskCode } from '../Types/environment';
import type { EnvironmentCode } from '../Types/mission';

/**
 * Lo que cambia para el cuerpo en cada entorno (ficha M6 §3): gravedad,
 * retraso con Tierra y los riesgos que se destacan, explicados en simple.
 *
 * Los números y QUÉ riesgos tiene cada entorno vienen de GET
 * /api/v1/environments (config/environments.php): applyEnvironmentFacts()
 * los pone aquí al cargar. Los textos de cada riesgo (basados en la NASA
 * HRP) son de presentación y viven aquí.
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

export const RISKS: Record<RiskCode, EnvironmentRisk> = {
    microgravity: {
        icon: markRaw(Bone),
        title: 'Microgravedad',
        organ: 'Huesos y músculos',
        body: 'Sin peso que cargar, los huesos pierden densidad y los músculos se atrofian. El ejercicio diario es la defensa.',
    },
    fluids: {
        icon: markRaw(Eye),
        title: 'Gravedad alterada',
        organ: 'Corazón y ojos',
        body: 'Los líquidos suben hacia la cabeza: cambia la presión y puede inflamarse el nervio óptico (SANS).',
    },
    radiation: {
        icon: markRaw(Radiation),
        title: 'Radiación',
        organ: 'Todo el cuerpo',
        body: 'Fuera del escudo magnético de la Tierra la dosis diaria sube: más riesgo de cáncer y daño en tejidos.',
    },
    isolation: {
        icon: markRaw(Users),
        title: 'Aislamiento y encierro',
        organ: 'Mente',
        body: 'Pocas personas, espacio pequeño y lejos de casa: el sueño y el ánimo sufren. Hablar a tiempo ayuda.',
    },
    distance: {
        icon: markRaw(RadioTower),
        title: 'Distancia de la Tierra',
        organ: 'Toda la tripulación',
        body: 'La ayuda de Tierra tarda: la tripulación debe resolver sola. Por eso cada uno vigila su salud.',
    },
    dust: {
        icon: markRaw(Wind),
        title: 'Polvo lunar',
        organ: 'Pulmones y ojos',
        body: 'El regolito es fino y cortante: irrita los pulmones y los ojos si entra al módulo.',
    },
    stress: {
        icon: markRaw(Brain),
        title: 'Ciclos de luz alterados',
        organ: 'Cerebro',
        body: '16 amaneceres al día en órbita desordenan el reloj del cuerpo y el sueño.',
    },
};

/** Valores de arranque (los mismos de config/environments.php) hasta que llega la API. */
export const ENVIRONMENT_FACTS = reactive<
    Record<EnvironmentCode, EnvironmentFacts>
>({
    iss: {
        label: 'En órbita terrestre',
        gravityG: 0,
        delaySeconds: 1,
        risks: [RISKS.microgravity, RISKS.stress, RISKS.isolation],
    },
    transit: {
        label: 'En tránsito',
        gravityG: 0,
        delaySeconds: 300,
        risks: [
            RISKS.radiation,
            RISKS.microgravity,
            RISKS.isolation,
            RISKS.distance,
        ],
    },
    moon: {
        label: 'En la Luna',
        gravityG: 0.166,
        delaySeconds: 1.3,
        risks: [RISKS.radiation, RISKS.dust, RISKS.fluids],
    },
    mars: {
        label: 'Rumbo a Marte',
        gravityG: 0.38,
        delaySeconds: 1200,
        risks: [RISKS.distance, RISKS.radiation, RISKS.isolation, RISKS.fluids],
    },
});

/** Pone los números y los riesgos del backend en cada entorno. */
export function applyEnvironmentFacts(list: EnvironmentData[]): void {
    for (const data of list) {
        ENVIRONMENT_FACTS[data.code] = {
            label: data.label,
            gravityG: data.gravity_g,
            delaySeconds: data.ground_delay_seconds,
            risks: data.risks
                .filter((code) => code in RISKS)
                .map((code) => RISKS[code]),
        };
    }
}

/** "1 s", "5 min", "20 min". */
export function formatDelay(seconds: number): string {
    return seconds >= 60
        ? `${Math.round(seconds / 60)} min`
        : `${seconds.toLocaleString('es')} s`;
}
