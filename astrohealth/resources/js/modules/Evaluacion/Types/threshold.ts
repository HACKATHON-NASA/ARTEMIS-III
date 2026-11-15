import type { IndicatorCode } from '@/types/health';

/**
 * Los umbrales de un indicador en un entorno, como los devolverá
 * GET /api/v1/thresholds?environment= (M2) y los guardará
 * PUT /api/v1/thresholds/{indicator}?environment= (permiso guide.manage).
 * Dentro del verde → verde; dentro del amarillo → amarillo; fuera → rojo.
 */
export type Threshold = {
    indicator: IndicatorCode;
    green_min: number;
    green_max: number;
    yellow_min: number;
    yellow_max: number;
};

export type GuideEnvironment = 'iss' | 'transit' | 'moon' | 'mars';

/** "Qué hacer" en la guía: un título y sus pasos (sale de las recomendaciones de M5). */
export type GuideAdvice = { title: string; steps: string[] } | null;

/** Lo que guarda el editor de la guía. */
export type GuideEdit = {
    threshold: Threshold;
    yellow: GuideAdvice;
    red: GuideAdvice;
};
