import { Globe, Moon, Rocket, Satellite } from '@lucide/vue';
import type { Component } from 'vue';
import type { EnvironmentCode } from '../Types/mission';

/**
 * Los 4 entornos de misión, como config/environments.php (ficha M6 §3).
 * ⚠️ Cuando exista GET /api/v1/environments, vienen de ahí.
 */
export type EnvironmentInfo = {
    code: EnvironmentCode;
    name: string;
    gravity: string;
    radiation: string;
    /** Dosis base de radiación en mSv/día (la referencia de M2). */
    radiationBase: number;
    delay: string;
    icon: Component;
    /** Clases del fondo de la tarjeta (degradado del entorno). */
    tint: string;
};

export const ENVIRONMENTS: EnvironmentInfo[] = [
    {
        code: 'iss',
        name: 'Estación Espacial',
        gravity: '~0 g',
        radiation: '0,5 mSv/día',
        radiationBase: 0.5,
        delay: '1 s',
        icon: Satellite,
        tint: 'from-sky-500/25 via-sky-500/5',
    },
    {
        code: 'transit',
        name: 'Tránsito',
        gravity: '0 g',
        radiation: '1,8 mSv/día',
        radiationBase: 1.8,
        delay: '5 min',
        icon: Rocket,
        tint: 'from-indigo-500/25 via-indigo-500/5',
    },
    {
        code: 'moon',
        name: 'Luna',
        gravity: '0,166 g',
        radiation: '1,4 mSv/día',
        radiationBase: 1.4,
        delay: '1,3 s',
        icon: Moon,
        tint: 'from-slate-400/30 via-slate-400/5',
    },
    {
        code: 'mars',
        name: 'Marte',
        gravity: '0,38 g',
        radiation: '0,6 mSv/día',
        radiationBase: 0.6,
        delay: '20 min',
        icon: Globe,
        tint: 'from-orange-500/25 via-orange-500/5',
    },
];

export function environmentInfo(code: EnvironmentCode): EnvironmentInfo {
    return ENVIRONMENTS.find((item) => item.code === code) ?? ENVIRONMENTS[0];
}

/** Los cargos de la tripulación (para crear un astronauta). */
export const CREW_ROLES = [
    'Comandante',
    'Piloto',
    'Ingeniero de vuelo',
    'Especialista de misión',
    'Médico de la tripulación',
];
