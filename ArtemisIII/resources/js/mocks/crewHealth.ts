import { PROFILES } from './crewProfiles';
import type { Profile, Reading } from './crewProfiles';
import type {
    BodyZoneCode,
    IndicatorCode,
    MaybeStatus,
    Status,
} from '@/types/health';

/**
 * ⚠️ DATOS DE PRUEBA de la salud de la tripulación (Ares-1), en un solo lugar
 * para que el cuerpo (M3), el puntaje (M2) y las alertas cuenten la misma
 * historia. Imita lo que devolverá el backend (que es quien evalúa).
 * Se borra cuando existan las rutas de M1–M3.
 */

const ZONE_OF: Record<IndicatorCode, BodyZoneCode> = {
    hr: 'heart',
    bp_sys: 'heart',
    bp_dia: 'heart',
    sleep: 'brain',
    mood: 'brain',
    spo2: 'lungs',
    temp: 'immune',
    glucose: 'metabolism',
    exercise_min: 'bones',
    radiation: 'whole_body',
};

const ZONES: BodyZoneCode[] = [
    'heart',
    'brain',
    'eyes',
    'lungs',
    'bones',
    'ear',
    'immune',
    'metabolism',
    'whole_body',
];
const WORST: Record<Status, number> = { red: 2, yellow: 1, green: 0 };

/** Solo la tripulación de Ares-1 trae datos; los demás empiezan sin datos. */
function profileOf(astronautId: number): Profile | null {
    return PROFILES[astronautId] ?? null;
}

const UNITS: Record<IndicatorCode, string> = {
    hr: 'bpm',
    bp_sys: 'mmHg',
    bp_dia: 'mmHg',
    sleep: 'h',
    mood: '1-5',
    spo2: '%',
    temp: '°C',
    glucose: 'mg/dL',
    exercise_min: 'min',
    radiation: 'mSv',
};

/** Lo que se registró en esta carga de la página (M1), por astronauta. */
const RECORDED: Record<
    number,
    Partial<
        Record<IndicatorCode, { value: number; status: Status; at: number }>
    >
> = {};

/** Las lecturas del perfil con lo registrado encima (lo último manda). */
function readingsOf(astronautId: number): Reading[] {
    const recorded = RECORDED[astronautId] ?? {};
    const base = profileOf(astronautId)?.readings ?? [];
    const merged = base.map((reading): Reading => {
        const latest = recorded[reading[0]];

        return latest
            ? [
                  reading[0],
                  latest.value,
                  reading[2],
                  latest.status,
                  (Date.now() - latest.at) / 60_000,
              ]
            : reading;
    });

    // Lo registrado de indicadores que el perfil no tenía (o sin perfil).
    for (const [code, latest] of Object.entries(recorded) as [
        IndicatorCode,
        { value: number; status: Status; at: number },
    ][]) {
        if (!base.some((reading) => reading[0] === code)) {
            merged.push([
                code,
                latest.value,
                UNITS[code],
                latest.status,
                (Date.now() - latest.at) / 60_000,
            ]);
        }
    }

    return merged;
}

/** Guarda mediciones nuevas (lo que hará POST /api/v1/measurements). */
export function mockRecordReadings(
    astronautId: number,
    items: {
        indicator: IndicatorCode;
        value: number;
        status: Status;
        measured_at: string;
    }[],
): void {
    const recorded = (RECORDED[astronautId] ??= {});

    for (const item of items) {
        recorded[item.indicator] = {
            value: item.value,
            status: item.status,
            at: new Date(item.measured_at).getTime(),
        };
    }
}

export type MockZone = {
    status: MaybeStatus;
    values: {
        indicator: IndicatorCode;
        value: number;
        unit: string;
        status: Status;
        measured_at: string;
    }[];
};

/** El estado de cada zona: el peor de sus indicadores (lo que hará el backend). */
export function mockBodyStatusFor(
    astronautId: number,
): Record<BodyZoneCode, MockZone> {
    const empty = (): MockZone => ({ status: null, values: [] });
    const result: Record<BodyZoneCode, MockZone> = {
        heart: empty(),
        brain: empty(),
        eyes: empty(),
        lungs: empty(),
        bones: empty(),
        ear: empty(),
        immune: empty(),
        metabolism: empty(),
        whole_body: empty(),
    };

    for (const [indicator, value, unit, status, minutes] of readingsOf(
        astronautId,
    )) {
        const zone = result[ZONE_OF[indicator]];
        zone.values.push({
            indicator,
            value,
            unit,
            status,
            measured_at: new Date(Date.now() - minutes * 60_000).toISOString(),
        });
        zone.status =
            zone.status === null || WORST[status] > WORST[zone.status]
                ? status
                : zone.status;
    }

    return result;
}

const SCORE: Record<Status, number> = { green: 100, yellow: 70, red: 30 };

/** El puntaje: el del perfil o, si solo hay lo registrado, un promedio simple. Sin datos, null. */
export function mockAssessmentFor(astronautId: number) {
    const readings = readingsOf(astronautId);

    if (readings.length === 0) {
        return null;
    }

    const zones = mockBodyStatusFor(astronautId);
    const worst = readings.reduce<Status>(
        (acc, reading) => (WORST[reading[3]] > WORST[acc] ? reading[3] : acc),
        'green',
    );
    const profile = profileOf(astronautId) ?? {
        score: Math.round(
            readings.reduce((sum, reading) => sum + SCORE[reading[3]], 0) /
                readings.length,
        ),
        status: worst,
        summary:
            worst === 'green'
                ? 'Primeros registros en orden.'
                : 'Hay valores que vigilar en sus primeros registros.',
    };

    return {
        astronaut_id: astronautId,
        score: profile.score,
        status: profile.status,
        body_status: Object.fromEntries(
            ZONES.map((zone) => [zone, zones[zone].status]),
        ) as Record<BodyZoneCode, MaybeStatus>,
        summary: profile.summary,
        evaluated_at: new Date(Date.now() - 6 * 60_000).toISOString(),
    };
}

/** Mediciones de hoy (las de las últimas 24 h) sobre el total de indicadores. */
export function mockMeasuredToday(astronautId: number): {
    done: number;
    total: number;
} {
    const readings = readingsOf(astronautId);

    return {
        done: readings.filter((reading) => reading[4] < 24 * 60).length,
        total: readings.length,
    };
}
