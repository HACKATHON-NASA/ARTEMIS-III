import type { MissionSummary } from '../Types/admin';

/**
 * ⚠️ DATOS DE PRUEBA que quedan en la vista de administración. Usuarios y
 * dispositivos ya vienen de la API (/api/v1/users y /api/v1/devices).
 *
 * - La misión del encabezado se borra con GET /api/v1/missions/current (F1 · M6).
 * - Las alertas pendientes se borran con GET /api/v1/alerts (F5 · M5).
 */

export const MOCK_MISSION: MissionSummary = {
    id: 1,
    name: 'Ares-1',
    environment: 'mars',
    environment_label: 'Rumbo a Marte',
    day: 24,
    day_count: 30,
};

/** Alertas que esperan confirmación desde Tierra (M5, aún sin API). */
export const MOCK_PENDING_ALERTS = 2;
