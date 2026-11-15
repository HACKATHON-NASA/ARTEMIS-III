import type { IndicatorCode } from '@/types/health';

/**
 * Nombres de los indicadores para mostrar.
 * ⚠️ Cuando exista GET /api/v1/indicators (M1), vienen de ahí.
 */
export const INDICATOR_NAMES: Record<IndicatorCode, string> = {
    hr: 'Frecuencia cardíaca',
    bp_sys: 'Presión sistólica',
    bp_dia: 'Presión diastólica',
    glucose: 'Glucosa',
    temp: 'Temperatura',
    spo2: 'Saturación de oxígeno',
    sleep: 'Sueño',
    mood: 'Estado de ánimo',
    radiation: 'Radiación diaria',
    exercise_min: 'Ejercicio',
};
