<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { computed, ref, watch } from 'vue';
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
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import type { NewMessage } from '../Types/alerts';
import { HELP_CATEGORIES, helpCategoryLabel } from '../Utils/alertKinds';

const { t } = useI18n();

/**
 * El formulario para hablar con Tierra. SOS: confirmar (y un texto opcional,
 * para no perder segundos). Ayuda: el motivo y qué pasa. Novedad: el texto.
 */
const props = defineProps<{
    kind: NewMessage['kind'] | null;
    submit: (message: NewMessage) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const category = ref('');
const note = ref('');
const error = ref<string | null>(null);
const saving = ref(false);

const text = computed(() => {
    const kind = props.kind ?? 'report';

    return {
        title: t(`Alertas.message.${kind}Title`),
        description: t(`Alertas.message.${kind}Description`),
        placeholder: t(`Alertas.message.${kind}Placeholder`),
        button: t(`Alertas.message.${kind}Button`),
    };
});

watch(open, (isOpen) => {
    if (isOpen) {
        category.value = '';
        note.value = '';
        error.value = null;
    }
});

async function send(): Promise<void> {
    if (!props.kind) {
        return;
    }

    if (props.kind === 'help' && category.value === '') {
        error.value = t('Alertas.message.errReason');

        return;
    }

    if (props.kind !== 'sos' && note.value.trim().length < 3) {
        error.value = t('Alertas.message.errText');

        return;
    }

    saving.value = true;

    try {
        await props.submit({
            kind: props.kind,
            category: category.value || null,
            note: note.value,
        });
        open.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle
                    :class="{
                        'text-red-600 dark:text-red-400': kind === 'sos',
                    }"
                    >{{ text.title }}</DialogTitle
                >
                <DialogDescription>{{ text.description }}</DialogDescription>
            </DialogHeader>

            <form
                id="message-form"
                class="grid gap-4"
                novalidate
                @submit.prevent="send"
            >
                <div v-if="kind === 'help'" class="grid gap-2">
                    <span id="help-category" class="text-sm font-medium">{{
                        t('Alertas.message.reason')
                    }}</span>
                    <div
                        role="radiogroup"
                        aria-labelledby="help-category"
                        class="flex flex-wrap gap-2"
                    >
                        <button
                            v-for="option in HELP_CATEGORIES"
                            :key="option"
                            type="button"
                            role="radio"
                            :aria-checked="category === option"
                            class="rounded-full border px-3 py-1 text-sm transition"
                            :class="
                                category === option
                                    ? 'border-sky-500 bg-sky-500/15 text-sky-700 dark:text-sky-300'
                                    : 'text-muted-foreground hover:text-foreground'
                            "
                            @click="
                                category = option;
                                error = null;
                            "
                        >
                            {{ helpCategoryLabel(option) }}
                        </button>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="message-note">{{
                        kind === 'sos'
                            ? t('Alertas.message.messageLabel')
                            : t('Alertas.message.whatsUp')
                    }}</Label>
                    <textarea
                        id="message-note"
                        v-model="note"
                        rows="3"
                        maxlength="300"
                        :placeholder="text.placeholder"
                        class="w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 dark:bg-input/30"
                        @input="error = null"
                    />
                    <InputError :message="error ?? undefined" />
                </div>
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">{{
                    t('common.cancel')
                }}</Button>
                <Button
                    type="submit"
                    form="message-form"
                    :disabled="saving"
                    :class="
                        kind === 'sos'
                            ? 'bg-[var(--status-red)] text-white hover:bg-[var(--status-red)]/90'
                            : 'bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110'
                    "
                >
                    <Spinner v-if="saving" />
                    {{ text.button }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
