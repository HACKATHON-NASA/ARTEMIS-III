import { t, tOr } from '@/lib/i18n';
import type { Badge, PointReason } from '../Types/gamification';

/**
 * Textos de M7 para la pantalla. Cuántos puntos da cada acción y cada
 * cuánto se sube de nivel los manda el backend en el perfil
 * (config/gamification.php): aquí no hay cifras.
 */

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
