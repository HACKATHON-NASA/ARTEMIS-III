import { tOr } from '@/lib/i18n';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus, ZoneValue } from '../Types/body';

const ORDER = { red: 0, yellow: 1, green: 2 } as const;

/** El valor que explica el color de una zona: el peor de sus indicadores. */
export function worstValue(
    status: BodyStatus,
    code: BodyZoneCode,
): ZoneValue | null {
    return (
        [...status[code].values].sort(
            (a, b) => ORDER[a.status] - ORDER[b.status],
        )[0] ?? null
    );
}

/**
 * Una zona del cuerpo con sus textos en el idioma de ahora (el catálogo de
 * zonas es fijo; si llega una que no se conoce, se usa el texto de la API).
 */
export function localizePart(part: BodyPart): BodyPart {
    const key = `Cuerpo.zones.${part.code}`;
    const { name, function_text, space_risk_text, hrp_hazard } = part;

    return {
        ...part,
        get name() {
            return tOr(`${key}.name`, name);
        },
        get function_text() {
            return tOr(`${key}.function`, function_text);
        },
        get space_risk_text() {
            return tOr(`${key}.risk`, space_risk_text);
        },
        get hrp_hazard() {
            return tOr(`${key}.hazard`, hrp_hazard);
        },
    };
}

export function zoneName(parts: BodyPart[], code: BodyZoneCode): string {
    return parts.find((part) => part.code === code)?.name ?? code;
}
