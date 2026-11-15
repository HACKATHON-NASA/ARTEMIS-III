<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Check, Copy } from '@lucide/vue';
import { ref } from 'vue';

const { t } = useI18n();

/**
 * Copia un texto al portapapeles y muestra un ✓ un momento.
 */
const props = defineProps<{ value: string; label: string }>();

const copied = ref(false);
let timer: ReturnType<typeof setTimeout> | undefined;

async function copy(): Promise<void> {
    try {
        await navigator.clipboard.writeText(props.value);
        copied.value = true;
        clearTimeout(timer);
        timer = setTimeout(() => (copied.value = false), 1600);
    } catch {
        copied.value = false;
    }
}
</script>

<template>
    <button
        type="button"
        class="inline-flex size-7 shrink-0 items-center justify-center rounded-md text-muted-foreground transition hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
        :aria-label="copied ? t('Identidad.admin.copied') : label"
        :title="copied ? t('Identidad.admin.copied') : label"
        @click="copy"
    >
        <Transition
            mode="out-in"
            enter-active-class="transition duration-150"
            enter-from-class="scale-50 opacity-0"
            leave-active-class="transition duration-100"
            leave-to-class="scale-50 opacity-0"
        >
            <Check v-if="copied" class="size-4 text-[var(--status-green)]" />
            <Copy v-else class="size-4" />
        </Transition>
    </button>
</template>
