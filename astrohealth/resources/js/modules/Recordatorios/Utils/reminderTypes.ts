import {
    Activity,
    Dumbbell,
    HeartPulse,
    Moon,
    Radiation,
    Smile,
} from '@lucide/vue';
import type { Component } from 'vue';
import { t, tIn } from '@/lib/i18n';
import type { Reminder, ReminderType } from '../Types/reminders';

/**
 * Los tipos de recordatorio, con su ícono y el título que se sugiere (en el
 * idioma de ahora).
 */
export type ReminderTypeInfo = {
    type: ReminderType;
    icon: Component;
    readonly label: string;
    readonly suggestion: string;
};

function info(type: ReminderType, icon: Component): ReminderTypeInfo {
    return {
        type,
        icon,
        get label() {
            return t(`Recordatorios.types.${type}.label`);
        },
        get suggestion() {
            return type === 'custom'
                ? ''
                : t(`Recordatorios.types.${type}.suggestion`);
        },
    };
}

export const REMINDER_TYPES: ReminderTypeInfo[] = [
    info('measure_bp', HeartPulse),
    info('exercise', Dumbbell),
    info('dosimeter', Radiation),
    info('mood_check', Smile),
    info('log_sleep', Moon),
    info('custom', Activity),
];

export function reminderIcon(type: ReminderType): Component {
    return REMINDER_TYPES.find((item) => item.type === type)?.icon ?? Activity;
}

/** El título sugerido de un tipo en cualquiera de los dos idiomas. */
function suggestionsOf(type: ReminderType): string[] {
    const key = `Recordatorios.types.${type}.suggestion`;

    return [tIn('en', key), tIn('es', key)];
}

/**
 * Lo que se muestra de un recordatorio: si su título es el sugerido (como
 * los de la rutina por defecto), sale en el idioma de ahora; si la persona
 * lo escribió, sale tal cual.
 */
export function reminderTitle(
    reminder: Pick<Reminder, 'type' | 'title'>,
): string {
    return reminder.type !== 'custom' &&
        suggestionsOf(reminder.type).includes(reminder.title)
        ? t(`Recordatorios.types.${reminder.type}.suggestion`)
        : reminder.title;
}

/** ¿Es uno de los títulos sugeridos (en cualquier idioma)? */
export function isSuggestion(title: string): boolean {
    return REMINDER_TYPES.some(
        (item) =>
            item.type !== 'custom' && suggestionsOf(item.type).includes(title),
    );
}
