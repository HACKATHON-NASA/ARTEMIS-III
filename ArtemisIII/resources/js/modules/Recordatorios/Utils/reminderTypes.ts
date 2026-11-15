import {
    Activity,
    Dumbbell,
    HeartPulse,
    Moon,
    Radiation,
    Smile,
} from '@lucide/vue';
import type { Component } from 'vue';
import type { ReminderType } from '../Types/reminders';

/**
 * Los tipos de recordatorio, con su ícono y el título que se sugiere.
 */
export const REMINDER_TYPES: {
    type: ReminderType;
    label: string;
    icon: Component;
    suggestion: string;
}[] = [
    {
        type: 'measure_bp',
        label: 'Presión',
        icon: HeartPulse,
        suggestion: 'Hora de medir tu presión 🩺',
    },
    {
        type: 'exercise',
        label: 'Ejercicio',
        icon: Dumbbell,
        suggestion: 'Ejercicio de resistencia: 2 h 🏋️',
    },
    {
        type: 'dosimeter',
        label: 'Dosímetro',
        icon: Radiation,
        suggestion: 'Lee tu dosímetro de radiación ☢️',
    },
    {
        type: 'mood_check',
        label: 'Ánimo',
        icon: Smile,
        suggestion: '¿Cómo te sientes hoy? 🙂',
    },
    {
        type: 'log_sleep',
        label: 'Sueño',
        icon: Moon,
        suggestion: 'Registra cómo dormiste 😴',
    },
    { type: 'custom', label: 'Otro', icon: Activity, suggestion: '' },
];

export function reminderIcon(type: ReminderType): Component {
    return REMINDER_TYPES.find((item) => item.type === type)?.icon ?? Activity;
}
