<script setup lang="ts">
import { CircleHelp } from '@lucide/vue';
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { Status } from '@/types/health';
import type { Indicator } from '../Types/measurement';

/**
 * Un campo numérico con su unidad, el último valor guardado como referencia
 * y el botón "¿Cómo me mido?". El error sale debajo, en palabras simples.
 */
const props = defineProps<{
    indicator: Indicator;
    error?: string;
    last: { value: number; status: Status; measured_at: string } | null;
}>();

const model = defineModel<string>({ default: '' });

defineEmits<{ help: []; focus: []; blur: [] }>();

const id = computed(() => `field-${props.indicator.code}`);

const lastLabel = computed(() => {
    if (!props.last) {
        return null;
    }

    const hours = Math.round(
        (Date.now() - new Date(props.last.measured_at).getTime()) / 3_600_000,
    );
    const ago =
        hours < 1 ? 'hace un rato' : hours < 24 ? `hace ${hours} h` : 'ayer';

    return `${props.last.value.toLocaleString('es')} ${props.indicator.unit} · ${ago}`;
});
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <div class="flex items-start justify-between gap-2">
            <label :for="id" class="text-sm font-medium">
                {{ indicator.name }}
            </label>
            <button
                type="button"
                class="inline-flex shrink-0 items-center gap-1 rounded-md px-1.5 py-0.5 text-xs whitespace-nowrap text-sky-600 transition hover:bg-sky-500/10 focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none dark:text-sky-400"
                @click="$emit('help')"
            >
                <CircleHelp class="size-3.5" />
                ¿Cómo me mido?
            </button>
        </div>

        <div
            class="group relative flex items-center rounded-lg border bg-background shadow-xs transition focus-within:border-sky-500 focus-within:ring-[3px] focus-within:ring-sky-500/20 dark:bg-input/30"
            :class="{
                'border-destructive focus-within:border-destructive focus-within:ring-destructive/20':
                    error,
            }"
            :data-indicator="indicator.code"
        >
            <input
                :id="id"
                v-model="model"
                type="text"
                inputmode="decimal"
                autocomplete="off"
                :placeholder="last ? last.value.toLocaleString('es') : '—'"
                :aria-invalid="error ? true : undefined"
                :aria-describedby="error ? `${id}-error` : `${id}-hint`"
                class="h-11 w-full min-w-0 bg-transparent px-3 text-lg font-semibold tabular-nums outline-none placeholder:font-normal placeholder:text-muted-foreground/50"
                @focus="$emit('focus')"
                @blur="$emit('blur')"
            />
            <span
                class="pointer-events-none pr-3 text-sm text-muted-foreground"
            >
                {{ indicator.unit }}
            </span>
        </div>

        <p
            v-if="error"
            :id="`${id}-error`"
            class="text-xs text-destructive"
            role="alert"
        >
            {{ error }}
        </p>
        <p
            v-else
            :id="`${id}-hint`"
            class="flex items-center gap-1.5 text-xs text-muted-foreground"
        >
            <template v-if="lastLabel && last">
                <span
                    class="size-1.5 rounded-full"
                    :style="{ background: statusMeta(last.status).color }"
                />
                Último: {{ lastLabel }}
            </template>
            <template v-else>Sin registros todavía</template>
        </p>
    </div>
</template>
