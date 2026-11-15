import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { ClinicalNote, NoteDraft } from '../Types/notes';
import { mockAddNote, mockNotes, mockResolveNote } from '../Utils/notesMock';

/**
 * Las notas clínicas de un tripulante (expediente privado del equipo
 * médico). Solo para quien tiene el permiso clinical_notes.
 *
 * ⚠️ HOY con datos de prueba (Utils/notesMock.ts). Con el backend:
 * GET/POST /api/v1/astronauts/{id}/clinical-notes y
 * PATCH /api/v1/clinical-notes/{id}/resolve.
 */
const wait = (ms = 250) => new Promise((resolve) => setTimeout(resolve, ms));

export const useNotesStore = defineStore('notes', () => {
    const notes = ref<ClinicalNote[]>([]);

    /** Seguimientos sin resolver. */
    const pending = computed(() =>
        notes.value.filter((note) => note.follow_up && !note.resolved_at),
    );

    async function load(astronautId: number): Promise<void> {
        await wait();
        notes.value = mockNotes()
            .filter((note) => note.astronaut_id === astronautId)
            .sort((a, b) => b.created_at.localeCompare(a.created_at));
    }

    async function add(
        astronautId: number,
        author: string,
        draft: NoteDraft,
    ): Promise<void> {
        await wait(350);
        notes.value = [mockAddNote(astronautId, author, draft), ...notes.value];
    }

    async function resolve(id: number, by: string): Promise<void> {
        await wait(250);
        mockResolveNote(id, by);
        const note = notes.value.find((item) => item.id === id);

        if (note) {
            note.resolved_at = new Date().toISOString();
            note.resolved_by = by;
        }
    }

    return { notes, pending, load, add, resolve };
});
