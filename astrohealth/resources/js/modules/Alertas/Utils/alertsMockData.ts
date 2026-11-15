import { mockBodyStatusFor } from '@/mocks/crewHealth';
import type { IndicatorCode } from '@/types/health';
import type { Alert, NewMessage } from '../Types/alerts';
import {
    MARS_DELAY_SECONDS,
    buildAlert,
    readDb,
    readSpeed,
    writeDb,
} from './alertsMockDb';

/**
 * ⚠️ DATOS DE PRUEBA de M5: imita lo que hará el backend.
 * - Las alertas salen hacia Tierra solas al crearse.
 * - El "simulador" manda de vez en cuando una lectura de los sensores que
 *   sale en amarillo o rojo (con los valores reales de @/mocks/crewHealth).
 * Se borra cuando existan las rutas de M5.
 */

const NAMES: Record<number, string> = {
    1: 'Elena',
    2: 'Kenji',
    3: 'Amara',
    4: 'Lucas',
};

const LABELS: Partial<Record<IndicatorCode, string>> = {
    hr: 'Frecuencia cardíaca',
    bp_sys: 'Presión sistólica',
    bp_dia: 'Presión diastólica',
    glucose: 'Glucosa',
    temp: 'Temperatura',
    spo2: 'Saturación de oxígeno',
    sleep: 'Sueño',
    mood: 'Estado de ánimo',
    radiation: 'Radiación diaria',
    exercise_min: 'Ejercicio',
};

/** Lo que mandan los sensores de Ares-1, en orden (una cada 2 minutos). */
const SIMULATED: [number, IndicatorCode][] = [
    [2, 'sleep'],
    [3, 'glucose'],
    [1, 'bp_dia'],
    [1, 'exercise_min'],
];
const SIM_EVERY_MS = 120_000;

function delay(): number {
    return Math.round(MARS_DELAY_SECONDS / readSpeed());
}

function nextId(alerts: Alert[]): number {
    return Math.max(0, ...alerts.map((alert) => alert.id)) + 1;
}

function vitalMessage(
    indicator: IndicatorCode,
    value: number,
    unit: string,
    red: boolean,
): string {
    const shown =
        unit === '1-5'
            ? `${value} de 5`
            : `${value.toLocaleString('es')} ${unit}`;

    return `${LABELS[indicator]} ${shown} (${red ? 'crítico' : 'precaución'})`;
}

/** Las alertas de un astronauta, o de toda la tripulación con `null`. Más reciente primero. */
export function mockAlerts(astronautId: number | null): Alert[] {
    return readDb()
        .alerts.filter(
            (alert) =>
                astronautId === null || alert.astronaut_id === astronautId,
        )
        .sort((a, b) => b.created_at.localeCompare(a.created_at));
}

export function mockPendingTotal(astronautId: number | null): number {
    return mockAlerts(astronautId).filter(
        (alert) => alert.acknowledged_at === null,
    ).length;
}

/** Un mensaje del astronauta (SOS, ayuda o novedad): sale hacia Tierra ya. */
export function mockSendMessage(
    astronautId: number,
    message: NewMessage,
): Alert {
    const db = readDb();
    const name = NAMES[astronautId] ?? 'Tripulante';
    const title = {
        sos: `SOS de ${name}`,
        help: `${name} pide ayuda${message.category ? ` · ${message.category}` : ''}`,
        report: `Novedad de ${name}`,
    }[message.kind];
    const alert = buildAlert(
        {
            id: nextId(db.alerts),
            astronaut_id: astronautId,
            kind: message.kind,
            indicator: null,
            level:
                message.kind === 'sos' || message.urgent
                    ? 'red'
                    : message.kind === 'help'
                      ? 'yellow'
                      : 'info',
            message: title,
            note: message.note.trim() || null,
            source: 'astronaut',
            reply: null,
            minutesAgo: 0,
        },
        delay(),
    );
    db.alerts.push(alert);
    writeDb(db);

    return alert;
}

/** Lo que hará M5 al escuchar MeasurementsRecorded: amarillos y rojos crean su alerta. */
export function mockAlertsFromMeasurements(
    astronautId: number,
    measurements: {
        indicator: IndicatorCode;
        value: number;
        unit: string;
        status: 'green' | 'yellow' | 'red';
    }[],
): Alert[] {
    const db = readDb();
    const created = measurements
        .filter((item) => item.status !== 'green')
        .map((item) => {
            const alert = buildAlert(
                {
                    id: nextId(db.alerts),
                    astronaut_id: astronautId,
                    kind: 'vital',
                    indicator: item.indicator,
                    level: item.status === 'red' ? 'red' : 'yellow',
                    message: vitalMessage(
                        item.indicator,
                        item.value,
                        item.unit,
                        item.status === 'red',
                    ),
                    note: null,
                    source: 'manual',
                    reply: null,
                    minutesAgo: 0,
                },
                delay(),
            );
            db.alerts.push(alert);

            return alert;
        });
    writeDb(db);

    return created;
}

export function mockAcknowledge(id: number, reply: string): Alert | null {
    const db = readDb();
    const alert = db.alerts.find((item) => item.id === id);

    if (alert) {
        alert.acknowledged_at = new Date().toISOString();
        alert.reply = reply.trim() || null;
        // La respuesta viaja de vuelta con el retraso de ahora (demo incluida).
        alert.delay_seconds = Math.min(alert.delay_seconds, delay());
        writeDb(db);
    }

    return alert ?? null;
}

/** El simulador de sensores: si ya toca, crea la siguiente lectura fuera de rango. */
export function mockSimulatorTick(): void {
    const db = readDb();

    if (Date.now() < db.sim.next || db.sim.index >= SIMULATED.length) {
        return;
    }

    const [astronautId, indicator] = SIMULATED[db.sim.index];
    const reading = Object.values(mockBodyStatusFor(astronautId))
        .flatMap((zone) => zone.values)
        .find((value) => value.indicator === indicator);
    db.sim = { next: Date.now() + SIM_EVERY_MS, index: db.sim.index + 1 };

    if (reading && reading.status !== 'green') {
        db.alerts.push(
            buildAlert(
                {
                    id: nextId(db.alerts),
                    astronaut_id: astronautId,
                    kind: 'vital',
                    indicator,
                    level: reading.status,
                    message: vitalMessage(
                        indicator,
                        reading.value,
                        reading.unit,
                        reading.status === 'red',
                    ),
                    note: null,
                    source: 'device',
                    reply: null,
                    minutesAgo: 0,
                },
                delay(),
            ),
        );
    }

    writeDb(db);
}
