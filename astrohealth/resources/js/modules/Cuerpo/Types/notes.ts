/**
 * Notas clínicas (expediente privado del equipo médico), con la forma
 * propuesta para GET/POST /api/v1/astronauts/{id}/clinical-notes y
 * PATCH /clinical-notes/{id}/resolve (permiso clinical_notes).
 * El astronauta y administración NO las ven. No se editan ni se borran:
 * si algo cambia, se escribe otra nota.
 */
export type NoteKind = 'evolution' | 'impression' | 'plan' | 'follow_up';

export type ClinicalNote = {
    id: number;
    astronaut_id: number;
    author: string;
    kind: NoteKind;
    text: string;
    /** Requiere seguimiento hasta que alguien la marque resuelta. */
    follow_up: boolean;
    resolved_at: string | null;
    resolved_by: string | null;
    created_at: string;
};

export type NoteDraft = Pick<ClinicalNote, 'kind' | 'text' | 'follow_up'>;
