/**
 * Tipos de salud que comparten los módulos (vienen así de la API).
 */

/** El semáforo. Lo calcula el backend (M2); el frontend solo lo muestra. */
export type Status = 'green' | 'yellow' | 'red';

/** Una zona o un indicador sin datos todavía. */
export type MaybeStatus = Status | null;

export type IndicatorCode =
    | 'hr'
    | 'bp_sys'
    | 'bp_dia'
    | 'glucose'
    | 'temp'
    | 'spo2'
    | 'sleep'
    | 'mood'
    | 'radiation'
    | 'exercise_min';

export type BodyZoneCode =
    | 'heart'
    | 'brain'
    | 'eyes'
    | 'lungs'
    | 'bones'
    | 'ear'
    | 'immune'
    | 'metabolism'
    | 'whole_body';
