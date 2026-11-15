import { defineStore } from 'pinia';
import { ref } from 'vue';
import type { PsychResult, PsychSubmission, PsychTest } from '../Types/psych';
import {
    mockAddResult,
    mockPsychResults,
    mockPsychTests,
    mockSaveTest,
} from '../Utils/psychMockData';
import { bandFor, score } from '../Utils/psychScoring';

/**
 * La guía psicológica (M2): el catálogo de tests, las respuestas de la
 * tripulación y sus resultados. Las respuestas de cada uno son datos de
 * salud: solo las ven quienes tienen health.read.any.
 *
 * ⚠️ HOY con datos de prueba (Utils/psychMockData.ts). Con el backend:
 * GET/POST/PUT /api/v1/psych-tests y GET/POST /api/v1/psych-results (el
 * puntaje lo calcula el backend y avisa a Tierra si hay una respuesta de riesgo).
 */
const wait = (ms = 250) => new Promise((resolve) => setTimeout(resolve, ms));

export const usePsychStore = defineStore('psych', () => {
    const tests = ref<PsychTest[]>([]);
    const results = ref<PsychResult[]>([]);

    async function load(): Promise<void> {
        await wait();
        tests.value = mockPsychTests();
        results.value = mockPsychResults();
    }

    function find(id: number): PsychTest | null {
        return tests.value.find((test) => test.id === id) ?? null;
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

    async function saveTest(test: PsychTest): Promise<PsychTest> {
        await wait(400);
        const saved = mockSaveTest(test);
        tests.value = mockPsychTests();

        return saved;
    }

    async function submit(
        astronautId: number,
        testId: number,
        submission: PsychSubmission,
        today: number,
    ): Promise<PsychResult | null> {
        const test = find(testId);

        if (!test) {
            return null;
        }

        await wait(500);
        const scored = score(test, submission);
        const band = bandFor(test, scored.score);
        const saved = mockAddResult({
            test_id: testId,
            astronaut_id: astronautId,
            day: today,
            taken_at: new Date().toISOString(),
            score: scored.score,
            status: band.status,
            band: band.label,
            detail: scored.detail,
            flagged: scored.flagged,
        });
        results.value = mockPsychResults();

        return saved;
    }

    return {
        tests,
        results,
        load,
        find,
        history,
        latest,
        isDue,
        saveTest,
        submit,
    };
});
