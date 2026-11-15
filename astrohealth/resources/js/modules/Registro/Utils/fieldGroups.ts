import {
    Activity,
    Brain,
    Dumbbell,
    HeartPulse,
    Radiation,
    Thermometer,
    Wind,
} from '@lucide/vue';
import type { Component } from 'vue';
import type { BodyZoneCode, IndicatorCode } from '@/types/health';

/**
 * Cómo se agrupan los campos en la pantalla: por órgano, en el orden en que
 * se suele medir (signos vitales primero, el dosímetro al final del día).
 * Los de un solo campo van en pares para que la cuadrícula no deje huecos.
 */
export type FieldGroup = {
    zone: BodyZoneCode;
    title: string;
    hint: string;
    icon: Component;
    indicators: IndicatorCode[];
};

export const FIELD_GROUPS: FieldGroup[] = [
    {
        zone: 'heart',
        title: 'Corazón',
        hint: 'Pulso y presión, en reposo',
        icon: HeartPulse,
        indicators: ['hr', 'bp_sys', 'bp_dia'],
    },
    {
        zone: 'lungs',
        title: 'Pulmones',
        hint: 'Oxímetro en el dedo',
        icon: Wind,
        indicators: ['spo2'],
    },
    {
        zone: 'immune',
        title: 'Defensas',
        hint: 'Temperatura',
        icon: Thermometer,
        indicators: ['temp'],
    },
    {
        zone: 'metabolism',
        title: 'Metabolismo',
        hint: 'Glucosa en ayunas',
        icon: Activity,
        indicators: ['glucose'],
    },
    {
        zone: 'bones',
        title: 'Huesos y músculos',
        hint: 'Ejercicio de hoy',
        icon: Dumbbell,
        indicators: ['exercise_min'],
    },
    {
        zone: 'brain',
        title: 'Mente y descanso',
        hint: 'Cómo dormiste y cómo te sientes',
        icon: Brain,
        indicators: ['sleep', 'mood'],
    },
    {
        zone: 'whole_body',
        title: 'Radiación',
        hint: 'Dosímetro, al final del día',
        icon: Radiation,
        indicators: ['radiation'],
    },
];

/**
 * A qué altura del cuerpo "aterriza" el valor que vuela (fracción del alto
 * del visor, de arriba hacia abajo). Es una aproximación para la animación.
 */
export const ZONE_HEIGHT: Record<BodyZoneCode, number> = {
    brain: 0.14,
    eyes: 0.16,
    ear: 0.17,
    heart: 0.34,
    lungs: 0.32,
    immune: 0.4,
    metabolism: 0.48,
    whole_body: 0.5,
    bones: 0.68,
};
