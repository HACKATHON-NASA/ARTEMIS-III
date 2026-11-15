import { Globe, Moon, Rocket, Satellite } from '@lucide/vue';
import { markRaw, reactive } from 'vue';
import type { Component } from 'vue';
import { formatNumber, t } from '@/lib/i18n';
import type { EnvironmentData } from '../Types/environment';
import type { EnvironmentCode } from '../Types/mission';
import { formatDelay } from './environmentFacts';

/**
 * Los 4 entornos de misión para las tarjetas y los textos.
 *
 * Los NÚMEROS (gravedad, radiación, retraso) vienen de GET /api/v1/environments
 * (config/environments.php): applyEnvironments() los pone aquí al cargar. Lo
 * que se ve antes de esa respuesta son los mismos valores, solo de arranque.
 * El nombre, el ícono y el color son de presentación y viven aquí; los
 * textos (nombre, "0.38 g", "0.6 mSv/day") salen en el idioma de ahora.
 */
export type EnvironmentInfo = {
    code: EnvironmentCode;
    readonly name: string;
    readonly gravity: string;
    readonly radiation: string;
    /** Dosis de referencia de radiación en mSv/día. */
    radiationBase: number;
    readonly delay: string;
    gravityG: number;
    delaySeconds: number;
    icon: Component;
    /** Clases del fondo de la tarjeta (degradado del entorno). */
    tint: string;
};

function environment(
    code: EnvironmentCode,
    gravityG: number,
    radiationBase: number,
    delaySeconds: number,
    icon: Component,
    tint: string,
): EnvironmentInfo {
    return {
        code,
        gravityG,
        radiationBase,
        delaySeconds,
        icon: markRaw(icon),
        tint,
        get name() {
            return t(`environments.${code}`);
        },
        get gravity() {
            return formatGravity(this.gravityG);
        },
        get radiation() {
            return t('units.mSvPerDay', {
                value: formatNumber(this.radiationBase),
            });
        },
        get delay() {
            return formatDelay(this.delaySeconds);
        },
    };
}

export const ENVIRONMENTS = reactive<EnvironmentInfo[]>([
    environment('iss', 0, 0.5, 1, Satellite, 'from-sky-500/25 via-sky-500/5'),
    environment(
        'transit',
        0,
        1.8,
        300,
        Rocket,
        'from-indigo-500/25 via-indigo-500/5',
    ),
    environment(
        'moon',
        0.166,
        1.4,
        1.3,
        Moon,
        'from-slate-400/30 via-slate-400/5',
    ),
    environment(
        'mars',
        0.38,
        0.6,
        1200,
        Globe,
        'from-orange-500/25 via-orange-500/5',
    ),
]);

export function environmentInfo(code: EnvironmentCode): EnvironmentInfo {
    return ENVIRONMENTS.find((item) => item.code === code) ?? ENVIRONMENTS[0];
}

/** "0.38 g"; sin gravedad apreciable, "~0 g". */
export function formatGravity(g: number): string {
    return g === 0 ? '~0 g' : `${formatNumber(g)} g`;
}

/** Pone los números del backend en las tarjetas de cada entorno. */
export function applyEnvironmentInfo(list: EnvironmentData[]): void {
    for (const data of list) {
        const info = ENVIRONMENTS.find((item) => item.code === data.code);

        if (info) {
            info.gravityG = data.gravity_g;
            info.radiationBase = data.radiation_msv_day;
            info.delaySeconds = data.ground_delay_seconds;
        }
    }
}

/**
 * Los cargos de la tripulación (para crear un astronauta). El valor que se
 * guarda es el de la izquierda (así ya están en la base); en pantalla se
 * muestra traducido con crewRoleLabel().
 */
const CREW_ROLE_KEYS: Record<string, string> = {
    Comandante: 'commander',
    Piloto: 'pilot',
    'Ingeniero de vuelo': 'flightEngineer',
    'Especialista de misión': 'missionSpecialist',
    'Médico de la tripulación': 'medicalOfficer',
};

export const CREW_ROLES = Object.keys(CREW_ROLE_KEYS);

/** El cargo en el idioma de ahora (uno que no está en la lista sale tal cual). */
export function crewRoleLabel(value: string): string {
    const key = CREW_ROLE_KEYS[value];

    return key ? t(`crewRoles.${key}`) : value;
}
