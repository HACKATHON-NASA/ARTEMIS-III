import type { PointReason } from '../Types/gamification';

/**
 * Textos de M7 para la pantalla. Cuántos puntos da cada acción lo decide el
 * backend (config/gamification.php); POINTS solo se muestra en "Cómo ganar
 * puntos" y debe coincidir con esa configuración.
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
