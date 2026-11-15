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

export function zoneName(parts: BodyPart[], code: BodyZoneCode): string {
    return parts.find((part) => part.code === code)?.name ?? code;
}
