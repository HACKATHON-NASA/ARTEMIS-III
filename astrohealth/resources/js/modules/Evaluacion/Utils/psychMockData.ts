import type { PsychResult, PsychTest } from '../Types/psych';
import { PSYCH_CATALOG } from './psychCatalog';
import { bandFor } from './psychScoring';

/**
 * ⚠️ DATOS DE PRUEBA de la guía psicológica: el catálogo de tests (con lo
 * que edite el equipo médico) y los resultados de Ares-1, guardados en
 * localStorage para que sobrevivan a recargar. Se borran con el backend.
 *
 * Historias (coherentes con el resto de la demo): Kenji duerme poco y su
 * ánimo y su atención empeoran; Elena tiene la presión alta y su ansiedad y
 * estrés suben; Amara y Lucas están estables. Elena aún no hace los de esta
 * semana (le salen pendientes).
 */
const KEY = 'astrohealth.demo.psych.v1';
const DAYS = [3, 10, 17, 24];

/** [PHQ-9, GAD-7, PSS-4, PVT] por semana. */
const HISTORY: Record<number, number[][]> = {
    1: [
        [3, 4, 5],
        [4, 6, 8],
        [5, 7, 9],
        [265, 270, 285],
    ],
    2: [
        [3, 5, 8, 11],
        [3, 4, 5, 6],
        [4, 6, 8, 9],
        [255, 290, 330, 372],
    ],
    3: [
        [2, 3, 3, 4],
        [3, 3, 4, 5],
        [4, 5, 6, 7],
        [248, 252, 262, 270],
    ],
    4: [
        [1, 1, 2, 1],
        [1, 2, 1, 2],
        [3, 3, 4, 3],
        [240, 238, 245, 242],
    ],
};

type Db = { tests: PsychTest[]; results: PsychResult[] };

function seed(): Db {
    const results: PsychResult[] = [];
    const today = 24;

    for (const [astronaut, perTest] of Object.entries(HISTORY)) {
        perTest.forEach((scores, testIndex) => {
            const test = PSYCH_CATALOG[testIndex];

            scores.forEach((value, week) => {
                const band = bandFor(test, value);
                const daysAgo = today - DAYS[week];
                results.push({
                    id: results.length + 1,
                    test_id: test.id,
                    astronaut_id: Number(astronaut),
                    day: DAYS[week],
                    taken_at: new Date(
                        Date.now() - daysAgo * 86_400_000,
                    ).toISOString(),
                    score: value,
                    status: band.status,
                    band: band.label,
                    detail:
                        test.kind === 'cognitive'
                            ? `${value > 330 ? 2 : 0} lapsos > 500 ms`
                            : null,
                    flagged: false,
                });
            });
        });
    }

    return { tests: PSYCH_CATALOG, results };
}

function read(): Db {
    try {
        const raw = localStorage.getItem(KEY);

        if (raw) {
            return JSON.parse(raw) as Db;
        }
    } catch {
        // Sin localStorage: se usa la semilla en memoria.
    }

    return memory;
}

let memory: Db = seed();

function write(db: Db): void {
    memory = db;

    try {
        localStorage.setItem(KEY, JSON.stringify(db));
    } catch {
        // Sin localStorage: queda en memoria.
    }
}

export function mockPsychTests(): PsychTest[] {
    return read().tests;
}

export function mockPsychResults(): PsychResult[] {
    return read().results;
}

/** Crea (id 0) o cambia un test del catálogo. */
export function mockSaveTest(test: PsychTest): PsychTest {
    const db = read();
    const saved = test.id
        ? test
        : { ...test, id: Math.max(0, ...db.tests.map((item) => item.id)) + 1 };
    db.tests = [...db.tests.filter((item) => item.id !== saved.id), saved].sort(
        (a, b) => a.id - b.id,
    );
    write(db);

    return saved;
}

export function mockAddResult(result: Omit<PsychResult, 'id'>): PsychResult {
    const db = read();
    const saved = {
        ...result,
        id: Math.max(0, ...db.results.map((item) => item.id)) + 1,
    };
    db.results.push(saved);
    write(db);

    return saved;
}
