<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { agoFromMinutes } from '@/lib/i18n';
import { useAlertText } from '../Composables/useAlertText';
import { ChevronDown, MessageCircleReply, Stethoscope } from '@lucide/vue';
import { computed, ref, toRef } from 'vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { useCountdown } from '../Composables/useCountdown';
import type { Alert, Recommendation } from '../Types/alerts';
import { KIND_META } from '../Utils/alertKinds';
import { alertState, replyRemainingMs, stateLabel } from '../Utils/alertState';
import AlertTimeline from './AlertTimeline.vue';
import RecommendationCard from './RecommendationCard.vue';

const { t } = useI18n();
const text = useAlertText();

/**
 * Una alerta: qué pasó (o qué dijo el astronauta), de quién, en qué paso va
 * y la respuesta de Tierra. Quien atiende ve "Atender" cuando ya llegó.
 */
const props = defineProps<{
    alert: Alert;
    astronautName: string | null;
    recommendation: Recommendation | null;
    canAttend: boolean;
}>();

defineEmits<{ attend: [] }>();

const open = ref(false);
const { label, now } = useCountdown(toRef(() => props.alert.ground_eta_at));
const state = computed(() => alertState(props.alert, now.value));
const kind = computed(() => KIND_META[props.alert.kind]);
const replyIn = computed(() => replyRemainingMs(props.alert, now.value));

const ago = computed(() =>
    agoFromMinutes(
        (now.value - new Date(props.alert.created_at).getTime()) / 60_000,
    ),
);

const mmss = (ms: number) => {
    const total = Math.ceil(ms / 1000);

    return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
};

const ICON_CLASS = {
    red: 'bg-[var(--status-red)] text-white',
    yellow: 'bg-[var(--status-yellow)]/15 text-amber-600 dark:text-amber-300',
    info: 'bg-sky-500/12 text-sky-600 dark:text-sky-300',
} as const;
</script>

<template>
    <li
        class="rounded-2xl border bg-card p-4 transition hover:border-sky-500/30 sm:p-5"
        :class="{
            'border-[var(--status-red)]/50 shadow-[0_0_0_1px_var(--status-red)]/20':
                alert.level === 'red' && state !== 'acknowledged',
        }"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
            <span
                class="relative flex size-11 shrink-0 items-center justify-center rounded-xl"
                :class="ICON_CLASS[alert.level]"
            >
                <span
                    v-if="alert.kind === 'sos' && state !== 'acknowledged'"
                    class="absolute inset-0 animate-ping rounded-xl bg-[var(--status-red)] opacity-40 motion-reduce:hidden"
                />
                <component :is="kind.icon" class="relative size-5" />
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="font-semibold">{{ text(alert) }}</p>
                    <StatusBadge
                        v-if="alert.level !== 'info'"
                        :status="alert.level"
                    />
                    <span
                        class="rounded-full border px-2 py-0.5 text-xs text-muted-foreground"
                    >
                        {{ kind.label }}
                    </span>
                </div>
                <p class="mt-0.5 text-sm text-muted-foreground">
                    <span v-if="astronautName">{{ astronautName }} · </span
                    >{{ ago }} ·
                    <span
                        :class="{
                            'font-medium text-sky-600 dark:text-sky-400':
                                state === 'traveling',
                        }"
                    >
                        {{ stateLabel(state) }}
                        <template v-if="state === 'traveling'">{{
                            t('Alertas.item.arrivesIn', { time: label })
                        }}</template>
                    </span>
                    <template v-if="alert.source === 'device'">
                        {{ t('Alertas.item.bySensors') }}</template
                    >
                </p>

                <blockquote
                    v-if="alert.note"
                    class="mt-3 rounded-lg border-l-2 border-sky-500/60 bg-muted/40 px-3 py-2 text-sm"
                >
                    “{{ alert.note }}”
                </blockquote>

                <div class="mt-4 max-w-lg">
                    <AlertTimeline :alert="alert" :now="now" />
                </div>

                <div
                    v-if="alert.reply"
                    class="mt-4 flex gap-3 rounded-xl border border-sky-500/25 bg-sky-500/5 p-3 text-sm"
                >
                    <MessageCircleReply
                        class="mt-0.5 size-4 shrink-0 text-sky-500"
                    />
                    <div>
                        <p
                            class="text-xs font-medium text-sky-700 dark:text-sky-300"
                        >
                            {{ t('Alertas.item.earthReply') }}
                            <template v-if="replyIn > 0">
                                {{ t('Alertas.item.replyArrives') }}
                                <span class="tabular-nums">{{
                                    mmss(replyIn)
                                }}</span>
                            </template>
                        </p>
                        <p class="mt-0.5">{{ alert.reply }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2 sm:flex-col sm:items-end">
                <Button
                    v-if="canAttend && state === 'received'"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                    @click="$emit('attend')"
                >
                    <Stethoscope />
                    {{ t('Alertas.item.attend') }}
                </Button>
                <Button
                    v-if="recommendation"
                    variant="ghost"
                    size="sm"
                    :aria-expanded="open"
                    @click="open = !open"
                >
                    {{ t('Alertas.item.whatToDo') }}
                    <ChevronDown
                        class="transition-transform"
                        :class="{ 'rotate-180': open }"
                    />
                </Button>
            </div>
        </div>

        <Transition
            enter-active-class="transition duration-200 ease-out"
            enter-from-class="-translate-y-1 opacity-0"
            leave-active-class="transition duration-150"
            leave-to-class="opacity-0"
        >
            <div v-if="open && recommendation" class="mt-4 sm:ml-15">
                <RecommendationCard :recommendation="recommendation" />
            </div>
        </Transition>
    </li>
</template>
