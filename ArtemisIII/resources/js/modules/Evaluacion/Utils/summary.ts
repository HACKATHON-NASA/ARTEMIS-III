import { t, tOr } from '@/lib/i18n';
import type { BodyZoneCode, MaybeStatus } from '@/types/health';

/**
 * El resumen del estado general en el idioma de ahora ("Heart critical;
 * Mind caution; the rest, normal."). Es la misma regla con la que el
 * backend (RuleEvaluator::summary) arma el `summary` en español: primero lo
 * rojo, luego lo amarillo, en el orden de las zonas.
 */
const SEVERITY = { red: 2, yellow: 1 } as const;

export function assessmentSummary(
    bodyStatus: Partial<Record<BodyZoneCode, MaybeStatus>>,
): string {
    const entries = Object.entries(bodyStatus) as [BodyZoneCode, MaybeStatus][];
    const flagged = entries
        .filter(
            (entry): entry is [BodyZoneCode, 'red' | 'yellow'] =>
                entry[1] === 'red' || entry[1] === 'yellow',
        )
        .sort((a, b) => SEVERITY[b[1]] - SEVERITY[a[1]]);

    if (flagged.length === 0) {
        return t('Evaluacion.summary.allGood');
    }

    const text = flagged
        .map(([zone, status]) =>
            t('Evaluacion.summary.zone', {
                zone: tOr(`Cuerpo.zones.${zone}.name`, zone),
                state: t(`Evaluacion.summary.${status}`),
            }),
        )
        .join('; ');

    return flagged.length < entries.length
        ? t('Evaluacion.summary.restNormal', { text })
        : t('Evaluacion.summary.end', { text });
}
