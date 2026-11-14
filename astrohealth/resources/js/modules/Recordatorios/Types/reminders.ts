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

export type Reminder = {
    id: number;
    type: ReminderType;
    title: string;
    time: string;
    state: ReminderState;
    last_done_at: string | null;
};
