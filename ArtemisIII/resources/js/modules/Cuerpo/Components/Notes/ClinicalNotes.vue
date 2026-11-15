<script setup lang="ts">
import { Lock } from '@lucide/vue';
import { computed, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { useNotesStore } from '../../Stores/useNotesStore';
import type { NoteDraft } from '../../Types/notes';
import NoteComposer from './NoteComposer.vue';
import NoteItem from './NoteItem.vue';

/**
 * Notas clínicas del tripulante (expediente privado del equipo médico, en
 * su ficha): escribir una, verlas todas o solo los seguimientos pendientes,
 * y marcar un seguimiento como resuelto. La pantalla solo la muestra a
 * quien tiene clinical_notes.
 */
const props = defineProps<{ astronautId: number; author: string }>();

const store = useNotesStore();
const onlyPending = ref(false);
const busyId = ref<number | null>(null);

const shown = computed(() => (onlyPending.value ? store.pending : store.notes));

async function add(draft: NoteDraft): Promise<void> {
    await store.add(props.astronautId, props.author, draft);
    toast.success('Nota guardada', {
        description: draft.follow_up
            ? 'Quedó con seguimiento pendiente.'
            : undefined,
    });
}

async function resolve(id: number): Promise<void> {
    busyId.value = id;
    await store.resolve(id, props.author);
    busyId.value = null;
}

onMounted(() => void store.load(props.astronautId));
</script>

<template>
    <section class="flex flex-col gap-4 rounded-2xl border bg-card p-4 sm:p-6">
        <header class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="flex items-center gap-2 text-lg font-semibold">
                    <Lock class="size-4 text-sky-500" /> Notas clínicas
                </h2>
                <p class="text-xs text-muted-foreground">
                    Privadas: solo las ve el equipo médico. No se editan ni se
                    borran.
                </p>
            </div>
            <div
                class="inline-flex rounded-lg border bg-muted/40 p-0.5"
                role="group"
                aria-label="Qué notas ver"
            >
                <button
                    v-for="option in [
                        {
                            value: false,
                            label: `Todas (${store.notes.length})`,
                        },
                        {
                            value: true,
                            label: `Seguimiento (${store.pending.length})`,
                        },
                    ]"
                    :key="option.label"
                    type="button"
                    class="rounded-md px-3 py-1 text-xs font-medium transition"
                    :class="
                        onlyPending === option.value
                            ? 'bg-card text-foreground shadow-sm'
                            : 'text-muted-foreground hover:text-foreground'
                    "
                    :aria-pressed="onlyPending === option.value"
                    @click="onlyPending = option.value"
                >
                    {{ option.label }}
                </button>
            </div>
        </header>

        <NoteComposer :submit="add" />

        <TransitionGroup
            v-if="shown.length"
            tag="ul"
            class="flex flex-col gap-3"
            enter-active-class="transition duration-300"
            enter-from-class="-translate-y-1 opacity-0"
        >
            <NoteItem
                v-for="note in shown"
                :key="note.id"
                :note="note"
                :busy="busyId === note.id"
                :can-resolve="true"
                @resolve="resolve(note.id)"
            />
        </TransitionGroup>
        <p
            v-else
            class="rounded-xl border border-dashed p-6 text-center text-sm text-muted-foreground"
        >
            {{
                onlyPending
                    ? 'No hay seguimientos pendientes.'
                    : 'Aún no hay notas.'
            }}
        </p>
    </section>
</template>
