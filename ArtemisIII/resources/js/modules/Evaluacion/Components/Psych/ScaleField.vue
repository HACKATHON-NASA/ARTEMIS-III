<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Plus, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { PsychOption } from '../../Types/psych';

const { t } = useI18n();

/**
 * La escala de respuestas (la misma para todas las preguntas): el texto que
 * ve el astronauta y cuántos puntos suma.
 */
const scale = defineModel<PsychOption[]>({ required: true });

function add(): void {
    const next =
        Math.max(-1, ...scale.value.map((option) => option.points)) + 1;
    scale.value = [...scale.value, { label: '', points: next }];
}
</script>

<template>
    <fieldset class="grid gap-2">
        <legend class="mb-1 text-sm font-medium">Respuestas posibles</legend>
        <div
            v-for="(option, index) in scale"
            :key="index"
            class="flex items-center gap-2"
        >
            <Input
                v-model="option.label"
                :aria-label="t('Evaluacion.editor.answerN', { n: index + 1 })"
                :placeholder="t('Evaluacion.editor.answerPlaceholder')"
            />
            <Input
                v-model.number="option.points"
                type="number"
                min="0"
                class="w-20 tabular-nums"
                :aria-label="t('Evaluacion.editor.pointsOf', { n: index + 1 })"
            />
            <span class="text-xs text-muted-foreground">pts</span>
            <Button
                variant="ghost"
                size="icon-sm"
                :aria-label="
                    t('Evaluacion.editor.removeAnswer', { n: index + 1 })
                "
                @click="scale = scale.filter((_, i) => i !== index)"
            >
                <Trash2 />
            </Button>
        </div>
        <Button
            variant="outline"
            size="sm"
            class="justify-self-start"
            @click="add"
            ><Plus /> {{ t('Evaluacion.editor.addAnswer') }}</Button
        >
    </fieldset>
</template>
