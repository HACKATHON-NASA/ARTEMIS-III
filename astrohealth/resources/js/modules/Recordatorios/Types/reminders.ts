/**
 * Un recordatorio del día, como GET /api/v1/reminders (M8).
 */
export type ReminderType =
    | 'measure_bp'
    | 'exercise'
    | 'log_sleep'
    | 'mood_check'
    | 'dosimeter'
    | 'custom';

/** done = hecho hoy · due = ya es la hora · upcoming = más tarde. */
export type ReminderState = 'done' | 'due' | 'upcoming';

/** Como lo devuelve la API. done_today lo decide el backend (día UTC). */
export type StoredReminder = {
    id: number;
    type: ReminderType;
    title: string;
    /** "HH:MM" */
    time: string;
    active: boolean;
    last_done_at: string | null;
    done_today: boolean;
};

export type Reminder = StoredReminder & { state: ReminderState };

/** Lo que se edita de un recordatorio (crear o cambiar). */
export type ReminderDraft = Pick<Reminder, 'type' | 'title' | 'time'>;
