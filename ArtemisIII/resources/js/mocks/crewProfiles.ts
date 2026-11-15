import type { IndicatorCode, Status } from '@/types/health';

/**
 * ⚠️ DATOS DE PRUEBA: los perfiles de salud de la tripulación de Ares-1
 * (los 4 astronautas del MissionSeeder). Los usa @/mocks/crewHealth.
 */

export type Reading = [IndicatorCode, number, string, Status, number];

export type Profile = {
    score: number;
    status: Status;
    summary: string;
    readings: Reading[];
};

/** [indicador, valor, unidad, estado, minutos desde la medición] */
export const PROFILES: Record<number, Profile> = {
    1: {
        score: 74,
        status: 'yellow',
        summary: 'Corazón en estado crítico. Revisa la recomendación.',
        readings: [
            ['hr', 88, 'bpm', 'green', 6],
            ['bp_sys', 148, 'mmHg', 'red', 6],
            ['bp_dia', 94, 'mmHg', 'red', 6],
            ['sleep', 6.2, 'h', 'yellow', 420],
            ['mood', 4, '1-5', 'green', 90],
            ['spo2', 97, '%', 'green', 6],
            ['temp', 36.7, '°C', 'green', 6],
            ['glucose', 92, 'mg/dL', 'green', 180],
            ['exercise_min', 70, 'min', 'yellow', 1440],
            ['radiation', 0.52, 'mSv', 'green', 60],
        ],
    },
    2: {
        // Mismo cálculo que ScoreRule (M2): sueño 5,8 h es rojo (< 6 h) → 90.
        score: 90,
        status: 'green',
        summary: 'Buen estado general, pero el sueño está en crítico.',
        readings: [
            ['hr', 64, 'bpm', 'green', 30],
            ['bp_sys', 112, 'mmHg', 'green', 30],
            ['bp_dia', 72, 'mmHg', 'green', 30],
            ['sleep', 5.8, 'h', 'red', 400],
            ['mood', 3, '1-5', 'yellow', 120],
            ['spo2', 98, '%', 'green', 30],
            ['temp', 36.5, '°C', 'green', 30],
            ['glucose', 88, 'mg/dL', 'green', 200],
            ['exercise_min', 110, 'min', 'green', 600],
            ['radiation', 0.48, 'mSv', 'green', 60],
        ],
    },
    3: {
        // ScoreRule: oxígeno, glucosa y radiación en amarillo → 87 (normal).
        score: 87,
        status: 'green',
        summary: 'Oxígeno en sangre, glucosa y radiación en precaución.',
        readings: [
            ['hr', 76, 'bpm', 'green', 15],
            ['bp_sys', 118, 'mmHg', 'green', 15],
            ['bp_dia', 76, 'mmHg', 'green', 15],
            ['sleep', 7.4, 'h', 'green', 380],
            ['mood', 4, '1-5', 'green', 60],
            ['spo2', 93, '%', 'yellow', 15],
            ['temp', 36.9, '°C', 'green', 15],
            ['glucose', 104, 'mg/dL', 'yellow', 240],
            ['exercise_min', 95, 'min', 'green', 900],
            ['radiation', 0.94, 'mSv', 'yellow', 45],
        ],
    },
    4: {
        score: 100,
        status: 'green',
        summary: 'Todo en orden. Sigue con tu rutina.',
        readings: [
            ['hr', 62, 'bpm', 'green', 10],
            ['bp_sys', 108, 'mmHg', 'green', 10],
            ['bp_dia', 70, 'mmHg', 'green', 10],
            ['sleep', 7.8, 'h', 'green', 360],
            ['mood', 5, '1-5', 'green', 40],
            ['spo2', 99, '%', 'green', 10],
            ['temp', 36.6, '°C', 'green', 10],
            ['glucose', 86, 'mg/dL', 'green', 150],
            ['exercise_min', 120, 'min', 'green', 500],
            ['radiation', 0.5, 'mSv', 'green', 60],
        ],
    },
};
