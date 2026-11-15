import { Globe, Moon, Rocket, Satellite } from '@lucide/vue';
import { markRaw, reactive } from 'vue';
import type { Component } from 'vue';
import type { EnvironmentData } from '../Types/environment';
import type { EnvironmentCode } from '../Types/mission';
import { formatDelay } from './environmentFacts';

/**
 * Los 4 entornos de misión para las tarjetas y los textos.
 *
 * Los NÚMEROS (gravedad, radiación, retraso) vienen de GET /api/v1/environments
 * (config/environments.php): applyEnvironments() los pone aquí al cargar. Lo
 * que se ve antes de esa respuesta son los mismos valores, solo de arranque.
 * El nombre, el ícono y el color son de presentación y viven aquí.
 */
export type EnvironmentInfo = {
    code: EnvironmentCode;
    name: string;
    gravity: string;
    radiation: string;
    /** Dosis de referencia de radiación en mSv/día. */
    radiationBase: number;
    delay: string;
    icon: Component;
    /** Clases del fondo de la tarjeta (degradado del entorno). */
    tint: string;
};

export const ENVIRONMENTS = reactive<EnvironmentInfo[]>([
    {
        code: 'iss',
        name: 'Estación Espacial',
        gravity: '~0 g',
        radiation: '0,5 mSv/día',
        radiationBase: 0.5,
        delay: '1 s',
        icon: markRaw(Satellite),
        tint: 'from-sky-500/25 via-sky-500/5',
    },
    {
        code: 'transit',
        name: 'Tránsito',
        gravity: '~0 g',
        radiation: '1,8 mSv/día',
        radiationBase: 1.8,
        delay: '5 min',
        icon: markRaw(Rocket),
        tint: 'from-indigo-500/25 via-indigo-500/5',
    },
    {
        code: 'moon',
        name: 'Luna',
        gravity: '0,166 g',
        radiation: '1,4 mSv/día',
        radiationBase: 1.4,
        delay: '1,3 s',
        icon: markRaw(Moon),
        tint: 'from-slate-400/30 via-slate-400/5',
    },
    {
        code: 'mars',
        name: 'Marte',
        gravity: '0,38 g',
        radiation: '0,6 mSv/día',
        radiationBase: 0.6,
        delay: '20 min',
        icon: markRaw(Globe),
        tint: 'from-orange-500/25 via-orange-500/5',
    },
]);

export function environmentInfo(code: EnvironmentCode): EnvironmentInfo {
    return ENVIRONMENTS.find((item) => item.code === code) ?? ENVIRONMENTS[0];
}

/** "0,38 g"; sin gravedad apreciable, "~0 g". */
export function formatGravity(g: number): string {
    return g === 0 ? '~0 g' : `${g.toLocaleString('es')} g`;
}

/** Pone los números del backend en las tarjetas de cada entorno. */
export function applyEnvironmentInfo(list: EnvironmentData[]): void {
    for (const data of list) {
        const info = ENVIRONMENTS.find((item) => item.code === data.code);

        if (info) {
            info.name = data.name;
            info.gravity = formatGravity(data.gravity_g);
            info.radiationBase = data.radiation_msv_day;
            info.radiation = `${data.radiation_msv_day.toLocaleString('es')} mSv/día`;
            info.delay = formatDelay(data.ground_delay_seconds);
        }
    }
}

/** Los cargos de la tripulación (para crear un astronauta). */
export const CREW_ROLES = [
    'Comandante',
    'Piloto',
    'Ingeniero de vuelo',
    'Especialista de misión',
    'Médico de la tripulación',
];
