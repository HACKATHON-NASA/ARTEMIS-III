<script setup lang="ts">
import { Send, Siren } from '@lucide/vue';
import { toRef } from 'vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useCountdown } from '../Composables/useCountdown';
import type { Alert } from '../Types/alerts';

/**
 * El aviso rojo de arriba: qué valor está crítico y dos acciones, ver qué
 * hacer o enviar la alerta a Tierra. Si ya se envió, muestra cuánto falta
 * para que llegue.
 */
const props = defineProps<{ alert: Alert; sending: boolean }>();
defineEmits<{ details: []; send: [] }>();

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
                    <template v-if="!alert.sent_to_ground">
                        Sigue los pasos y, si no mejora, avisa al médico en
                        Tierra.
                    </template>
                    <template v-else-if="remainingMs > 0">
                        Enviada. Llega a Tierra en
                        <strong class="text-foreground tabular-nums">{{
                            label
                        }}</strong>
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
                <Button
                    v-if="!alert.sent_to_ground"
                    :disabled="sending"
                    class="bg-[var(--status-red)] text-white hover:bg-[var(--status-red)]/90"
                    @click="$emit('send')"
                >
                    <Spinner v-if="sending" />
                    <Send v-else />
                    Enviar a Tierra
                </Button>
            </div>
        </div>
    </div>
</template>
