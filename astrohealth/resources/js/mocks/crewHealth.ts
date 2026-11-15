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

type Reading = [IndicatorCode, number, string, Status, number];

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

type Profile = {
    score: number;
    status: Status;
    summary: string;
    readings: Reading[];
};

/** [indicador, valor, unidad, estado, minutos desde la medición] */
const PROFILES: Record<number, Profile> = {
    1: {
        score: 74,
        status: 'yellow',
        summary: 'Corazón en estado crítico. Revisa la recomendación.',
        readings: [
            ['hr', 88, 'bpm', 'green', 6],
            ['bp_sys', 148, 'mmHg', 'red', 6],
            ['bp_dia', 94, 'mmHg', 'red', 6],
            ['sleep', 6.2, 'h', 'yellow', 420],
            ['mood', 4, '1-5', 'green', 90],
            ['spo2', 97, '%', 'green', 6],
            ['temp', 36.7, '°C', 'green', 6],
            ['glucose', 92, 'mg/dL', 'green', 180],
            ['exercise_min', 70, 'min', 'yellow', 1440],
            ['radiation', 0.52, 'mSv', 'green', 60],
        ],
    },
    2: {
        score: 89,
        status: 'green',
        summary: 'Buen estado general. Vigila las horas de sueño.',
        readings: [
            ['hr', 64, 'bpm', 'green', 30],
            ['bp_sys', 112, 'mmHg', 'green', 30],
            ['bp_dia', 72, 'mmHg', 'green', 30],
            ['sleep', 5.8, 'h', 'yellow', 400],
            ['mood', 3, '1-5', 'yellow', 120],
            ['spo2', 98, '%', 'green', 30],
            ['temp', 36.5, '°C', 'green', 30],
            ['glucose', 88, 'mg/dL', 'green', 200],
            ['exercise_min', 110, 'min', 'green', 600],
            ['radiation', 0.48, 'mSv', 'green', 60],
        ],
    },
    3: {
        score: 78,
        status: 'yellow',
        summary: 'Oxígeno en sangre y radiación en precaución.',
        readings: [
            ['hr', 76, 'bpm', 'green', 15],
            ['bp_sys', 118, 'mmHg', 'green', 15],
            ['bp_dia', 76, 'mmHg', 'green', 15],
            ['sleep', 7.4, 'h', 'green', 380],
            ['mood', 4, '1-5', 'green', 60],
            ['spo2', 93, '%', 'yellow', 15],
            ['temp', 36.9, '°C', 'green', 15],
            ['glucose', 104, 'mg/dL', 'yellow', 240],
            ['exercise_min', 95, 'min', 'green', 900],
            ['radiation', 0.94, 'mSv', 'yellow', 45],
        ],
    },
    4: {
        score: 97,
        status: 'green',
        summary: 'Todo en orden. Sigue con tu rutina.',
        readings: [
            ['hr', 62, 'bpm', 'green', 10],
            ['bp_sys', 108, 'mmHg', 'green', 10],
            ['bp_dia', 70, 'mmHg', 'green', 10],
            ['sleep', 7.8, 'h', 'green', 360],
            ['mood', 5, '1-5', 'green', 40],
            ['spo2', 99, '%', 'green', 10],
            ['temp', 36.6, '°C', 'green', 10],
            ['glucose', 86, 'mg/dL', 'green', 150],
            ['exercise_min', 120, 'min', 'green', 500],
            ['radiation', 0.5, 'mSv', 'green', 60],
        ],
    },
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

function profileOf(astronautId: number): Profile {
    return PROFILES[astronautId] ?? PROFILES[1];
}

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

    return profileOf(astronautId).readings.map((reading) => {
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

export function mockAssessmentFor(astronautId: number) {
    const profile = profileOf(astronautId);
    const zones = mockBodyStatusFor(astronautId);

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
