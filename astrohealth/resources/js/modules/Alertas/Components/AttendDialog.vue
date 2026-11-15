<script setup lang="ts">
import { ref, watch } from 'vue';
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
import type { Alert } from '../Types/alerts';
import { QUICK_REPLIES } from '../Utils/alertKinds';

/**
 * Tierra atiende una alerta y responde. Hay respuestas rápidas; la respuesta
 * tarda en llegar a la nave lo mismo que tardó la alerta en llegar aquí.
 */
const props = defineProps<{
    alert: Alert | null;
    submit: (reply: string) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });
const reply = ref('');
const error = ref<string | null>(null);
const saving = ref(false);

watch(open, (isOpen) => {
    if (isOpen) {
        reply.value = props.alert?.kind === 'sos' ? QUICK_REPLIES[3] : '';
        error.value = null;
    }
});

async function send(): Promise<void> {
    if (reply.value.trim().length < 3) {
        error.value = 'Escribe una respuesta o elige una rápida.';

        return;
    }

    saving.value = true;

    try {
        await props.submit(reply.value);
        open.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent v-if="alert" class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>Atender: {{ alert.message }}</DialogTitle>
                <DialogDescription>
                    Tu respuesta tarda en llegar a la nave lo mismo que tardó la
                    alerta en llegar aquí.
                </DialogDescription>
            </DialogHeader>

            <blockquote
                v-if="alert.note"
                class="rounded-lg border-l-2 border-sky-500/60 bg-muted/40 px-3 py-2 text-sm"
            >
                “{{ alert.note }}”
            </blockquote>

            <form
                id="attend-form"
                class="grid gap-3"
                novalidate
                @submit.prevent="send"
            >
                <div class="flex flex-col gap-1.5">
                    <button
                        v-for="option in QUICK_REPLIES"
                        :key="option"
                        type="button"
                        class="rounded-lg border px-3 py-2 text-left text-sm transition hover:border-sky-500/50"
                        :class="
                            reply === option
                                ? 'border-sky-500 bg-sky-500/10'
                                : 'text-muted-foreground'
                        "
                        @click="
                            reply = option;
                            error = null;
                        "
                    >
                        {{ option }}
                    </button>
                </div>
                <div class="grid gap-2">
                    <Label for="attend-reply">Respuesta</Label>
                    <textarea
                        id="attend-reply"
                        v-model="reply"
                        rows="3"
                        maxlength="300"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
                        @input="error = null"
                    />
                    <InputError :message="error ?? undefined" />
                </div>
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">Cancelar</Button>
                <Button
                    type="submit"
                    form="attend-form"
                    :disabled="saving"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    Responder y marcar atendida
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
