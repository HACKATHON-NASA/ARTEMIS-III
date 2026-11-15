import { t, tIn } from '@/lib/i18n';
import type { PsychResult, PsychTest } from '../Types/psych';

/**
 * Los textos de los tests en el idioma de ahora.
 *
 * Los 4 tests de base (PHQ-9, GAD-7, PSS-4, PVT) se guardan en español. Un
 * texto se traduce solo si sigue IGUAL al de base (Locales → tests): si el
 * equipo médico lo cambió, se muestra tal como lo escribió. Los tests que
 * crea el equipo médico salen como están.
 */
function localized(key: string, value: string): string {
    return value === tIn('es', key) ? t(key) : value;
}

/** Un test con sus textos en el idioma de ahora (solo para mostrar: no se guarda). */
export function localizeTest(test: PsychTest): PsychTest {
    const base = `Evaluacion.tests.${test.code}`;

    return {
        ...test,
        name: localized(`${base}.name`, test.name),
        description: localized(`${base}.description`, test.description),
        instructions: localized(`${base}.instructions`, test.instructions),
        unit: localized(`Evaluacion.units.${test.unit}`, test.unit),
        scale: test.scale.map((option, index) => ({
            ...option,
            label: localized(`${base}.scale.${index}`, option.label),
        })),
        questions: test.questions.map((question) => ({
            ...question,
            text: localized(`${base}.questions.${question.id}`, question.text),
        })),
        bands: test.bands.map((band, index) => ({
            ...band,
            label: localized(`${base}.bands.${index}`, band.label),
        })),
    };
}

/** El nombre de la banda de un resultado (se guardó en español al responder). */
export function resultBand(
    test: PsychTest | null,
    result: PsychResult,
): string {
    if (!test) {
        return result.band;
    }

    const index = test.bands.findIndex(
        (_, i) =>
            tIn('es', `Evaluacion.tests.${test.code}.bands.${i}`) ===
            result.band,
    );

    return index === -1
        ? result.band
        : t(`Evaluacion.tests.${test.code}.bands.${index}`);
}

/** El detalle del PVT ("2 lapsos > 500 ms · 0 anticipos") en el idioma de ahora. */
export function resultDetail(detail: string | null): string | null {
    const match = detail?.match(/^(\d+) lapsos? > (\d+) ms · (\d+) anticipos$/);

    if (!detail || !match) {
        return detail;
    }

    const lapses = Number(match[1]);

    return t(
        'Evaluacion.psych.detail',
        { lapses, ms: Number(match[2]), early: Number(match[3]) },
        lapses,
    );
}
