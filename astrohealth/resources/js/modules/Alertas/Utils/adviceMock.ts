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
import type { Advice, AdviceCategory, AdviceDraft } from '../Types/advice';
import { MARS_DELAY_SECONDS, readSpeed } from './alertsMockDb';

/**
 * ⚠️ DATOS DE PRUEBA de las indicaciones del médico, en localStorage (se
 * comparten entre ventanas, como las alertas: el médico escribe en una y le
 * llega al astronauta en la otra). Se borran con el backend de M5.
 */
const KEY = 'astrohealth.demo.advice.v1';
const MINUTE = 60_000;

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

function at(minutesAgo: number): string {
    return new Date(Date.now() - minutesAgo * MINUTE).toISOString();
}

function seed(): Advice[] {
    const make = (
        id: number,
        astronautId: number,
        category: AdviceCategory,
        title: string,
        body: string,
        minutesAgo: number,
        read: boolean,
        important = false,
        validDays: number | null = 7,
    ): Advice => ({
        id,
        astronaut_id: astronautId,
        author: 'Dra. Sofía Ramírez',
        category,
        title,
        body,
        important,
        valid_days: validDays,
        sent_at: at(minutesAgo),
        arrives_at: at(minutesAgo - MARS_DELAY_SECONDS / 60),
        read_at: read ? at(minutesAgo - 30) : null,
    });

    return [
        make(
            3,
            1,
            'medication',
            'Control de la presión',
            'Mide la presión mañana y tarde durante 3 días\nReduce la sal de las raciones\nSi pasa de 160, avisa enseguida',
            90,
            false,
            true,
            3,
        ),
        make(
            2,
            2,
            'sleep',
            'Higiene del sueño',
            'Apaga pantallas 1 h antes de dormir\nUsa el antifaz y los tapones\nMantén la misma hora de acostarte',
            60 * 26,
            true,
        ),
        make(
            1,
            3,
            'exercise',
            'Ejercicio con oxímetro',
            'Baja la intensidad de la bicicleta un 20 %\nMide el oxígeno antes y después',
            60 * 50,
            true,
            false,
            5,
        ),
    ];
}

export function mockAdvice(): Advice[] {
    try {
        const raw = localStorage.getItem(KEY);

        if (raw) {
            return JSON.parse(raw) as Advice[];
        }
    } catch {
        // Sin localStorage: la semilla.
    }

    return seed();
}

function write(items: Advice[]): void {
    try {
        localStorage.setItem(KEY, JSON.stringify(items));
    } catch {
        // Sin localStorage: el cambio dura hasta recargar.
    }
}

export function mockSendAdvice(
    astronautId: number,
    author: string,
    draft: AdviceDraft,
): Advice {
    const items = mockAdvice();
    const now = Date.now();
    const advice: Advice = {
        ...draft,
        id: Math.max(0, ...items.map((item) => item.id)) + 1,
        astronaut_id: astronautId,
        author,
        sent_at: new Date(now).toISOString(),
        arrives_at: new Date(
            now + (MARS_DELAY_SECONDS / readSpeed()) * 1000,
        ).toISOString(),
        read_at: null,
    };
    write([...items, advice]);

    return advice;
}

export function mockReadAdvice(id: number): void {
    write(
        mockAdvice().map((item) =>
            item.id === id && !item.read_at
                ? { ...item, read_at: new Date().toISOString() }
                : item,
        ),
    );
}
