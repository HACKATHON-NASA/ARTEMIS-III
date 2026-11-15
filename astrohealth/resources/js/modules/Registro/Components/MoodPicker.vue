<script setup lang="ts">
import { CircleHelp } from '@lucide/vue';
import { ref } from 'vue';

/**
 * El ánimo con 5 caritas. Es un grupo de radio: Tab entra, las flechas
 * cambian la elección y Espacio/Enter la marca. Tocar la elegida la quita.
 */
const model = defineModel<string>({ default: '' });

defineEmits<{ help: []; focus: [] }>();

const FACES = [
    { value: '1', face: '😞', label: 'Muy mal' },
    { value: '2', face: '😕', label: 'Mal' },
    { value: '3', face: '😐', label: 'Regular' },
    { value: '4', face: '🙂', label: 'Bien' },
    { value: '5', face: '😄', label: 'Muy bien' },
];

const buttons = ref<HTMLButtonElement[]>([]);

function toggle(value: string): void {
    model.value = model.value === value ? '' : value;
}

function move(index: number, step: number): void {
    const next = (index + step + FACES.length) % FACES.length;
    model.value = FACES[next].value;
    buttons.value[next]?.focus();
}
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <div class="flex items-center justify-between gap-2">
            <span id="mood-label" class="text-sm font-medium">
                Estado de ánimo
            </span>
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
            role="radiogroup"
            aria-labelledby="mood-label"
            class="grid grid-cols-5 gap-1.5"
            data-indicator="mood"
        >
            <button
                v-for="(item, index) in FACES"
                :key="item.value"
                ref="buttons"
                type="button"
                role="radio"
                :aria-checked="model === item.value"
                :aria-label="item.label"
                :tabindex="
                    model === item.value || (!model && index === 0) ? 0 : -1
                "
                class="flex flex-col items-center gap-0.5 rounded-lg border py-2 transition hover:border-sky-500/50 hover:bg-sky-500/5 focus-visible:ring-[3px] focus-visible:ring-sky-500/20 focus-visible:outline-none"
                :class="
                    model === item.value
                        ? 'border-sky-500 bg-sky-500/10 shadow-sm'
                        : 'bg-background dark:bg-input/30'
                "
                @click="toggle(item.value)"
                @focus="$emit('focus')"
                @keydown.right.prevent="move(index, 1)"
                @keydown.down.prevent="move(index, 1)"
                @keydown.left.prevent="move(index, -1)"
                @keydown.up.prevent="move(index, -1)"
            >
                <span
                    class="text-2xl transition-transform"
                    :class="model === item.value ? 'scale-110' : 'grayscale-50'"
                    aria-hidden="true"
                >
                    {{ item.face }}
                </span>
                <span class="text-[10px] text-muted-foreground">
                    {{ item.label }}
                </span>
            </button>
        </div>
        <p class="text-xs text-muted-foreground">
            {{
                model
                    ? `Elegiste: ${FACES[Number(model) - 1].label}`
                    : 'Toca la carita de hoy'
            }}
        </p>
    </div>
</template>
