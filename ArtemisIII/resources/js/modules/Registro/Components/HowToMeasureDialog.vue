<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { indicatorHow, indicatorName, indicatorWhy } from '@/lib/indicators';
import { Orbit, Ruler } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { Indicator } from '../Types/measurement';

const { t } = useI18n();

/**
 * "¿Cómo me mido?": cómo tomar el valor y por qué importa en el espacio
 * (el help_text del indicador).
 */
defineProps<{ indicator: Indicator | null }>();

const open = defineModel<boolean>('open', { required: true });
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent v-if="indicator" class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>{{
                    indicatorName(indicator.code, indicator.name)
                }}</DialogTitle>
                <DialogDescription>
                    {{ t('Registro.howDialog.unit', { unit: indicator.unit }) }}
                </DialogDescription>
            </DialogHeader>

            <div class="flex flex-col gap-3">
                <div class="flex gap-3 rounded-xl border p-4">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-sky-500/10 text-sky-600 dark:text-sky-400"
                    >
                        <Ruler class="size-4" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold">
                            {{ t('Registro.howDialog.how') }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{
                                indicatorHow(
                                    indicator.code,
                                    indicator.help_text.how,
                                )
                            }}
                        </p>
                    </div>
                </div>
                <div class="flex gap-3 rounded-xl border p-4">
                    <span
                        class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"
                    >
                        <Orbit class="size-4" />
                    </span>
                    <div>
                        <p class="text-sm font-semibold">
                            {{ t('Registro.howDialog.why') }}
                        </p>
                        <p class="mt-1 text-sm text-muted-foreground">
                            {{
                                indicatorWhy(
                                    indicator.code,
                                    indicator.help_text.why,
                                )
                            }}
                        </p>
                    </div>
                </div>
            </div>

            <DialogFooter>
                <Button class="w-full sm:w-auto" @click="open = false">
                    {{ t('Registro.howDialog.ok') }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
