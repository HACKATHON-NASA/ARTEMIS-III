import type { Status } from '@/types/health';

/**
 * Tipos de la guía psicológica (M2), con la forma propuesta para
 * GET/POST/PUT /api/v1/psych-tests y GET/POST /api/v1/psych-results.
 *
 * - `psychological`: un cuestionario (todas las preguntas usan la misma
 *   escala de respuestas). El puntaje es la suma de los puntos.
 * - `cognitive`: una prueba de rendimiento (hoy, tiempo de reacción tipo
 *   PVT). El puntaje es el promedio en milisegundos.
 */
export type PsychKind = 'psychological' | 'cognitive';

export type PsychOption = { label: string; points: number };

export type PsychQuestion = {
    id: string;
    text: string;
    /** Se puntúa al revés (en el PSS, las preguntas "positivas"). */
    reverse: boolean;
    /** Cualquier respuesta distinta de la de 0 puntos avisa a Tierra ya. */
    critical: boolean;
};

/** Hasta qué puntaje (incluido) va cada interpretación, de menor a mayor. */
export type PsychBand = { max: number; label: string; status: Status };

export type PsychTest = {
    id: number;
    code: string;
    name: string;
    kind: PsychKind;
    description: string;
    instructions: string;
    /** Cada cuántos días se repite. */
    frequency_days: number;
    unit: string;
    scale: PsychOption[];
    questions: PsychQuestion[];
    bands: PsychBand[];
    /** De dónde sale el test (o null si lo creó el equipo médico). */
    source: string | null;
};

export type PsychResult = {
    id: number;
    test_id: number;
    astronaut_id: number;
    /** Día de misión en que se respondió. */
    day: number;
    taken_at: string;
    score: number;
    status: Status;
    band: string;
    /** Algo más para mostrar (p. ej. "2 lapsos > 500 ms"). */
    detail: string | null;
    /** Respondió algo en una pregunta crítica: se avisó a Tierra. */
    flagged: boolean;
};

/** Lo que manda el astronauta al terminar un test. */
export type PsychSubmission =
    | { kind: 'psychological'; answers: number[] }
    | { kind: 'cognitive'; reactions: number[]; falseStarts: number };
