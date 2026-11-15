<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { NewMessage } from '../Types/alerts';
import { HELP_CATEGORIES } from '../Utils/alertKinds';

/**
 * El formulario para hablar con Tierra. SOS: confirmar (y un texto opcional,
 * para no perder segundos). Ayuda: el motivo y qué pasa. Novedad: el texto.
 */
const props = defineProps<{
    kind: NewMessage['kind'] | null;
    submit: (message: NewMessage) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const category = ref('');
const note = ref('');
const error = ref<string | null>(null);
const saving = ref(false);

const TEXT = {
    sos: {
        title: '¿Enviar SOS a Tierra?',
        description:
            'Es para emergencias. Avisa también al médico de la tripulación, que está a bordo.',
        placeholder: 'Opcional: qué está pasando',
        button: 'Enviar SOS',
    },
    help: {
        title: 'Pedir ayuda al médico',
        description: 'El médico de vuelo recibe tu mensaje y te responde.',
        placeholder: '¿Qué sientes? ¿Desde cuándo?',
        button: 'Pedir ayuda',
    },
    report: {
        title: 'Contar una novedad',
        description: 'Algo que el médico deba saber, aunque no sea urgente.',
        placeholder: 'Ej.: dormí mal por el ruido del módulo',
        button: 'Enviar novedad',
    },
};

const text = computed(() => TEXT[props.kind ?? 'report']);

watch(open, (isOpen) => {
    if (isOpen) {
        category.value = '';
        note.value = '';
        error.value = null;
    }
});

async function send(): Promise<void> {
    if (!props.kind) {
        return;
    }

    if (props.kind === 'help' && category.value === '') {
        error.value = 'Elige el motivo.';

        return;
    }

    if (props.kind !== 'sos' && note.value.trim().length < 3) {
        error.value = 'Escribe qué pasa, aunque sea corto.';

        return;
    }

    saving.value = true;

    try {
        await props.submit({
            kind: props.kind,
            category: category.value || null,
            note: note.value,
        });
        open.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle
                    :class="{
                        'text-red-600 dark:text-red-400': kind === 'sos',
                    }"
                    >{{ text.title }}</DialogTitle
                >
                <DialogDescription>{{ text.description }}</DialogDescription>
            </DialogHeader>

            <form
                id="message-form"
                class="grid gap-4"
                novalidate
                @submit.prevent="send"
            >
                <div v-if="kind === 'help'" class="grid gap-2">
                    <span id="help-category" class="text-sm font-medium"
                        >Motivo</span
                    >
                    <div
                        role="radiogroup"
                        aria-labelledby="help-category"
                        class="flex flex-wrap gap-2"
                    >
                        <button
                            v-for="option in HELP_CATEGORIES"
                            :key="option"
                            type="button"
                            role="radio"
                            :aria-checked="category === option"
                            class="rounded-full border px-3 py-1 text-sm transition"
                            :class="
                                category === option
                                    ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="
                                category = option;
                                error = null;
                            "
                        >
                            {{ option }}
                        </button>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="message-note">{{
                        kind === 'sos' ? 'Mensaje' : 'Qué pasa'
                    }}</Label>
                    <textarea
                        id="message-note"
                        v-model="note"
                        rows="3"
                        maxlength="300"
                        :placeholder="text.placeholder"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
                        @input="error = null"
                    />
                    <InputError :message="error ?? undefined" />
                </div>
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">Cancelar</Button>
                <Button
                    type="submit"
                    form="message-form"
                    :disabled="saving"
                    :class="
                        kind === 'sos'
                            ? 'bg-[var(--status-red)] text-white hover:bg-[var(--status-red)]/90'
                            : 'bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110'
                    "
                >
                    <Spinner v-if="saving" />
                    {{ text.button }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
