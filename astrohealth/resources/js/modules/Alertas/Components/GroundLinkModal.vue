<script setup lang="ts">
import { CircleCheck } from '@lucide/vue';
import { computed, toRef } from 'vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { useCountdown } from '../Composables/useCountdown';
import type { Alert } from '../Types/alerts';

/**
 * El enlace nave → Tierra: una señal de luz recorre la ruta y la cuenta
 * regresiva dice cuándo la recibe el médico (en Marte, unos 20 minutos).
 * La señal va dentro del SVG (animateMotion), así se escala con el dibujo.
 * La velocidad de la demo se cambia en el centro de alertas (DemoSpeedToggle).
 */
const props = defineProps<{ alert: Alert | null }>();
const open = defineModel<boolean>('open', { required: true });

const { label, remainingMs } = useCountdown(
    toRef(() => props.alert?.ground_eta_at ?? null),
);

const title = computed(
    () =>
        ({
            sos: 'SOS enviado a Tierra',
            help: 'Pedido de ayuda enviado',
            report: 'Novedad enviada',
            vital: 'Alerta en camino a Tierra',
        })[props.alert?.kind ?? 'vital'],
);

const ROUTE = 'M40 110 C 140 10, 300 10, 400 110';
const reducedMotion =
    typeof window !== 'undefined' &&
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-lg">
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
                <DialogDescription>
                    La señal viaja a la velocidad de la luz, pero la Tierra está
                    lejos.
                </DialogDescription>
            </DialogHeader>

            <div class="overflow-hidden rounded-xl border bg-[#030816] p-3">
                <svg viewBox="0 0 440 150" class="w-full" aria-hidden="true">
                    <defs>
                        <filter
                            id="signal-glow"
                            x="-200%"
                            y="-200%"
                            width="500%"
                            height="500%"
                        >
                            <feGaussianBlur stdDeviation="4" />
                        </filter>
                    </defs>
                    <path
                        :d="ROUTE"
                        fill="none"
                        stroke="#38bdf8"
                        stroke-opacity="0.35"
                        stroke-dasharray="4 8"
                    />

                    <circle
                        cx="40"
                        cy="110"
                        r="17"
                        fill="#1e3a8a"
                        stroke="#7dd3fc"
                    />
                    <text x="40" y="116" text-anchor="middle" font-size="15">
                        🚀
                    </text>
                    <text
                        x="40"
                        y="145"
                        text-anchor="middle"
                        font-size="11"
                        fill="#bae6fd"
                    >
                        Nave
                    </text>

                    <circle
                        cx="400"
                        cy="110"
                        r="19"
                        fill="#0c4a6e"
                        stroke="#7dd3fc"
                    />
                    <text x="400" y="116" text-anchor="middle" font-size="17">
                        🌍
                    </text>
                    <text
                        x="400"
                        y="145"
                        text-anchor="middle"
                        font-size="11"
                        fill="#bae6fd"
                    >
                        Tierra
                    </text>

                    <g v-if="!reducedMotion">
                        <circle r="9" fill="#38bdf8" filter="url(#signal-glow)">
                            <animateMotion
                                :path="ROUTE"
                                dur="2.6s"
                                repeatCount="indefinite"
                                calcMode="spline"
                                keyTimes="0;1"
                                keySplines="0.45 0 0.55 1"
                            />
                        </circle>
                        <circle r="4" fill="#e0f2fe">
                            <animateMotion
                                :path="ROUTE"
                                dur="2.6s"
                                repeatCount="indefinite"
                                calcMode="spline"
                                keyTimes="0;1"
                                keySplines="0.45 0 0.55 1"
                            />
                        </circle>
                    </g>
                </svg>
            </div>

            <div v-if="remainingMs > 0" class="text-center">
                <p class="text-sm text-muted-foreground">
                    El médico de vuelo la recibe en
                </p>
                <p
                    class="mt-1 text-5xl font-semibold tracking-tight tabular-nums"
                >
                    {{ label }}
                </p>
                <p class="mt-2 text-xs text-muted-foreground">
                    Su respuesta tardará lo mismo en volver. Mientras tanto,
                    sigue los pasos.
                </p>
            </div>
            <div v-else class="flex flex-col items-center gap-2 text-center">
                <CircleCheck class="size-10 text-[var(--status-green)]" />
                <p class="text-lg font-semibold">Recibida en Tierra</p>
                <p class="text-xs text-muted-foreground">
                    El médico de vuelo ya la tiene. Su respuesta viene en
                    camino.
                </p>
            </div>

            <DialogFooter>
                <Button class="w-full sm:w-auto" @click="open = false">
                    Entendido
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
