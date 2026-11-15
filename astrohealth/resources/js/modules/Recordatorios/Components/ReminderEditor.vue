<script setup lang="ts">
import { Trash2 } from '@lucide/vue';
import { reactive, ref, watch } from 'vue';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { Reminder, ReminderDraft, ReminderType } from '../Types/reminders';
import { REMINDER_TYPES } from '../Utils/reminderTypes';

/**
 * Crear o cambiar un recordatorio: qué es (el tipo pone un título sugerido),
 * el título y la hora. Al editar, también se puede eliminar.
 */
const props = defineProps<{
    reminder: Reminder | null;
    submit: (draft: ReminderDraft) => Promise<void>;
    remove: () => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const form = reactive<ReminderDraft>({
    type: 'custom',
    title: '',
    time: '09:00',
});
const error = ref<string | null>(null);
const saving = ref(false);
const confirming = ref(false);

watch(open, (isOpen) => {
    if (isOpen) {
        Object.assign(
            form,
            props.reminder
                ? {
                      type: props.reminder.type,
                      title: props.reminder.title,
                      time: props.reminder.time,
                  }
                : { type: 'custom', title: '', time: '09:00' },
        );
        error.value = null;
        confirming.value = false;
    }
});

function pick(type: ReminderType): void {
    const suggested = REMINDER_TYPES.find((item) => item.type === type);
    const isSuggestion = REMINDER_TYPES.some(
        (item) => item.suggestion === form.title,
    );

    form.type = type;

    if (suggested && (form.title === '' || isSuggestion)) {
        form.title = suggested.suggestion;
    }
}

async function save(): Promise<void> {
    if (form.title.trim().length < 3) {
        error.value = 'Escribe qué hay que hacer.';

        return;
    }

    if (!/^([01]\d|2[0-3]):[0-5]\d$/.test(form.time)) {
        error.value = 'Elige una hora válida.';

        return;
    }

    saving.value = true;

    try {
        await props.submit({ ...form, title: form.title.trim() });
        open.value = false;
    } finally {
        saving.value = false;
    }
}

async function destroy(): Promise<void> {
    if (!confirming.value) {
        confirming.value = true;

        return;
    }

    saving.value = true;

    try {
        await props.remove();
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
                <DialogTitle>{{
                    reminder ? 'Editar recordatorio' : 'Nuevo recordatorio'
                }}</DialogTitle>
                <DialogDescription
                    >Se repite todos los días a la misma
                    hora.</DialogDescription
                >
            </DialogHeader>

            <form
                id="reminder-form"
                class="grid gap-4"
                novalidate
                @submit.prevent="save"
            >
                <div class="grid gap-2">
                    <span id="reminder-type" class="text-sm font-medium"
                        >Qué es</span
                    >
                    <div
                        role="radiogroup"
                        aria-labelledby="reminder-type"
                        class="grid grid-cols-3 gap-2"
                    >
                        <button
                            v-for="item in REMINDER_TYPES"
                            :key="item.type"
                            type="button"
                            role="radio"
                            :aria-checked="form.type === item.type"
                            class="flex flex-col items-center gap-1 rounded-xl border p-2 text-xs transition"
                            :class="
                                form.type === item.type
                                    ? 'border-sky-500 bg-sky-500/10'
                                    : 'text-muted-foreground hover:border-sky-500/50'
                            "
                            @click="pick(item.type)"
                        >
                            <component :is="item.icon" class="size-4" />
                            {{ item.label }}
                        </button>
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="reminder-title">Título</Label>
                    <Input
                        id="reminder-title"
                        v-model="form.title"
                        maxlength="80"
                        placeholder="Ej.: Tomar la vitamina D"
                        @input="error = null"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="reminder-time">Hora</Label>
                    <Input
                        id="reminder-time"
                        v-model="form.time"
                        type="time"
                        class="w-36"
                        @input="error = null"
                    />
                </div>
                <InputError :message="error ?? undefined" />
            </form>

            <DialogFooter class="gap-2 sm:justify-between">
                <Button
                    v-if="reminder"
                    variant="ghost"
                    :disabled="saving"
                    class="text-destructive hover:text-destructive"
                    @click="destroy"
                >
                    <Trash2 />
                    {{ confirming ? '¿Seguro? Tocar otra vez' : 'Eliminar' }}
                </Button>
                <span v-else />
                <div class="flex gap-2">
                    <Button variant="ghost" @click="open = false"
                        >Cancelar</Button
                    >
                    <Button
                        type="submit"
                        form="reminder-form"
                        :disabled="saving"
                        class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                    >
                        <Spinner v-if="saving" />
                        Guardar
                    </Button>
                </div>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
