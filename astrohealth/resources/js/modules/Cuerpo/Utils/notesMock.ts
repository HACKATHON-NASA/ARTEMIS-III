import type { ClinicalNote, NoteDraft, NoteKind } from '../Types/notes';

/**
 * ⚠️ DATOS DE PRUEBA de las notas clínicas, en localStorage. Coherentes con
 * la historia de Ares-1 (la presión de Elena, el sueño y el ánimo de Kenji).
 * Se borran con el backend.
 */
const KEY = 'astrohealth.demo.notes.v1';
const HOUR = 3_600_000;

export const NOTE_KINDS: Record<NoteKind, { label: string; hint: string }> = {
    evolution: { label: 'Evolución', hint: 'Cómo va desde la última vez' },
    impression: { label: 'Impresión', hint: 'Lo que crees que pasa' },
    plan: { label: 'Plan', hint: 'Qué se va a hacer' },
    follow_up: { label: 'Seguimiento', hint: 'Qué revisar más adelante' },
};

function seed(): ClinicalNote[] {
    const note = (
        id: number,
        astronautId: number,
        kind: NoteKind,
        text: string,
        hoursAgo: number,
        followUp = false,
    ): ClinicalNote => ({
        id,
        astronaut_id: astronautId,
        author: 'Dra. Sofía Ramírez',
        kind,
        text,
        follow_up: followUp,
        resolved_at: null,
        resolved_by: null,
        created_at: new Date(Date.now() - hoursAgo * HOUR).toISOString(),
    });

    return [
        note(
            1,
            1,
            'impression',
            'Presión en ascenso desde el día 18, coincide con menos ejercicio y peor sueño. Probable efecto de la redistribución de líquidos + estrés del turno de mando.',
            30,
        ),
        note(
            2,
            1,
            'plan',
            'Control de presión mañana y tarde por 3 días. Bajar sal. Si supera 160/100, valorar medicación con el médico de la tripulación.',
            2,
            true,
        ),
        note(
            3,
            2,
            'evolution',
            'Sueño bajando de forma sostenida (7,3 → 5,8 h) y PHQ-9 de 3 a 11. Refiere ruido en el módulo de descanso.',
            20,
        ),
        note(
            4,
            2,
            'follow_up',
            'Revisar el PHQ-9 de la próxima semana. Si pasa de 14, llamada con psicología de Tierra.',
            19,
            true,
        ),
    ];
}

export function mockNotes(): ClinicalNote[] {
    try {
        const raw = localStorage.getItem(KEY);

        if (raw) {
            return JSON.parse(raw) as ClinicalNote[];
        }
    } catch {
        // Sin localStorage: la semilla.
    }

    return seed();
}

function write(notes: ClinicalNote[]): void {
    try {
        localStorage.setItem(KEY, JSON.stringify(notes));
    } catch {
        // Sin localStorage: el cambio dura hasta recargar.
    }
}

export function mockAddNote(
    astronautId: number,
    author: string,
    draft: NoteDraft,
): ClinicalNote {
    const notes = mockNotes();
    const note: ClinicalNote = {
        ...draft,
        id: Math.max(0, ...notes.map((item) => item.id)) + 1,
        astronaut_id: astronautId,
        author,
        resolved_at: null,
        resolved_by: null,
        created_at: new Date().toISOString(),
    };
    write([...notes, note]);

    return note;
}

export function mockResolveNote(id: number, by: string): void {
    write(
        mockNotes().map((note) =>
            note.id === id && !note.resolved_at
                ? {
                      ...note,
                      resolved_at: new Date().toISOString(),
                      resolved_by: by,
                  }
                : note,
        ),
    );
}
