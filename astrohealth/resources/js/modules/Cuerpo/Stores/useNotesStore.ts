import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api } from '@/lib/api';
import type { ClinicalNote, NoteDraft } from '../Types/notes';

/**
 * Las notas clínicas de un tripulante (expediente privado del equipo
 * médico de su misión: clinical_notes.manage).
 *
 * GET/POST /api/v1/astronauts/{id}/clinical-notes y
 * PATCH /api/v1/clinical-notes/{id}/resolve. El autor lo pone el backend
 * con la sesión; las notas no se editan ni se borran.
 */
export const useNotesStore = defineStore('notes', () => {
    const notes = ref<ClinicalNote[]>([]);

    /** Seguimientos sin resolver. */
    const pending = computed(() =>
        notes.value.filter((note) => note.follow_up && !note.resolved_at),
    );

    async function load(astronautId: number): Promise<void> {
        notes.value = await api<ClinicalNote[]>(
            `/astronauts/${astronautId}/clinical-notes`,
        );
    }

    /** `author` ya no se envía: lo decide el backend con la sesión. */
    async function add(
        astronautId: number,
        _author: string,
        draft: NoteDraft,
    ): Promise<void> {
        const note = await api<ClinicalNote>(
            `/astronauts/${astronautId}/clinical-notes`,
            { method: 'POST', body: draft },
        );
        notes.value = [note, ...notes.value];
    }

    async function resolve(id: number, _by: string): Promise<void> {
        const note = await api<ClinicalNote>(`/clinical-notes/${id}/resolve`, {
            method: 'PATCH',
        });
        notes.value = notes.value.map((item) =>
            item.id === note.id ? note : item,
        );
    }

    return { notes, pending, load, add, resolve };
});
