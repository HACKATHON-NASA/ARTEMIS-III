import {
    Apple,
    Brain,
    Dumbbell,
    Moon,
    Pill,
    Sofa,
    Stethoscope,
} from '@lucide/vue';
import type { Component } from 'vue';
import type { AdviceCategory } from '../Types/advice';

/** El nombre y el ícono de cada tema de una indicación (presentación). */
export const ADVICE_CATEGORIES: Record<
    AdviceCategory,
    { label: string; icon: Component }
> = {
    sleep: { label: 'Sueño', icon: Moon },
    exercise: { label: 'Ejercicio', icon: Dumbbell },
    nutrition: { label: 'Alimentación', icon: Apple },
    medication: { label: 'Medicamento', icon: Pill },
    rest: { label: 'Descanso', icon: Sofa },
    mental: { label: 'Salud mental', icon: Brain },
    other: { label: 'Otra', icon: Stethoscope },
};
