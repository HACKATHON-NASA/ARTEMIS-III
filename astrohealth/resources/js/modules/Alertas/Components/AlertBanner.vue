<script setup lang="ts">
import { Siren } from '@lucide/vue';
import { toRef } from 'vue';
import { Button } from '@/components/ui/button';
import { useCountdown } from '../Composables/useCountdown';
import type { Alert } from '../Types/alerts';

/**
 * El aviso rojo de arriba: qué está crítico (o el SOS) y en qué va su viaje
 * a Tierra (sale sola al crearse). La acción es ver qué hacer mientras tanto.
 */
const props = defineProps<{ alert: Alert }>();
defineEmits<{ details: [] }>();

const { remainingMs, label } = useCountdown(
    toRef(() => props.alert.ground_eta_at),
);
</script>

<template>
    <div
        role="alert"
        class="relative overflow-hidden rounded-2xl border border-[var(--status-red)]/40 bg-linear-to-r from-[var(--status-red)]/15 via-[var(--status-red)]/5 to-transparent p-4 sm:p-5"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <span
                class="relative flex size-11 shrink-0 items-center justify-center rounded-xl bg-[var(--status-red)] text-white"
            >
                <span
                    class="absolute inset-0 animate-ping rounded-xl bg-[var(--status-red)] opacity-40 motion-reduce:hidden"
                />
                <Siren class="relative size-5" />
            </span>

            <div class="min-w-0 flex-1">
                <p class="font-semibold text-red-700 dark:text-red-300">
                    {{ alert.message }}
                </p>
                <p class="text-sm text-muted-foreground">
                    <template v-if="remainingMs > 0">
                        Ya va en camino a Tierra, sola. Llega en
                        <strong class="text-foreground tabular-nums">{{
                            label
                        }}</strong>
                        · mientras tanto, sigue los pasos.
                    </template>
                    <template v-else
                        >Recibida en Tierra. Esperando respuesta del
                        médico.</template
                    >
                </p>
            </div>

            <div class="flex gap-2">
                <Button variant="outline" @click="$emit('details')"
                    >Qué hacer</Button
                >
            </div>
        </div>
    </div>
</template>
