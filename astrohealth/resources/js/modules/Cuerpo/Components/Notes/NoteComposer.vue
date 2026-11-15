<script setup lang="ts">
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { NoteDraft, NoteKind } from '../../Types/notes';
import { NOTE_KINDS } from '../../Utils/notesMock';

/**
 * Escribir una nota clínica: el tipo, el texto y si requiere seguimiento.
 * Se guarda como está (las notas no se editan ni se borran).
 */
const props = defineProps<{ submit: (draft: NoteDraft) => Promise<void> }>();

const kind = ref<NoteKind>('evolution');
const text = ref('');
const followUp = ref(false);
const error = ref<string | null>(null);
const saving = ref(false);

async function save(): Promise<void> {
    if (text.value.trim().length < 5) {
        error.value = 'Escribe la nota (al menos unas palabras).';

        return;
    }

    saving.value = true;

    try {
        await props.submit({
            kind: kind.value,
            text: text.value.trim(),
            follow_up: followUp.value,
        });
        text.value = '';
        followUp.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <form
        class="flex flex-col gap-3 rounded-xl border bg-muted/30 p-3"
        novalidate
        @submit.prevent="save"
    >
        <div
            class="flex flex-wrap gap-1.5"
            role="radiogroup"
            aria-label="Tipo de nota"
        >
            <button
                v-for="(meta, code) in NOTE_KINDS"
                :key="code"
                type="button"
                role="radio"
                :aria-checked="kind === code"
                :title="meta.hint"
                class="rounded-full border px-3 py-1 text-xs font-medium transition"
                :class="
                    kind === code
                        ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                        : 'bg-card text-muted-foreground hover:text-foreground'
                "
                @click="kind = code"
            >
                {{ meta.label }}
            </button>
        </div>
        <textarea
            v-model="text"
            rows="3"
            maxlength="1000"
            aria-label="Texto de la nota"
            :placeholder="NOTE_KINDS[kind].hint"
            class="w-full rounded-md border border-input bg-card px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
            @input="error = null"
        />
        <InputError :message="error ?? undefined" />
        <div class="flex flex-wrap items-center justify-between gap-2">
            <label class="flex items-center gap-2 text-sm">
                <input
                    v-model="followUp"
                    type="checkbox"
                    class="accent-amber-500"
                />
                Requiere seguimiento
            </label>
            <Button
                type="submit"
                size="sm"
                :disabled="saving"
                class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
            >
                <Spinner v-if="saving" />
                Guardar nota
            </Button>
        </div>
    </form>
</template>
