<script setup lang="ts">
import { computed } from 'vue';
import type { Alert } from '../Types/alerts';
import { alertState, replyRemainingMs } from '../Utils/alertState';

/**
 * El recorrido de una alerta en 4 pasos: enviada (sale sola al crearse) →
 * recibida en Tierra → atendida → la respuesta llega a la nave. Los pasos
 * cumplidos se marcan en azul.
 */
const props = defineProps<{ alert: Alert; now: number }>();

const time = (iso: string | null): string =>
    iso === null
        ? ''
        : new Date(iso).toLocaleTimeString('es', {
              hour: '2-digit',
              minute: '2-digit',
          });

const steps = computed(() => {
    const state = alertState(props.alert, props.now);
    const received = state !== 'traveling';
    const acknowledged = state === 'acknowledged';
    const replied =
        acknowledged && replyRemainingMs(props.alert, props.now) === 0;
    const replyAt =
        props.alert.acknowledged_at === null
            ? null
            : new Date(
                  new Date(props.alert.acknowledged_at).getTime() +
                      props.alert.delay_seconds * 1000,
              ).toISOString();

    return [
        { label: 'Enviada', done: true, at: time(props.alert.sent_at) },
        {
            label: 'En Tierra',
            done: received,
            at: received ? time(props.alert.ground_eta_at) : '',
        },
        {
            label: 'Atendida',
            done: acknowledged,
            at: time(props.alert.acknowledged_at),
        },
        {
            label: 'Respuesta en la nave',
            done: replied,
            at: replied ? time(replyAt) : '',
        },
    ];
});
</script>

<template>
    <ol class="grid grid-cols-4 gap-1" aria-label="Recorrido de la alerta">
        <li
            v-for="(step, index) in steps"
            :key="step.label"
            class="flex flex-col gap-1.5"
        >
            <span class="flex items-center">
                <span
                    class="size-2.5 shrink-0 rounded-full border-2 transition-colors"
                    :class="
                        step.done
                            ? 'border-sky-500 bg-sky-500'
                            : 'border-muted-foreground/40 bg-transparent'
                    "
                />
                <span
                    v-if="index < steps.length - 1"
                    class="h-0.5 flex-1 rounded-full transition-colors"
                    :class="steps[index + 1].done ? 'bg-sky-500' : 'bg-muted'"
                />
            </span>
            <span
                class="text-[11px] leading-tight"
                :class="step.done ? 'text-foreground' : 'text-muted-foreground'"
            >
                {{ step.label }}
                <span
                    v-if="step.at"
                    class="block text-muted-foreground tabular-nums"
                    >{{ step.at }}</span
                >
            </span>
        </li>
    </ol>
</template>
