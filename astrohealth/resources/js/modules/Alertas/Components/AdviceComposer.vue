<script setup lang="ts">
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
import type { AdviceCategory, AdviceDraft } from '../Types/advice';
import { ADVICE_CATEGORIES } from '../Utils/adviceMock';

/**
 * Escribir una indicación para un tripulante: el tema, el título, los pasos
 * (uno por línea), si es importante y por cuántos días vale. Llega a la nave
 * con el retraso de la comunicación.
 */
const props = withDefaults(
    defineProps<{
        astronautName: string;
        delay: string;
        /** Desde el historial: elegir a quién (si no, va a astronautName). */
        members?: { id: number; name: string }[] | null;
        submit: (
            draft: AdviceDraft,
            astronautId: number | null,
        ) => Promise<void>;
    }>(),
    { members: null },
);

const open = defineModel<boolean>('open', { required: true });

const recipient = ref('');
const form = reactive({
    category: 'sleep' as AdviceCategory,
    title: '',
    body: '',
    important: false,
    days: '7',
});
const error = ref<string | null>(null);
const saving = ref(false);

const DAYS = [
    { value: '3', label: '3 días' },
    { value: '7', label: '1 semana' },
    { value: '14', label: '2 semanas' },
    { value: 'none', label: 'Hasta nuevo aviso' },
];

watch(open, (isOpen) => {
    if (isOpen) {
        Object.assign(form, {
            category: 'sleep',
            title: '',
            body: '',
            important: false,
            days: '7',
        });
        error.value = null;
        recipient.value = '';
    }
});

async function send(): Promise<void> {
    if (props.members && recipient.value === '') {
        error.value = 'Elige a quién va la indicación.';

        return;
    }

    if (form.title.trim().length < 3 || form.body.trim().length < 3) {
        error.value = 'Escribe un título y al menos un paso.';

        return;
    }

    saving.value = true;

    try {
        await props.submit(
            {
                category: form.category,
                title: form.title.trim(),
                body: form.body
                    .split('\n')
                    .map((line) => line.trim())
                    .filter(Boolean)
                    .join('\n'),
                important: form.important,
                valid_days: form.days === 'none' ? null : Number(form.days),
            },
            recipient.value === '' ? null : Number(recipient.value),
        );
        open.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-h-[92vh] overflow-y-auto sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>
                    {{
                        members
                            ? 'Nueva indicación'
                            : `Indicación para ${astronautName}`
                    }}
                </DialogTitle>
                <DialogDescription
                    >Le llega a la nave en {{ delay }}. La verá en su
                    Inicio.</DialogDescription
                >
            </DialogHeader>

            <form
                id="advice-form"
                class="grid gap-4"
                novalidate
                @submit.prevent="send"
            >
                <div v-if="members" class="grid gap-2">
                    <Label for="advice-to">Para</Label>
                    <select
                        id="advice-to"
                        v-model="recipient"
                        class="h-9 rounded-md border border-input bg-transparent px-2 text-sm dark:bg-input/30"
                        @change="error = null"
                    >
                        <option value="" disabled>Elige un tripulante</option>
                        <option
                            v-for="member in members"
                            :key="member.id"
                            :value="String(member.id)"
                        >
                            {{ member.name }}
                        </option>
                    </select>
                </div>
                <div class="grid gap-2">
                    <span id="advice-category" class="text-sm font-medium"
                        >Tema</span
                    >
                    <div
                        role="radiogroup"
                        aria-labelledby="advice-category"
                        class="flex flex-wrap gap-2"
                    >
                        <button
                            v-for="(meta, code) in ADVICE_CATEGORIES"
                            :key="code"
                            type="button"
                            role="radio"
                            :aria-checked="form.category === code"
                            class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-sm transition"
                            :class="
                                form.category === code
                                    ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="form.category = code"
                        >
                            <component :is="meta.icon" class="size-3.5" />
                            {{ meta.label }}
                        </button>
                    </div>
                </div>
                <div class="grid gap-2">
                    <Label for="advice-title">Título</Label>
                    <Input
                        id="advice-title"
                        v-model="form.title"
                        maxlength="80"
                        placeholder="Ej.: Higiene del sueño"
                        @input="error = null"
                    />
                </div>
                <div class="grid gap-2">
                    <Label for="advice-body"
                        >Qué hacer (un paso por línea)</Label
                    >
                    <textarea
                        id="advice-body"
                        v-model="form.body"
                        rows="4"
                        maxlength="600"
                        placeholder="Apaga pantallas 1 h antes de dormir&#10;Usa el antifaz y los tapones"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
                        @input="error = null"
                    />
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <label class="flex items-center gap-2 text-sm">
                        <input
                            v-model="form.important"
                            type="checkbox"
                            class="accent-red-500"
                        />
                        Importante
                    </label>
                    <label class="flex items-center gap-2 text-sm">
                        Vale por
                        <select
                            v-model="form.days"
                            class="h-8 rounded-md border border-input bg-transparent px-2 text-sm dark:bg-input/30"
                        >
                            <option
                                v-for="option in DAYS"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </label>
                </div>
                <InputError :message="error ?? undefined" />
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">Cancelar</Button>
                <Button
                    type="submit"
                    form="advice-form"
                    :disabled="saving"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    Enviar indicación
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
