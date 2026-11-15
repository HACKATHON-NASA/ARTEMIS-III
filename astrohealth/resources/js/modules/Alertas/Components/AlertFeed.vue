<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { agoFromMinutes } from '@/lib/i18n';
import { alertMessage } from '../Utils/alertText';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, BellOff, Radio } from '@lucide/vue';
import { alertas } from '@/routes';
import type { Alert } from '../Types/alerts';
import { KIND_META } from '../Utils/alertKinds';
import { alertState } from '../Utils/alertState';

const { t } = useI18n();

/**
 * Las novedades en vivo del Panel de salud: lo último que llegó de la
 * tripulación (SOS, ayuda, novedades y valores fuera de rango), lo urgente
 * sin atender primero, con "Atender" cuando ya llegó a Tierra.
 */
defineProps<{
    alerts: Alert[];
    nameOf: (astronautId: number) => string;
    canAttend: boolean;
}>();

defineEmits<{ attend: [alert: Alert] }>();

const TONE = {
    red: 'bg-[var(--status-red)] text-white',
    yellow: 'bg-[var(--status-yellow)]/15 text-amber-600 dark:text-amber-300',
    info: 'bg-sky-500/12 text-sky-600 dark:text-sky-300',
} as const;

function ago(iso: string): string {
    return agoFromMinutes((Date.now() - new Date(iso).getTime()) / 60_000);
}

function stateText(alert: Alert): string {
    if (alert.acknowledged_at) {
        return t('Alertas.feed.handled');
    }

    return alertState(alert) === 'traveling'
        ? t('Alertas.feed.onTheWay')
        : t('Alertas.feed.unattended');
}
</script>

<template>
    <section class="flex flex-col rounded-2xl border bg-card">
        <header
            class="flex items-center justify-between gap-3 border-b p-4 sm:px-6"
        >
            <h2 class="flex items-center gap-2 font-semibold">
                <Radio class="size-4 text-sky-500" />
                {{ t('Alertas.feed.title') }}
                <span
                    class="flex items-center gap-1.5 text-xs font-normal text-muted-foreground"
                >
                    <span
                        class="size-1.5 animate-pulse rounded-full bg-[var(--status-green)] motion-reduce:animate-none"
                    />
                    {{ t('Alertas.feed.live') }}
                </span>
            </h2>
            <Link
                :href="alertas()"
                class="inline-flex items-center gap-1 text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
                {{ t('Alertas.feed.seeAll') }} <ArrowRight class="size-4" />
            </Link>
        </header>

        <TransitionGroup
            v-if="alerts.length"
            tag="ul"
            class="divide-y"
            enter-active-class="transition duration-500 ease-out"
            enter-from-class="-translate-y-2 opacity-0"
        >
            <li
                v-for="alert in alerts"
                :key="alert.id"
                class="flex items-start gap-3 px-4 py-3 sm:px-6"
            >
                <span
                    class="mt-0.5 flex size-9 shrink-0 items-center justify-center rounded-lg"
                    :class="TONE[alert.level]"
                >
                    <component
                        :is="KIND_META[alert.kind].icon"
                        class="size-4"
                    />
                </span>
                <div class="min-w-0 flex-1">
                    <p
                        class="truncate text-sm font-semibold"
                        :class="{
                            'text-muted-foreground': alert.acknowledged_at,
                        }"
                    >
                        {{ alertMessage(alert, nameOf(alert.astronaut_id)) }}
                    </p>
                    <p class="truncate text-xs text-muted-foreground">
                        {{ nameOf(alert.astronaut_id) }} ·
                        {{ ago(alert.created_at) }} · {{ stateText(alert) }}
                    </p>
                    <p
                        v-if="alert.note"
                        class="mt-1 line-clamp-1 text-xs italic"
                    >
                        “{{ alert.note }}”
                    </p>
                </div>
                <button
                    v-if="
                        canAttend &&
                        !alert.acknowledged_at &&
                        alertState(alert) === 'received'
                    "
                    type="button"
                    class="shrink-0 rounded-lg bg-linear-to-r from-sky-500 to-blue-600 px-3 py-1.5 text-xs font-medium text-white hover:brightness-110"
                    @click="$emit('attend', alert)"
                >
                    {{ t('Alertas.feed.attend') }}
                </button>
            </li>
        </TransitionGroup>

        <div
            v-else
            class="flex flex-col items-center gap-2 p-10 text-center text-sm text-muted-foreground"
        >
            <BellOff class="size-6" />
            {{ t('Alertas.feed.nothing') }}
        </div>
    </section>
</template>
