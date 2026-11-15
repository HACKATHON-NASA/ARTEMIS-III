<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
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
import { Spinner } from '@/components/ui/spinner';
import type { GuideAdvice, GuideEdit, Threshold } from '../Types/threshold';
import AdviceFields from './AdviceFields.vue';
import RangeBar from './RangeBar.vue';

/**
 * Editar un indicador de la guía: sus rangos (con la barra en vivo) y qué
 * hacer en amarillo y en rojo. Las reglas finales las valida el backend.
 */
const props = defineProps<{
    name: string;
    unit: string;
    environmentName: string;
    min: number;
    max: number;
    decimals: number;
    threshold: Threshold | null;
    yellow: GuideAdvice;
    red: GuideAdvice;
    submit: (edit: GuideEdit) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const form = reactive({
    gmin: '',
    gmax: '',
    ymin: '',
    ymax: '',
    yTitle: '',
    ySteps: '',
    rTitle: '',
    rSteps: '',
});
const error = ref<string | null>(null);
const saving = ref(false);

const FIELDS = [
    { key: 'ymin', label: 'Amarillo desde' },
    { key: 'gmin', label: 'Verde desde' },
    { key: 'gmax', label: 'Verde hasta' },
    { key: 'ymax', label: 'Amarillo hasta' },
] as const;

const parse = (value: string) => Number(value.replace(',', '.'));

const draft = computed<Threshold | null>(() =>
    props.threshold
        ? {
              indicator: props.threshold.indicator,
              green_min: parse(form.gmin),
              green_max: parse(form.gmax),
              yellow_min: parse(form.ymin),
              yellow_max: parse(form.ymax),
          }
        : null,
);

const valid = computed(() => {
    const t = draft.value;

    return (
        t !== null &&
        [t.green_min, t.green_max, t.yellow_min, t.yellow_max].every(
            (v) => !Number.isNaN(v),
        ) &&
        props.min <= t.yellow_min &&
        t.yellow_min <= t.green_min &&
        t.green_min < t.green_max &&
        t.green_max <= t.yellow_max &&
        t.yellow_max <= props.max
    );
});

watch(open, (isOpen) => {
    if (isOpen && props.threshold) {
        const t = props.threshold;
        Object.assign(form, {
            gmin: String(t.green_min),
            gmax: String(t.green_max),
            ymin: String(t.yellow_min),
            ymax: String(t.yellow_max),
            yTitle: props.yellow?.title ?? '',
            ySteps: props.yellow?.steps.join('\n') ?? '',
            rTitle: props.red?.title ?? '',
            rSteps: props.red?.steps.join('\n') ?? '',
        });
        error.value = null;
    }
});

function advice(title: string, steps: string): GuideAdvice {
    const lines = steps
        .split('\n')
        .map((line) => line.trim())
        .filter(Boolean);

    return title.trim() && lines.length
        ? { title: title.trim(), steps: lines }
        : null;
}

async function save(): Promise<void> {
    if (!valid.value || !draft.value) {
        error.value = `Revisa los rangos: ${props.min} ≤ amarillo desde ≤ verde desde < verde hasta ≤ amarillo hasta ≤ ${props.max}.`;

        return;
    }

    saving.value = true;

    try {
        await props.submit({
            threshold: draft.value,
            yellow: advice(form.yTitle, form.ySteps),
            red: advice(form.rTitle, form.rSteps),
        });
        open.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            v-if="threshold"
            class="max-h-[92vh] overflow-y-auto sm:max-w-xl"
        >
            <DialogHeader>
                <DialogTitle>{{ name }}</DialogTitle>
                <DialogDescription>
                    Rangos para {{ environmentName }}, en {{ unit }}. Dentro del
                    verde es normal, dentro del amarillo es precaución y fuera
                    es crítico.
                </DialogDescription>
            </DialogHeader>

            <form
                id="guide-form"
                class="grid gap-4"
                novalidate
                @submit.prevent="save"
            >
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <label
                        v-for="field in FIELDS"
                        :key="field.key"
                        class="grid gap-1.5 text-xs font-medium"
                    >
                        {{ field.label }}
                        <Input
                            v-model="form[field.key]"
                            inputmode="decimal"
                            class="tabular-nums"
                            @input="error = null"
                        />
                    </label>
                </div>
                <RangeBar
                    v-if="valid && draft"
                    :threshold="draft"
                    :min="min"
                    :max="max"
                    :decimals="decimals"
                />
                <InputError :message="error ?? undefined" />

                <AdviceFields
                    v-model:title="form.yTitle"
                    v-model:steps="form.ySteps"
                    level="yellow"
                />
                <AdviceFields
                    v-model:title="form.rTitle"
                    v-model:steps="form.rSteps"
                    level="red"
                />
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">Cancelar</Button>
                <Button
                    type="submit"
                    form="guide-form"
                    :disabled="saving"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    Guardar cambios
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
