<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Clock, Save } from '@lucide/vue';
import { computed, ref, toRef } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { BodyZoneCode, IndicatorCode, Status } from '@/types/health';
import { useMeasurementForm } from '../Composables/useMeasurementForm';
import type { Indicator, RecordPayload } from '../Types/measurement';
import { FIELD_GROUPS } from '../Utils/fieldGroups';
import MeasurementField from './MeasurementField.vue';
import MoodPicker from './MoodPicker.vue';

const { t } = useI18n();

/**
 * El formulario del día: los campos agrupados por órgano, la hora de la
 * medición y la barra de "Guardar". Solo envía los campos llenos.
 */
type Latest = { value: number; status: Status; measured_at: string };

const props = defineProps<{
    indicators: Indicator[];
    latest: Partial<Record<IndicatorCode, Latest>>;
    saving: boolean;
}>();

const emit = defineEmits<{
    submit: [payload: RecordPayload];
    focusZone: [zone: BodyZoneCode | null];
    help: [indicator: Indicator];
}>();

const form = useMeasurementForm(toRef(props, 'indicators'));
const earlier = ref(false);
const time = ref('');
const timeError = ref<string | null>(null);

const groups = computed(() =>
    FIELD_GROUPS.map((group) => ({
        ...group,
        fields: group.indicators
            .map((code) => props.indicators.find((item) => item.code === code))
            .filter((item) => item !== undefined),
    })).filter((group) => group.fields.length > 0),
);

/** La hora elegida (de hoy) en ISO, o null para "ahora". */
function measuredAt(): string | null {
    timeError.value = null;

    if (!earlier.value || time.value === '') {
        return null;
    }

    const [hours, minutes] = time.value.split(':').map(Number);
    const date = new Date();
    date.setHours(hours, minutes, 0, 0);

    if (date.getTime() > Date.now()) {
        timeError.value = t('Registro.futureTime');
    }

    return date.toISOString();
}

function submit(): void {
    const at = measuredAt();

    if (form.validate() && !timeError.value) {
        emit('submit', form.payload(at));
    }
}

function groupCols(count: number): string {
    return count === 3 ? 'sm:grid-cols-3' : count === 2 ? 'sm:grid-cols-2' : '';
}

defineExpose({ reset: form.reset });
</script>

<template>
    <form class="flex flex-col gap-4" novalidate @submit.prevent="submit">
        <div class="grid gap-4 md:grid-cols-2">
            <fieldset
                v-for="group in groups"
                :key="group.zone"
                class="rounded-2xl border bg-card p-4 transition-colors focus-within:border-sky-500/40 sm:p-5"
                :class="{ 'md:col-span-2': group.fields.length > 1 }"
                @focusin="emit('focusZone', group.zone)"
            >
                <legend class="sr-only">
                    {{ t(`Registro.groups.${group.zone}.title`) }}
                </legend>
                <div class="mb-4 flex items-center gap-3">
                    <span
                        class="flex size-9 items-center justify-center rounded-xl bg-sky-500/10 text-sky-600 dark:text-sky-400"
                    >
                        <component :is="group.icon" class="size-4.5" />
                    </span>
                    <div>
                        <p class="leading-tight font-semibold">
                            {{ t(`Registro.groups.${group.zone}.title`) }}
                        </p>
                        <p class="text-xs text-muted-foreground">
                            {{ t(`Registro.groups.${group.zone}.hint`) }}
                        </p>
                    </div>
                </div>

                <div class="grid gap-4" :class="groupCols(group.fields.length)">
                    <template v-for="field in group.fields" :key="field.code">
                        <MoodPicker
                            v-if="field.code === 'mood'"
                            v-model="form.values.mood"
                            @help="emit('help', field)"
                        />
                        <MeasurementField
                            v-else
                            v-model="form.values[field.code]"
                            :indicator="field"
                            :error="form.errors[field.code]"
                            :last="latest[field.code] ?? null"
                            @help="emit('help', field)"
                            @blur="form.validateField(field)"
                        />
                    </template>
                </div>
            </fieldset>
        </div>

        <div
            class="sticky bottom-3 z-10 flex flex-col gap-3 rounded-2xl border bg-card/95 p-3 shadow-lg backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:p-4"
        >
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <Clock class="size-4 text-muted-foreground" />
                <div
                    class="inline-flex rounded-lg border bg-muted/40 p-0.5"
                    role="group"
                    :aria-label="t('Registro.measuredAt')"
                >
                    <button
                        v-for="option in [
                            { value: false, label: t('Registro.now') },
                            { value: true, label: t('Registro.earlier') },
                        ]"
                        :key="option.label"
                        type="button"
                        class="rounded-md px-2.5 py-1 text-xs font-medium transition"
                        :class="
                            earlier === option.value
                                ? 'bg-card text-foreground shadow-sm'
                                : 'text-muted-foreground hover:text-foreground'
                        "
                        :aria-pressed="earlier === option.value"
                        @click="earlier = option.value"
                    >
                        {{ option.label }}
                    </button>
                </div>
                <input
                    v-if="earlier"
                    v-model="time"
                    type="time"
                    :aria-label="t('Registro.whenToday')"
                    class="h-8 rounded-md border bg-background px-2 text-sm tabular-nums dark:bg-input/30"
                />
                <span v-if="timeError" class="text-xs text-destructive">
                    {{ timeError }}
                </span>
            </div>

            <div class="flex items-center justify-between gap-3 sm:justify-end">
                <span class="text-sm text-muted-foreground tabular-nums">
                    {{
                        t('Registro.ready', {
                            done: form.filled.value.length,
                            total: indicators.length,
                        })
                    }}
                </span>
                <Button
                    type="submit"
                    size="lg"
                    :disabled="saving || form.filled.value.length === 0"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white shadow-md shadow-sky-500/20 hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    <Save v-else />
                    {{ t('common.save') }}
                </Button>
            </div>
        </div>
    </form>
</template>
