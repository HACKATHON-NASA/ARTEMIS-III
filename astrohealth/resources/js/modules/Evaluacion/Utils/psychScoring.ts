import type { PsychBand, PsychSubmission, PsychTest } from '../Types/psych';

/**
 * Cómo se puntúa un test. ⚠️ Con el backend, lo calcula M2 al recibir las
 * respuestas; aquí se usa para los datos de prueba y la vista previa.
 */
const LAPSE_MS = 500;

export function maxPoints(test: PsychTest): number {
    return Math.max(0, ...test.scale.map((option) => option.points));
}

/** El puntaje máximo posible (cuestionarios) o el tope de la última banda (PVT). */
export function maxScore(test: PsychTest): number {
    return test.kind === 'psychological'
        ? test.questions.length * maxPoints(test)
        : (test.bands.at(-2)?.max ?? 400) * 1.5;
}

export function bandFor(test: PsychTest, score: number): PsychBand {
    return (
        test.bands.find((band) => score <= band.max) ??
        test.bands[test.bands.length - 1] ?? {
            max: score,
            label: '—',
            status: 'green',
        }
    );
}

export function score(
    test: PsychTest,
    submission: PsychSubmission,
): { score: number; detail: string | null; flagged: boolean } {
    if (submission.kind === 'cognitive') {
        const valid = submission.reactions.filter((ms) => ms > 0);
        const mean = valid.length
            ? Math.round(valid.reduce((sum, ms) => sum + ms, 0) / valid.length)
            : 0;
        const lapses = valid.filter((ms) => ms > LAPSE_MS).length;

        return {
            score: mean,
            detail: `${lapses} ${lapses === 1 ? 'lapso' : 'lapsos'} > ${LAPSE_MS} ms · ${submission.falseStarts} anticipos`,
            flagged: false,
        };
    }

    const top = maxPoints(test);
    let total = 0;
    let flagged = false;

    test.questions.forEach((question, index) => {
        const points = test.scale[submission.answers[index]]?.points ?? 0;
        total += question.reverse ? top - points : points;
        flagged ||= question.critical && points > 0;
    });

    return { score: total, detail: null, flagged };
}
