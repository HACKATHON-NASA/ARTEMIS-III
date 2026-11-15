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
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { EnvironmentCode } from '../Types/mission';
import type { MissionFormData } from '../Types/missions';
import {
    STATUS_LABELS,
    endDate,
    formatDate,
    missionStatus,
    today,
} from '../Utils/missionDates';
import EnvironmentPicker from './EnvironmentPicker.vue';

/**
 * Crear una misión: nombre, entorno, fecha de inicio, duración y objetivo.
 * Debajo se ve cuándo termina y en qué estado queda. Las reglas finales las
 * valida el backend; aquí solo se guía el formulario.
 */
const props = defineProps<{
    takenNames: string[];
    submit: (data: MissionFormData) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const form = reactive({
    name: '',
    environment: null as EnvironmentCode | null,
    start: '',
    days: '30',
    objective: '',
});
type Field = 'name' | 'environment' | 'start' | 'days';
const errors = ref<Partial<Record<Field, string>>>({});
const saving = ref(false);

const dayCount = computed(() => Number(form.days));
const preview = computed(() => {
    if (form.start === '' || !Number.isInteger(dayCount.value)) {
        return null;
    }

    if (dayCount.value < 1) {
        return null;
    }

    return {
        end: formatDate(endDate(form.start, dayCount.value)),
        status: STATUS_LABELS[missionStatus(form.start, dayCount.value).status],
    };
});

watch(open, (isOpen) => {
    if (isOpen) {
        Object.assign(form, {
            name: '',
            environment: null,
            start: today(),
            days: '30',
            objective: '',
        });
        errors.value = {};
    }
});

// Al corregir un campo, su error se va.
watch(
    () => ({ ...form }),
    (now, before) => {
        for (const key of ['name', 'environment', 'start', 'days'] as Field[]) {
            if (now[key] !== before[key]) {
                delete errors.value[key];
            }
        }
    },
);

function validate(): boolean {
    errors.value = {};
    const name = form.name.trim();

    if (name.length < 3) {
        errors.value.name = 'Escribe un nombre de al menos 3 letras.';
    } else if (
        props.takenNames.some(
            (taken) => taken.toLowerCase() === name.toLowerCase(),
        )
    ) {
        errors.value.name = 'Ya hay una misión con ese nombre.';
    }

    if (form.environment === null) {
        errors.value.environment = 'Elige dónde será la misión.';
    }

    if (form.start === '') {
        errors.value.start = 'Elige la fecha de inicio.';
    }

    if (
        !Number.isInteger(dayCount.value) ||
        dayCount.value < 1 ||
        dayCount.value > 1000
    ) {
        errors.value.days = 'La duración va de 1 a 1000 días.';
    }

    return Object.keys(errors.value).length === 0;
}

async function save(): Promise<void> {
    if (!validate() || form.environment === null) {
        return;
    }

    saving.value = true;

    try {
        await props.submit({
            name: form.name,
            environment: form.environment,
            start_date: form.start,
            day_count: dayCount.value,
            objective: form.objective,
        });
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
                <DialogTitle>Nueva misión</DialogTitle>
                <DialogDescription>
                    Después podrás asociarle tripulantes desde Usuarios.
                </DialogDescription>
            </DialogHeader>

            <form
                id="mission-form"
                class="grid gap-4"
                novalidate
                @submit.prevent="save"
            >
                <div class="grid gap-2">
                    <Label for="mission-name">Nombre</Label>
                    <Input
                        id="mission-name"
                        v-model="form.name"
                        placeholder="Ej.: Ares-2"
                        autocomplete="off"
                    />
                    <InputError :message="errors.name" />
                </div>

                <div class="grid gap-2">
                    <span id="mission-env" class="text-sm font-medium">
                        Entorno
                    </span>
                    <EnvironmentPicker
                        v-model="form.environment"
                        labelledby="mission-env"
                    />
                    <InputError :message="errors.environment" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="grid gap-2">
                        <Label for="mission-start">Fecha de inicio</Label>
                        <Input
                            id="mission-start"
                            v-model="form.start"
                            type="date"
                        />
                        <InputError :message="errors.start" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="mission-days">Duración (días)</Label>
                        <Input
                            id="mission-days"
                            v-model="form.days"
                            type="number"
                            min="1"
                            max="1000"
                            inputmode="numeric"
                        />
                        <InputError :message="errors.days" />
                    </div>
                </div>

                <p
                    v-if="preview"
                    class="rounded-lg bg-muted/50 px-3 py-2 text-sm text-muted-foreground"
                >
                    Termina el
                    <strong class="text-foreground">{{ preview.end }}</strong>
                    · quedará
                    <strong class="text-foreground">{{
                        preview.status.toLowerCase()
                    }}</strong>
                </p>

                <div class="grid gap-2">
                    <Label for="mission-objective">
                        Objetivo
                        <span class="font-normal text-muted-foreground">
                            (opcional)
                        </span>
                    </Label>
                    <textarea
                        id="mission-objective"
                        v-model="form.objective"
                        rows="2"
                        maxlength="200"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
                        placeholder="¿Qué se estudia en esta misión?"
                    />
                </div>
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">Cancelar</Button>
                <Button
                    type="submit"
                    form="mission-form"
                    :disabled="saving"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    Crear misión
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
