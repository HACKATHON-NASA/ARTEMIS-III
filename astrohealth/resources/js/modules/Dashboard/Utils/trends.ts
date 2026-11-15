import type { IndicatorCode } from '@/types/health';
import type { TrendPoint } from '../Types/crewTrends';

/**
 * La tendencia de un indicador (M4): compara el valor de hoy con el
 * promedio de hace una semana. ⚠️ Con el backend la calcula M2
 * (TrendAnalyzer) y llega en GET /api/v1/astronauts/{id}/trends.
 */
export type Direction = 'up' | 'down' | 'stable';

/** En estos, bajar es empeorar; en los demás, subir (salvo el pulso, que va en los dos sentidos). */
const WORSE_WHEN_DOWN: IndicatorCode[] = [
    'sleep',
    'spo2',
    'exercise_min',
    'mood',
];

/** Cuánto tiene que moverse para no ser "estable" (el ruido normal de un día a otro). */
const MIN_CHANGE: Record<IndicatorCode, number> = {
    hr: 6,
    bp_sys: 6,
    bp_dia: 5,
    glucose: 6,
    temp: 0.3,
    spo2: 1.5,
    sleep: 0.5,
    mood: 0.8,
    exercise_min: 15,
    radiation: 0.12,
};

export type Trend = {
    direction: Direction;
    worse: boolean;
    from: number;
    to: number;
};

export function trendOf(
    indicator: IndicatorCode,
    points: TrendPoint[],
): Trend | null {
    if (points.length < 4) {
        return null;
    }

    const week = points.slice(-8, -5);
    const from =
        week.reduce((sum, point) => sum + point.value, 0) / week.length;
    const to = points[points.length - 1].value;
    const delta = to - from;
    const direction: Direction =
        Math.abs(delta) < MIN_CHANGE[indicator]
            ? 'stable'
            : delta > 0
              ? 'up'
              : 'down';
    const worse =
        direction !== 'stable' &&
        (indicator === 'hr'
            ? true
            : WORSE_WHEN_DOWN.includes(indicator)
              ? direction === 'down'
              : direction === 'up');

    return { direction, worse, from, to };
}
