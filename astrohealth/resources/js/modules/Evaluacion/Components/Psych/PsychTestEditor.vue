<script setup lang="ts">
import { useI18n } from 'vue-i18n';
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

const { t } = useI18n();

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
    unit: t('Evaluacion.editor.defaultUnit'),
    scale: [
        { label: t('Evaluacion.editor.defaultScale.0'), points: 0 },
        { label: t('Evaluacion.editor.defaultScale.1'), points: 1 },
        { label: t('Evaluacion.editor.defaultScale.2'), points: 2 },
    ],
    questions: [],
    bands: [
        {
            max: 3,
            label: t('Evaluacion.editor.defaultBands.0'),
            status: 'green',
        },
        {
            max: 6,
            label: t('Evaluacion.editor.defaultBands.1'),
            status: 'yellow',
        },
        {
            max: 99,
            label: t('Evaluacion.editor.defaultBands.2'),
            status: 'red',
        },
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
    if (test.name.trim().length < 3) return t('Evaluacion.editor.errName');
    if (
        test.kind === 'psychological' &&
        test.questions.filter((q) => q.text.trim()).length === 0
    )
        return t('Evaluacion.editor.errQuestions');
    if (
        test.kind === 'psychological' &&
        test.scale.filter((o) => o.label.trim()).length < 2
    )
        return t('Evaluacion.editor.errScale');
    if (test.bands.length === 0 || test.bands.some((b) => !b.label.trim()))
        return t('Evaluacion.editor.errBandName');
    if (test.bands.some((b, i) => i > 0 && b.max <= test.bands[i - 1].max))
        return t('Evaluacion.editor.errBandOrder');
    if (!(test.frequency_days >= 1)) return t('Evaluacion.editor.errFrequency');

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
                    test
                        ? t('Evaluacion.editor.editTitle', { name: test.name })
                        : t('Evaluacion.editor.newTitle')
                }}</DialogTitle>
                <DialogDescription>
                    {{ t('Evaluacion.editor.hint') }}
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
                        <Label for="psych-name">{{
                            t('Evaluacion.editor.name')
                        }}</Label>
                        <Input
                            id="psych-name"
                            v-model="draft.name"
                            :placeholder="
                                t('Evaluacion.editor.namePlaceholder')
                            "
                        />
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="psych-kind">{{
                            t('Evaluacion.editor.kind')
                        }}</Label>
                        <select
                            id="psych-kind"
                            v-model="draft.kind"
                            :disabled="!!test"
                            class="h-9 w-full rounded-md border border-input bg-transparent px-2 text-sm dark:bg-input/30"
                        >
                            <option value="psychological">
                                {{ t('Evaluacion.editor.psychological') }}
                            </option>
                            <option value="cognitive">
                                {{ t('Evaluacion.editor.cognitive') }}
                            </option>
                        </select>
                    </div>
                    <div class="grid gap-1.5">
                        <Label for="psych-freq">{{
                            t('Evaluacion.editor.every')
                        }}</Label>
                        <Input
                            id="psych-freq"
                            v-model.number="draft.frequency_days"
                            type="number"
                            min="1"
                        />
                    </div>
                </div>
                <div class="grid gap-1.5">
                    <Label for="psych-desc">{{
                        t('Evaluacion.editor.measures')
                    }}</Label>
                    <Input id="psych-desc" v-model="draft.description" />
                </div>
                <div class="grid gap-1.5">
                    <Label for="psych-inst">{{
                        t('Evaluacion.editor.instructions')
                    }}</Label>
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
                <Button variant="ghost" @click="open = false">{{
                    t('common.cancel')
                }}</Button>
                <Button
                    type="submit"
                    form="psych-form"
                    :disabled="saving"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    {{ t('Evaluacion.editor.save') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
