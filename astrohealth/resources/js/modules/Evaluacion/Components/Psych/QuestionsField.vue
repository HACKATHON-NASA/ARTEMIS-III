<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Plus, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { PsychQuestion } from '../../Types/psych';

const { t } = useI18n();

/**
 * Las preguntas de un cuestionario: el texto y dos marcas, "al revés" (se
 * puntúa invertida) y "crítica" (cualquier respuesta avisa a Tierra ya).
 */
const questions = defineModel<PsychQuestion[]>({ required: true });

function add(): void {
    questions.value = [
        ...questions.value,
        { id: `q${Date.now()}`, text: '', reverse: false, critical: false },
    ];
}

function remove(index: number): void {
    questions.value = questions.value.filter((_, i) => i !== index);
}
</script>

<template>
    <fieldset class="grid gap-2">
        <legend class="mb-1 text-sm font-medium">Preguntas</legend>
        <div
            v-for="(question, index) in questions"
            :key="question.id"
            class="grid gap-1.5 rounded-lg border p-2.5"
        >
            <div class="flex items-center gap-2">
                <span class="w-5 text-xs text-muted-foreground tabular-nums"
                    >{{ index + 1 }}.</span
                >
                <Input
                    v-model="question.text"
                    :aria-label="
                        t('Evaluacion.editor.questionN', { n: index + 1 })
                    "
                    :placeholder="t('Evaluacion.editor.questionPlaceholder')"
                />
                <Button
                    variant="ghost"
                    size="icon-sm"
                    :aria-label="
                        t('Evaluacion.editor.removeQuestion', { n: index + 1 })
                    "
                    @click="remove(index)"
                >
                    <Trash2 />
                </Button>
            </div>
            <div class="flex gap-4 pl-7 text-xs text-muted-foreground">
                <label class="flex items-center gap-1.5">
                    <input
                        v-model="question.reverse"
                        type="checkbox"
                        class="accent-sky-500"
                    />
                    {{ t('Evaluacion.editor.reverse') }}
                </label>
                <label class="flex items-center gap-1.5">
                    <input
                        v-model="question.critical"
                        type="checkbox"
                        class="accent-red-500"
                    />
                    {{ t('Evaluacion.editor.critical') }}
                </label>
            </div>
        </div>
        <Button
            variant="outline"
            size="sm"
            class="justify-self-start"
            @click="add"
            ><Plus /> {{ t('Evaluacion.editor.addQuestion') }}</Button
        >
    </fieldset>
</template>
