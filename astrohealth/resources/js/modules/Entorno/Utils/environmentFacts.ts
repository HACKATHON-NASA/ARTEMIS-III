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
import { formatNumber, t } from '@/lib/i18n';
import type { EnvironmentData, RiskCode } from '../Types/environment';
import type { EnvironmentCode } from '../Types/mission';

/**
 * Lo que cambia para el cuerpo en cada entorno (ficha M6 §3): gravedad,
 * retraso con Tierra y los riesgos que se destacan, explicados en simple.
 *
 * Los números y QUÉ riesgos tiene cada entorno vienen de GET
 * /api/v1/environments (config/environments.php): applyEnvironmentFacts()
 * los pone aquí al cargar. Los textos de cada riesgo (basados en la NASA
 * HRP) son de presentación: salen de Entorno/Locales en el idioma de ahora.
 */
export type EnvironmentRisk = {
    icon: Component;
    readonly title: string;
    readonly body: string;
    readonly organ: string;
};

export type EnvironmentFacts = {
    readonly label: string;
    gravityG: number;
    delaySeconds: number;
    risks: EnvironmentRisk[];
};

/** Radiación de fondo en la Tierra: unos 2,4 mSv al año (≈ 0,0066 mSv/día). */
export const EARTH_DOSE_PER_DAY = 2.4 / 365;

function risk(code: RiskCode, icon: Component): EnvironmentRisk {
    return {
        icon: markRaw(icon),
        get title() {
            return t(`Entorno.risks.${code}.title`);
        },
        get organ() {
            return t(`Entorno.risks.${code}.organ`);
        },
        get body() {
            return t(`Entorno.risks.${code}.body`);
        },
    };
}

export const RISKS: Record<RiskCode, EnvironmentRisk> = {
    microgravity: risk('microgravity', Bone),
    fluids: risk('fluids', Eye),
    radiation: risk('radiation', Radiation),
    isolation: risk('isolation', Users),
    distance: risk('distance', RadioTower),
    dust: risk('dust', Wind),
    stress: risk('stress', Brain),
};

function facts(
    code: EnvironmentCode,
    gravityG: number,
    delaySeconds: number,
    risks: EnvironmentRisk[],
): EnvironmentFacts {
    return {
        gravityG,
        delaySeconds,
        risks,
        get label() {
            return t(`Entorno.where.${code}`);
        },
    };
}

/** Valores de arranque (los mismos de config/environments.php) hasta que llega la API. */
export const ENVIRONMENT_FACTS = reactive<
    Record<EnvironmentCode, EnvironmentFacts>
>({
    iss: facts('iss', 0, 1, [
        RISKS.microgravity,
        RISKS.stress,
        RISKS.isolation,
    ]),
    transit: facts('transit', 0, 300, [
        RISKS.radiation,
        RISKS.microgravity,
        RISKS.isolation,
        RISKS.distance,
    ]),
    moon: facts('moon', 0.166, 1.3, [
        RISKS.radiation,
        RISKS.dust,
        RISKS.fluids,
    ]),
    mars: facts('mars', 0.38, 1200, [
        RISKS.distance,
        RISKS.radiation,
        RISKS.isolation,
        RISKS.fluids,
    ]),
});

/** Pone los números y los riesgos del backend en cada entorno. */
export function applyEnvironmentFacts(list: EnvironmentData[]): void {
    for (const data of list) {
        ENVIRONMENT_FACTS[data.code] = facts(
            data.code,
            data.gravity_g,
            data.ground_delay_seconds,
            data.risks
                .filter((code) => code in RISKS)
                .map((code) => RISKS[code]),
        );
    }
}

/** "1 s", "5 min", "20 min". */
export function formatDelay(seconds: number): string {
    return seconds >= 60
        ? t('units.minutes', { value: Math.round(seconds / 60) })
        : t('units.seconds', { value: formatNumber(seconds) });
}
