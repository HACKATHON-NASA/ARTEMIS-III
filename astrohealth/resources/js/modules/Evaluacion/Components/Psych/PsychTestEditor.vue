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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { PsychTest } from '../../Types/psych';
import BandsField from './BandsField.vue';
import QuestionsField from './QuestionsField.vue';
import ScaleField from './ScaleField.vue';

/**
 * Crear o editar un test: datos generales, preguntas, respuestas posibles y
 * cómo se interpreta. Trabaja sobre una copia: nada cambia hasta "Guardar".
 */
const props = defineProps<{
    test: PsychTest | null;
    submit: (test: PsychTest) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const BLANK: PsychTest = {
    id: 0,
    code: '',
    name: '',
    kind: 'psychological',
    description: '',
    instructions: '',
    frequency_days: 7,
    unit: 'puntos',
    scale: [
        { label: 'Nunca', points: 0 },
        { label: 'A veces', points: 1 },
        { label: 'Siempre', points: 2 },
    ],
    questions: [],
    bands: [
        { max: 3, label: 'Bien', status: 'green' },
        { max: 6, label: 'Vigilar', status: 'yellow' },
        { max: 99, label: 'Atender', status: 'red' },
    ],
    source: null,
};

const draft = ref<PsychTest>(JSON.parse(JSON.stringify(BLANK)) as PsychTest);
const error = ref<string | null>(null);
const saving = ref(false);

watch(open, (isOpen) => {
    if (isOpen) {
        // Copia profunda (JSON): el test del store es reactivo y no se puede clonar directo.
        draft.value = JSON.parse(
            JSON.stringify(props.test ?? BLANK),
        ) as PsychTest;
        error.value = null;
    }
});

function check(test: PsychTest): string | null {
    if (test.name.trim().length < 3) return 'Escribe el nombre del test.';
    if (
        test.kind === 'psychological' &&
        test.questions.filter((q) => q.text.trim()).length === 0
    )
        return 'Agrega al menos una pregunta.';
    if (
        test.kind === 'psychological' &&
        test.scale.filter((o) => o.label.trim()).length < 2
    )
        return 'Agrega al menos dos respuestas posibles.';
    if (test.bands.length === 0 || test.bands.some((b) => !b.label.trim()))
        return 'Cada nivel necesita un nombre.';
    if (test.bands.some((b, i) => i > 0 && b.max <= test.bands[i - 1].max))
        return 'Los niveles van de menor a mayor puntaje.';
    if (!(test.frequency_days >= 1))
        return 'La frecuencia es de al menos 1 día.';

    return null;
}

async function save(): Promise<void> {
    const test: PsychTest = {
        ...draft.value,
        name: draft.value.name.trim(),
        code:
            draft.value.code.trim() ||
            draft.value.name.trim().slice(0, 12).toUpperCase(),
        questions: draft.value.questions.filter((q) => q.text.trim()),
        scale: draft.value.scale.filter((o) => o.label.trim()),
    };
    error.value = check(test);

    if (error.value) {
        return;
    }

    saving.value = true;

    try {
        await props.submit(test);
        open.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-h-[92vh] overflow-y-auto sm:max-w-2xl">
            <DialogHeader>
                <DialogTitle>{{
                    test ? `Editar ${test.name}` : 'Nuevo test'
                }}</DialogTitle>
                <DialogDescription>
                    Lo responde la tripulación. Los cambios valen para las
                    próximas respuestas; las anteriores no cambian.
                </DialogDescription>
            </DialogHeader>

            <form
                id="psych-form"
                class="grid gap-5"
                novalidate
                @submit.prevent="save"
            >
                <div
                    class="grid grid-cols-1 gap-3 sm:grid-cols-[minmax(0,1fr)_11rem_7rem]"
                >
                    <div class="grid gap-1.5">
                        <Label for="psych-name">Nombre</Label>
                        <Input
                            id="psych-name"
                            v-model="draft.name"
                            placeholder="Ej.: Calidad del sueño"
                        />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="psych-kind">Tipo</Label>
                        <select
                            id="psych-kind"
                            v-model="draft.kind"
                            :disabled="!!test"
                            class="h-9 w-full rounded-md border border-input bg-transparent px-2 text-sm dark:bg-input/30"
                        >
                            <option value="psychological">Psicológico</option>
                            <option value="cognitive">
                                Cognitivo (reacción)
                            </option>
                        </select>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="psych-freq">Cada (días)</Label>
                        <Input
                            id="psych-freq"
                            v-model.number="draft.frequency_days"
                            type="number"
                            min="1"
                        />
                    </div>
                </div>
                <div class="grid gap-1.5">
                    <Label for="psych-desc">Qué mide</Label>
                    <Input id="psych-desc" v-model="draft.description" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="psych-inst"
                        >Instrucciones para el astronauta</Label
                    >
                    <Input id="psych-inst" v-model="draft.instructions" />
                </div>

                <template v-if="draft.kind === 'psychological'">
                    <QuestionsField v-model="draft.questions" />
                    <ScaleField v-model="draft.scale" />
                </template>
                <BandsField v-model="draft.bands" />
                <InputError :message="error ?? undefined" />
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">Cancelar</Button>
                <Button
                    type="submit"
                    form="psych-form"
                    :disabled="saving"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    Guardar test
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
