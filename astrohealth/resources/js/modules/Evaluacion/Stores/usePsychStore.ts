import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { api, ApiError } from '@/lib/api';
import { t } from '@/lib/i18n';
import type { PsychResult, PsychSubmission, PsychTest } from '../Types/psych';
import { localizeTest } from '../Utils/psychText';

/**
 * La guía psicológica (M2): el catálogo de tests, las respuestas de la
 * tripulación y sus resultados.
 *
 * GET/POST/PUT /api/v1/psych-tests y GET/POST /api/v1/psych-results. El
 * puntaje, la banda y el color los calcula el backend; también suma los
 * puntos (M7) y, si hay una respuesta de riesgo, avisa a Tierra (M5). Cada
 * quien recibe solo los resultados que puede ver (health.read).
 *
 * `tests` sale con los textos en el idioma de ahora (para mostrar);
 * `original()` da el test tal como está guardado (para editarlo).
 */
function fail(error: unknown, fallback: string): void {
    toast.error(error instanceof ApiError ? error.message : fallback);
}

export const usePsychStore = defineStore('psych', () => {
    const stored = ref<PsychTest[]>([]);
    const tests = computed(() => stored.value.map(localizeTest));
    const results = ref<PsychResult[]>([]);

    async function load(): Promise<void> {
        [stored.value, results.value] = await Promise.all([
            api<PsychTest[]>('/psych-tests'),
            api<PsychResult[]>('/psych-results'),
        ]);
    }

    function find(id: number): PsychTest | null {
        return tests.value.find((test) => test.id === id) ?? null;
    }

    /** El test tal como está guardado (sin traducir), para el editor. */
    function original(id: number): PsychTest | null {
        return stored.value.find((test) => test.id === id) ?? null;
    }

    /** Resultados de un tripulante en un test, del más viejo al más nuevo. */
    function history(astronautId: number, testId: number): PsychResult[] {
        return results.value
            .filter(
                (item) =>
                    item.astronaut_id === astronautId &&
                    item.test_id === testId,
            )
            .sort(
                (a, b) => a.day - b.day || a.taken_at.localeCompare(b.taken_at),
            );
    }

    function latest(astronautId: number, testId: number): PsychResult | null {
        return history(astronautId, testId).at(-1) ?? null;
    }

    /** Le toca si nunca lo hizo o si ya pasó su frecuencia desde la última vez. */
    function isDue(
        astronautId: number,
        test: PsychTest,
        today: number,
    ): boolean {
        const last = latest(astronautId, test.id);

        return last === null || today - last.day >= test.frequency_days;
    }

    /** Crea (id 0) o cambia un test del catálogo. */
    async function saveTest(test: PsychTest): Promise<PsychTest | null> {
        const { id, ...body } = test;

        try {
            const saved = await api<PsychTest>(
                id ? `/psych-tests/${id}` : '/psych-tests',
                { method: id ? 'PUT' : 'POST', body },
            );
            stored.value = [
                ...stored.value.filter((item) => item.id !== saved.id),
                saved,
            ].sort((a, b) => a.id - b.id);

            return saved;
        } catch (error) {
            fail(error, t('Evaluacion.psych.saveFailed'));

            return null;
        }
    }

    /** Manda lo respondido; el backend devuelve el resultado ya puntuado. */
    async function submit(
        testId: number,
        submission: PsychSubmission,
    ): Promise<PsychResult | null> {
        const body =
            submission.kind === 'psychological'
                ? { test_id: testId, answers: submission.answers }
                : {
                      test_id: testId,
                      reactions: submission.reactions,
                      false_starts: submission.falseStarts,
                  };

        try {
            const saved = await api<PsychResult>('/psych-results', {
                method: 'POST',
                body,
            });
            results.value = [...results.value, saved];

            return saved;
        } catch (error) {
            fail(error, t('Evaluacion.psych.sendFailed'));

            return null;
        }
    }

    return {
        tests,
        results,
        load,
        find,
        original,
        history,
        latest,
        isDue,
        saveTest,
        submit,
    };
});
