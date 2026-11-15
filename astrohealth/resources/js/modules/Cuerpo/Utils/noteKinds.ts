import type { NoteKind } from '../Types/notes';

/** El nombre y la pista de cada tipo de nota clínica (presentación). */
export const NOTE_KINDS: Record<NoteKind, { label: string; hint: string }> = {
    evolution: { label: 'Evolución', hint: 'Cómo va desde la última vez' },
    impression: { label: 'Impresión', hint: 'Lo que crees que pasa' },
    plan: { label: 'Plan', hint: 'Qué se va a hacer' },
    follow_up: { label: 'Seguimiento', hint: 'Qué revisar más adelante' },
};
