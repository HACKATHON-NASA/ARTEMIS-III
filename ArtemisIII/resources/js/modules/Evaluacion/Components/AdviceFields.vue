<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import InputError from '@/components/InputError.vue';
import { Input } from '@/components/ui/input';
import { statusMeta } from '@/lib/status';

const { t } = useI18n();

/**
 * "Qué hacer" en un nivel (amarillo o rojo): un título y los pasos, uno por
 * línea. Si se deja vacío, ese nivel queda sin pasos.
 */
defineProps<{ level: 'yellow' | 'red'; error?: string }>();

const title = defineModel<string>('title', { required: true });
const steps = defineModel<string>('steps', { required: true });
</script>

<template>
    <fieldset
        class="grid gap-2 rounded-xl border p-3"
        :style="{
            borderColor: `color-mix(in srgb, ${statusMeta(level).color} 35%, transparent)`,
        }"
    >
        <legend class="flex items-center gap-1.5 px-1 text-xs font-semibold">
            <span
                class="size-2 rounded-full"
                :style="{ background: statusMeta(level).color }"
            />
            {{
                t('Evaluacion.guide.whatIf', {
                    status: statusMeta(level).label.toLowerCase(),
                })
            }}
        </legend>
        <Input
            v-model="title"
            :aria-label="
                t('Evaluacion.guide.adviceTitle', {
                    status: statusMeta(level).label,
                })
            "
            :placeholder="t('Evaluacion.guide.adviceTitlePlaceholder')"
        />
        <textarea
            v-model="steps"
            rows="3"
            :aria-label="
                t('Evaluacion.guide.adviceSteps', {
                    status: statusMeta(level).label,
                })
            "
            :placeholder="t('Evaluacion.guide.adviceStepsPlaceholder')"
            class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
        />
        <InputError :message="error" />
    </fieldset>
</template>
