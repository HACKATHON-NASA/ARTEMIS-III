import type { BadgeCode, PointReason } from '../Types/gamification';

/**
 * Las reglas de M7 (ficha §3, config/gamification.php). ⚠️ Con el backend
 * viven en Domain/Reglas/; aquí sirven para los datos de prueba.
 */
export const POINTS: Record<Exclude<PointReason, 'badge'>, number> = {
    daily_complete: 10,
    streak_7: 50,
    sleep_7h: 5,
    exercise: 15,
    reminder: 5,
    psych_test: 10,
};

export const REASON_LABELS: Record<PointReason, string> = {
    daily_complete: 'Registro completo del día',
    streak_7: 'Racha de 7 días',
    sleep_7h: 'Dormiste 7 h o más',
    exercise: 'Ejercicio de 90 min o más',
    reminder: 'Recordatorio cumplido',
    psych_test: 'Test psicológico',
    badge: 'Insignia nueva',
};

export const BADGES: {
    code: BadgeCode;
    name: string;
    icon: string;
    condition: string;
    bonus: number;
    target: number;
}[] = [
    {
        code: 'first_log',
        name: 'Primer registro',
        icon: '🚀',
        condition: 'Registra tu primera medición',
        bonus: 5,
        target: 1,
    },
    {
        code: 'orbit_7',
        name: '7 días en órbita',
        icon: '🛰️',
        condition: 'Racha de 7 días con el registro completo',
        bonus: 20,
        target: 7,
    },
    {
        code: 'steel_heart',
        name: 'Corazón de acero',
        icon: '❤️',
        condition: '7 días seguidos con pulso y presión en verde',
        bonus: 20,
        target: 7,
    },
    {
        code: 'space_sleeper',
        name: 'Dormilón espacial',
        icon: '😴',
        condition: '7 noches seguidas de 7 h o más',
        bonus: 20,
        target: 7,
    },
    {
        code: 'on_time',
        name: 'Puntual',
        icon: '⏰',
        condition: '10 recordatorios cumplidos',
        bonus: 15,
        target: 10,
    },
    {
        code: 'mind_check',
        name: 'Mente al día',
        icon: '🧠',
        condition: 'Haz los 4 tests psicológicos de una semana',
        bonus: 15,
        target: 4,
    },
    {
        code: 'iron_body',
        name: 'Cuerpo de hierro',
        icon: '💪',
        condition: '14 días con 90 min de ejercicio o más',
        bonus: 30,
        target: 14,
    },
    {
        code: 'swiss_clock',
        name: 'Reloj suizo',
        icon: '⌚',
        condition: '100 recordatorios cumplidos',
        bonus: 40,
        target: 100,
    },
    {
        code: 'mission_30',
        name: 'Misión completa',
        icon: '🏁',
        condition: 'Racha de 30 días con el registro completo',
        bonus: 100,
        target: 30,
    },
];

/** Nivel = floor(puntos / 100) + 1; la barra muestra puntos % 100. */
export function levelOf(points: number): { level: number; progress: number } {
    return { level: Math.floor(points / 100) + 1, progress: points % 100 };
}

/** Días seguidos, hasta hoy, con el día completo (si hoy no está, cuenta hasta ayer). */
export function streakOf(completeDays: Set<number>, today: number): number {
    let day = completeDays.has(today) ? today : today - 1;
    let streak = 0;

    while (day >= 1 && completeDays.has(day)) {
        streak++;
        day--;
    }

    return streak;
}
