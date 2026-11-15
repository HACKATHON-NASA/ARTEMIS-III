import type { Alert } from '../Types/alerts';

/**
 * ⚠️ DATOS DE PRUEBA de M5: la "base de datos" de alertas de la demo.
 *
 * Vive en localStorage para que dos ventanas (el astronauta en una y Tierra
 * en otra) vean las mismas alertas: lo que envía una le llega a la otra en
 * segundos. Se reinicia sola a las 12 horas. Si el navegador no deja usar
 * localStorage, queda solo en memoria. Se borra con el backend de M5.
 */
const KEY = 'astrohealth.demo.alerts.v2';
const SPEED_KEY = 'astrohealth.demo.speed';
const MAX_AGE_MS = 12 * 60 * 60 * 1000;
const MINUTE = 60_000;
/** Retraso de una vía con Marte (Ares-1), en segundos. */
export const MARS_DELAY_SECONDS = 1200;

export type MockDb = {
    seededAt: number;
    alerts: Alert[];
    /** Cuándo el simulador manda la siguiente lectura y cuál toca. */
    sim: { next: number; index: number };
};

let memory: MockDb | null = null;

type SeedInput = Omit<
    Alert,
    | 'sent_at'
    | 'ground_eta_at'
    | 'delay_seconds'
    | 'created_at'
    | 'acknowledged_at'
> & {
    minutesAgo: number;
    ackMinutesAgo?: number;
};

export function buildAlert(input: SeedInput, delaySeconds: number): Alert {
    const created = Date.now() - input.minutesAgo * MINUTE;
    const { minutesAgo: _m, ackMinutesAgo, ...fields } = input;

    return {
        ...fields,
        sent_at: new Date(created).toISOString(),
        ground_eta_at: new Date(created + delaySeconds * 1000).toISOString(),
        delay_seconds: delaySeconds,
        acknowledged_at:
            ackMinutesAgo === undefined
                ? null
                : new Date(Date.now() - ackMinutesAgo * MINUTE).toISOString(),
        created_at: new Date(created).toISOString(),
    };
}

const vital = (
    id: number,
    astronaut_id: number,
    indicator: Alert['indicator'],
    level: 'red' | 'yellow',
    message: string,
    minutesAgo: number,
    ack?: [number, string],
): SeedInput => ({
    id,
    astronaut_id,
    kind: 'vital',
    indicator,
    level,
    message,
    note: null,
    source: 'device',
    reply: ack?.[1] ?? null,
    minutesAgo,
    ackMinutesAgo: ack?.[0],
});

/** El historial de Ares-1, coherente con @/mocks/crewHealth. */
function seed(): MockDb {
    const inputs: SeedInput[] = [
        vital(
            57,
            1,
            'bp_sys',
            'red',
            'Presión sistólica 148 mmHg (crítico)',
            6,
        ),
        {
            id: 56,
            astronaut_id: 2,
            kind: 'report',
            indicator: null,
            level: 'info',
            message: 'Novedad de Kenji',
            note: 'Dolor de cabeza leve desde que me levanté. Ya tomé agua.',
            source: 'astronaut',
            reply: null,
            minutesAgo: 26,
        },
        vital(
            55,
            3,
            'spo2',
            'yellow',
            'Saturación de oxígeno 93 % (precaución)',
            70,
        ),
        {
            id: 53,
            astronaut_id: 3,
            kind: 'help',
            indicator: null,
            level: 'yellow',
            message: 'Amara pide ayuda · Síntoma o malestar',
            note: 'Me falta un poco el aire al hacer ejercicio.',
            source: 'astronaut',
            reply: 'Recibido. Baja la intensidad y mide el oxígeno antes y después.',
            minutesAgo: 60 * 3,
            ackMinutesAgo: 60 * 2,
        },
        vital(52, 1, 'sleep', 'yellow', 'Sueño 6,2 h (precaución)', 60 * 9, [
            60 * 8,
            'Sigue la rutina de sueño de esta noche.',
        ]),
        vital(
            49,
            2,
            'mood',
            'yellow',
            'Estado de ánimo 3 de 5 (precaución)',
            60 * 30,
            [60 * 29, 'Agendamos una llamada con tu familia.'],
        ),
        vital(
            44,
            3,
            'radiation',
            'yellow',
            'Radiación diaria 0,94 mSv (precaución)',
            60 * 50,
            [60 * 49, 'Reduce el tiempo en el módulo menos blindado.'],
        ),
    ];

    return {
        seededAt: Date.now(),
        alerts: inputs.map((input) => buildAlert(input, MARS_DELAY_SECONDS)),
        sim: { next: Date.now() + 45_000, index: 0 },
    };
}

export function readDb(): MockDb {
    try {
        const raw = localStorage.getItem(KEY);
        const parsed = raw ? (JSON.parse(raw) as MockDb) : null;

        if (parsed && Date.now() - parsed.seededAt < MAX_AGE_MS) {
            memory = parsed;

            return parsed;
        }
    } catch {
        // Sin localStorage: se sigue en memoria.
    }

    memory ??= seed();
    writeDb(memory);

    return memory;
}

export function writeDb(db: MockDb): void {
    memory = db;

    try {
        localStorage.setItem(KEY, JSON.stringify(db));
    } catch {
        // Sin localStorage: queda en memoria.
    }
}

/** Velocidad de la demo: 1 = real (20 min con Marte), 60 = 20 segundos. */
export function readSpeed(): number {
    try {
        return Number(localStorage.getItem(SPEED_KEY)) === 60 ? 60 : 1;
    } catch {
        return 1;
    }
}

export function writeSpeed(speed: number): void {
    try {
        localStorage.setItem(SPEED_KEY, String(speed));
    } catch {
        // Sin localStorage: la velocidad no se recuerda.
    }
}
