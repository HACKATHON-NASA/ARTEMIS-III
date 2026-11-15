<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { currentLocale, formatNumber } from '@/lib/i18n';
import { indicatorName } from '@/lib/indicators';
import { Link } from '@inertiajs/vue3';
import { BellRing, CircleCheck, HeartPulse, RotateCcw } from '@lucide/vue';
import { computed } from 'vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { statusMeta } from '@/lib/status';
import { alertas, inicio } from '@/routes';
import type { Indicator, Measurement } from '../Types/measurement';

const { t } = useI18n();

/**
 * Lo que se acaba de guardar, cada valor con el color que le puso el
 * backend, y qué sigue: ver el cuerpo, revisar las alertas o registrar más.
 */
const props = defineProps<{
    measurements: Measurement[];
    indicators: Indicator[];
    alertsCreated: number;
}>();

defineEmits<{ again: [] }>();

const nameOf = (code: Measurement['indicator']) =>
    indicatorName(
        code,
        props.indicators.find((item) => item.code === code)?.name ?? code,
    );

const allGreen = computed(() =>
    props.measurements.every((item) => item.status === 'green'),
);
const hasRed = computed(() =>
    props.measurements.some((item) => item.status === 'red'),
);

const time = computed(() =>
    props.measurements[0]
        ? new Date(props.measurements[0].measured_at).toLocaleTimeString(
              currentLocale(),
              {
                  hour: '2-digit',
                  minute: '2-digit',
              },
          )
        : '',
);
</script>

<template>
    <section class="rounded-2xl border bg-card p-4 sm:p-6">
        <div class="flex items-start gap-3">
            <span
                class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-[var(--status-green)]/15 text-[var(--status-green)]"
            >
                <CircleCheck class="size-5" />
            </span>
            <div>
                <h2 class="text-lg font-semibold">
                    {{
                        t(
                            'Registro.savedValues',
                            { n: measurements.length },
                            measurements.length,
                        )
                    }}
                </h2>
                <p class="text-sm text-muted-foreground">
                    {{ t('Registro.measuredAtTime', { time }) }}
                    {{
                        allGreen
                            ? t('Registro.allGreen')
                            : t('Registro.seeColors')
                    }}
                </p>
            </div>
        </div>

        <ul class="mt-5 grid gap-2 sm:grid-cols-2">
            <li
                v-for="item in measurements"
                :key="item.id"
                class="flex items-center justify-between gap-3 rounded-xl border px-3 py-2.5"
                :style="{
                    borderColor:
                        item.status === 'green'
                            ? undefined
                            : `color-mix(in srgb, ${statusMeta(item.status).color} 40%, transparent)`,
                }"
            >
                <div class="min-w-0">
                    <p class="truncate text-sm text-muted-foreground">
                        {{ nameOf(item.indicator) }}
                    </p>
                    <p class="font-semibold tabular-nums">
                        {{ formatNumber(item.value) }}
                        <span class="text-sm font-normal text-muted-foreground">
                            {{
                                item.unit === '1-5'
                                    ? t('Registro.outOf5')
                                    : item.unit
                            }}
                        </span>
                    </p>
                </div>
                <StatusBadge :status="item.status" />
            </li>
        </ul>

        <div
            v-if="alertsCreated > 0"
            class="mt-4 flex items-start gap-3 rounded-xl border p-3 text-sm"
            :class="
                hasRed
                    ? 'border-[var(--status-red)]/35 bg-[var(--status-red)]/8'
                    : 'border-[var(--status-yellow)]/35 bg-[var(--status-yellow)]/8'
            "
        >
            <BellRing class="mt-0.5 size-4 shrink-0" />
            <p>
                {{
                    t(
                        'Registro.alertsCreated',
                        { n: alertsCreated },
                        alertsCreated,
                    )
                }}
                {{
                    hasRed
                        ? t('Registro.criticalHint')
                        : t('Registro.reviewHint')
                }}
            </p>
        </div>

        <div class="mt-5 flex flex-wrap gap-2">
            <Button
                v-if="alertsCreated > 0"
                as-child
                class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
            >
                <Link :href="alertas()"
                    ><BellRing /> {{ t('Registro.seeAlerts') }}</Link
                >
            </Button>
            <Button variant="outline" as-child>
                <Link :href="inicio()"
                    ><HeartPulse /> {{ t('Registro.goHome') }}</Link
                >
            </Button>
            <Button variant="ghost" @click="$emit('again')">
                <RotateCcw /> {{ t('Registro.logMore') }}
            </Button>
        </div>
    </section>
</template>
