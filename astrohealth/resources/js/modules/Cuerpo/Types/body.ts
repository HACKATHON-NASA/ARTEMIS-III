import type {
    BodyZoneCode,
    IndicatorCode,
    MaybeStatus,
    Status,
} from '@/types/health';

/**
 * Tipos de M3 · Cuerpo, con la forma de la API:
 * GET /api/v1/body-parts y GET /api/v1/astronauts/{id}/body-status.
 */

export type BodyPart = {
    code: BodyZoneCode;
    name: string;
    function_text: string;
    space_risk_text: string;
    hrp_hazard: string;
    indicators: IndicatorCode[];
};

export type ZoneValue = {
    indicator: IndicatorCode;
    value: number;
    unit: string;
    status: Status;
    measured_at: string;
};

export type ZoneStatus = {
    status: MaybeStatus;
    values: ZoneValue[];
};

export type BodyStatus = Record<BodyZoneCode, ZoneStatus>;
