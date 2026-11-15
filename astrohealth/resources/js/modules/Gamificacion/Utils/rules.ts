import { t, tOr } from '@/lib/i18n';
import type { Badge, PointReason } from '../Types/gamification';

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

/** Por qué se ganaron unos puntos, en el idioma de ahora. */
export function reasonLabel(reason: PointReason): string {
    return t(`Gamificacion.reasons.${reason}`);
}

/** El nombre de una insignia (por su código; si no se conoce, el de la API). */
export function badgeName(badge: Pick<Badge, 'code' | 'name'>): string {
    return tOr(`Gamificacion.badges.${badge.code}.name`, badge.name);
}

export function badgeCondition(
    badge: Pick<Badge, 'code' | 'condition'>,
): string {
    return tOr(`Gamificacion.badges.${badge.code}.condition`, badge.condition);
}
