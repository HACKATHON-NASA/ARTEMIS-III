<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { resultBand, resultDetail } from '../../Utils/psychText';
import { ChevronLeft, LifeBuoy } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Spinner } from '@/components/ui/spinner';
import type {
    PsychResult,
    PsychSubmission,
    PsychTest,
} from '../../Types/psych';
import PvtRunner from './PvtRunner.vue';

const { t } = useI18n();

/**
 * Responder un test: las instrucciones, una pregunta a la vez (o la prueba
 * de reacción) y el resultado al final. Las respuestas no se pueden ver
 * después: solo el puntaje, para cuidar la privacidad.
 */
const props = defineProps<{
    test: PsychTest | null;
    submit: (submission: PsychSubmission) => Promise<PsychResult | null>;
}>();

const open = defineModel<boolean>('open', { required: true });

const step = ref<'intro' | 'run' | 'saving' | 'done'>('intro');
const current = ref(0);
const answers = ref<number[]>([]);
const result = ref<PsychResult | null>(null);

const total = computed(() => props.test?.questions.length ?? 0);
const progress = computed(() =>
    total.value ? Math.round((current.value / total.value) * 100) : 0,
);

watch(open, (isOpen) => {
    if (isOpen) {
        step.value = 'intro';
        current.value = 0;
        answers.value = [];
        result.value = null;
    }
});

async function finish(submission: PsychSubmission): Promise<void> {
    step.value = 'saving';
    result.value = await props.submit(submission);
    step.value = 'done';
}

function answer(option: number): void {
    answers.value[current.value] = option;

    if (current.value < total.value - 1) {
        current.value++;

        return;
    }

    void finish({ kind: 'psychological', answers: [...answers.value] });
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            v-if="test"
            class="max-h-[92vh] overflow-y-auto sm:max-w-lg"
        >
            <DialogHeader>
                <DialogTitle>{{ test.name }}</DialogTitle>
                <DialogDescription>{{ test.instructions }}</DialogDescription>
            </DialogHeader>

            <div v-if="step === 'intro'" class="flex flex-col gap-4">
                <p class="text-sm text-muted-foreground">
                    {{ t('Evaluacion.psych.noRightAnswers') }}
                </p>
                <Button
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                    @click="step = 'run'"
                >
                    {{ t('Evaluacion.psych.start') }}
                </Button>
            </div>

            <template
                v-else-if="step === 'run' && test.kind === 'psychological'"
            >
                <div
                    class="flex items-center gap-3 text-xs text-muted-foreground"
                >
                    <span class="tabular-nums"
                        >{{ current + 1 }} / {{ total }}</span
                    >
                    <div
                        class="h-1.5 flex-1 overflow-hidden rounded-full bg-muted"
                    >
                        <div
                            class="h-full rounded-full bg-sky-500 transition-[width] duration-300"
                            :style="{ width: `${progress}%` }"
                        />
                    </div>
                </div>
                <Transition
                    mode="out-in"
                    enter-active-class="transition duration-200"
                    enter-from-class="translate-x-2 opacity-0"
                >
                    <div :key="current" class="flex flex-col gap-3">
                        <p class="text-lg font-medium">
                            {{ test.questions[current].text }}
                        </p>
                        <button
                            v-for="(option, index) in test.scale"
                            :key="option.label"
                            type="button"
                            class="rounded-xl border px-4 py-3 text-left text-sm transition hover:border-sky-500/60 hover:bg-sky-500/5 focus-visible:ring-[3px] focus-visible:ring-sky-500/25 focus-visible:outline-none"
                            :class="{
                                'border-sky-500 bg-sky-500/10':
                                    answers[current] === index,
                            }"
                            @click="answer(index)"
                        >
                            {{ option.label }}
                        </button>
                    </div>
                </Transition>
                <Button
                    v-if="current > 0"
                    variant="ghost"
                    size="sm"
                    class="self-start"
                    @click="current--"
                >
                    <ChevronLeft /> {{ t('Evaluacion.psych.previous') }}
                </Button>
            </template>

            <PvtRunner
                v-else-if="step === 'run'"
                @done="
                    finish({
                        kind: 'cognitive',
                        reactions: $event.reactions,
                        falseStarts: $event.falseStarts,
                    })
                "
            />

            <div
                v-else-if="step === 'saving'"
                class="flex items-center justify-center gap-2 py-10 text-sm text-muted-foreground"
            >
                <Spinner /> {{ t('Evaluacion.psych.saving') }}
            </div>

            <div
                v-else-if="result"
                class="flex flex-col items-center gap-3 py-4 text-center"
            >
                <p class="text-sm text-muted-foreground">
                    {{ t('Evaluacion.psych.yourResult') }}
                </p>
                <p class="text-5xl font-semibold tabular-nums">
                    {{ result.score
                    }}<span class="text-base font-normal text-muted-foreground">
                        {{ test.unit }}</span
                    >
                </p>
                <div class="flex items-center gap-2">
                    <StatusBadge :status="result.status" size="md" />
                    <span class="text-sm font-medium">{{
                        resultBand(test, result)
                    }}</span>
                </div>
                <p v-if="result.detail" class="text-xs text-muted-foreground">
                    {{ resultDetail(result.detail) }}
                </p>
                <div
                    v-if="result.flagged"
                    class="mt-2 flex gap-3 rounded-xl border border-[var(--status-red)]/40 bg-[var(--status-red)]/8 p-3 text-left text-sm"
                >
                    <LifeBuoy
                        class="mt-0.5 size-4 shrink-0 text-[var(--status-red)]"
                    />
                    <p>
                        {{ t('Evaluacion.psych.flagged') }}
                    </p>
                </div>
                <Button class="mt-2" @click="open = false">{{
                    t('Evaluacion.psych.done')
                }}</Button>
            </div>
        </DialogContent>
    </Dialog>
</template>
