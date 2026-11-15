/**
 * Indicaciones del médico (M5), con la forma propuesta para
 * GET/POST /api/v1/advice y PATCH /api/v1/advice/{id}/read.
 *
 * El médico de vuelo (o el de la tripulación) le escribe a un tripulante qué
 * hacer. Viaja con el retraso de la comunicación, como las alertas, y el
 * astronauta la marca como "Entendido".
 */
export type AdviceCategory =
    | 'sleep'
    | 'exercise'
    | 'nutrition'
    | 'medication'
    | 'rest'
    | 'mental'
    | 'other';

export type Advice = {
    id: number;
    astronaut_id: number;
    /** Quién la escribió (nombre para mostrar). */
    author: string;
    category: AdviceCategory;
    title: string;
    /** Pasos, uno por línea. */
    body: string;
    important: boolean;
    /** Por cuántos días vale (null = hasta nuevo aviso). */
    valid_days: number | null;
    sent_at: string;
    /** Cuándo llega a la nave (sent_at + el retraso). */
    arrives_at: string;
    read_at: string | null;
};

export type AdviceDraft = Pick<
    Advice,
    'category' | 'title' | 'body' | 'important' | 'valid_days'
>;
