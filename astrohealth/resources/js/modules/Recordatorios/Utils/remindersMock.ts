import type { Reminder } from '../Types/reminders';

/**
 * ⚠️ DATOS DE PRUEBA de M8: la rutina de cada astronauta, guardada en
 * localStorage (sobrevive a recargar). Empieza con los 5 recordatorios
 * por defecto del ReminderSeeder; el de la presión ya está hecho hoy.
 * Se borra con el backend de M8.
 */
const KEY = 'astrohealth.demo.reminders.v1';

type Stored = Omit<Reminder, 'state'>;

function defaults(): Stored[] {
    const doneAt = new Date();
    doneAt.setHours(8, 3, 0, 0);
    // Hecho a las 08:03 solo si ya pasó esa hora (si no, aún no le toca).
    const pressureDone =
        doneAt.getTime() <= Date.now() ? doneAt.toISOString() : null;

    return [
        {
            id: 11,
            type: 'measure_bp',
            title: 'Hora de medir tu presión 🩺',
            time: '08:00',
            last_done_at: pressureDone,
        },
        {
            id: 12,
            type: 'exercise',
            title: 'Ejercicio de resistencia: 2 h 🏋️',
            time: '10:00',
            last_done_at: null,
        },
        {
            id: 13,
            type: 'dosimeter',
            title: 'Lee tu dosímetro de radiación ☢️',
            time: '12:00',
            last_done_at: null,
        },
        {
            id: 14,
            type: 'mood_check',
            title: '¿Cómo te sientes hoy? 🙂',
            time: '18:00',
            last_done_at: null,
        },
        {
            id: 15,
            type: 'log_sleep',
            title: 'Registra cómo dormiste 😴',
            time: '21:00',
            last_done_at: null,
        },
    ];
}

function readAll(): Record<string, Stored[]> {
    try {
        return JSON.parse(localStorage.getItem(KEY) ?? '{}') as Record<
            string,
            Stored[]
        >;
    } catch {
        return {};
    }
}

export function mockReadReminders(astronautId: number): Stored[] {
    return readAll()[astronautId] ?? defaults();
}

export function mockWriteReminders(astronautId: number, items: Stored[]): void {
    try {
        localStorage.setItem(
            KEY,
            JSON.stringify({ ...readAll(), [astronautId]: items }),
        );
    } catch {
        // Sin localStorage: el cambio dura hasta recargar.
    }
}
