<script setup lang="ts">
import {
    CheckCheck,
    ChevronDown,
    Send,
    Siren,
    TriangleAlert,
} from '@lucide/vue';
import { computed, ref, toRef } from 'vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import { useCountdown } from '../Composables/useCountdown';
import type { Alert, Recommendation } from '../Types/alerts';
import { STATE_LABELS, alertState } from '../Utils/alertState';
import AlertTimeline from './AlertTimeline.vue';
import RecommendationCard from './RecommendationCard.vue';

/**
 * Una alerta del centro: qué pasó, de quién, en qué paso va y qué se puede
 * hacer (enviar a Tierra o confirmar desde Tierra, según el rol).
 */
const props = defineProps<{
    alert: Alert;
    astronautName: string | null;
    recommendation: Recommendation | null;
    canSend: boolean;
    canAcknowledge: boolean;
    busy: boolean;
}>();

defineEmits<{ send: []; acknowledge: [] }>();

const open = ref(false);
const { label, now } = useCountdown(toRef(() => props.alert.ground_eta_at));
const state = computed(() => alertState(props.alert, now.value));

const ago = computed(() => {
    const minutes = Math.round(
        (now.value - new Date(props.alert.created_at).getTime()) / 60_000,
    );

    return minutes < 60
        ? `hace ${Math.max(1, minutes)} min`
        : minutes < 60 * 24
          ? `hace ${Math.round(minutes / 60)} h`
          : `hace ${Math.round(minutes / 1440)} d`;
});
</script>

<template>
    <li
        class="rounded-2xl border bg-card p-4 transition hover:border-sky-500/30 sm:p-5"
        :class="{
            'border-[var(--status-red)]/40':
                state === 'pending' && alert.level === 'red',
        }"
    >
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
            <span
                class="flex size-11 shrink-0 items-center justify-center rounded-xl"
                :class="
                    alert.level === 'red'
                        ? 'bg-[var(--status-red)] text-white'
                        : 'bg-[var(--status-yellow)]/15 text-amber-600 dark:text-amber-300'
                "
            >
                <Siren v-if="alert.level === 'red'" class="size-5" />
                <TriangleAlert v-else class="size-5" />
            </span>

            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <p class="font-semibold">{{ alert.message }}</p>
                    <StatusBadge :status="alert.level" />
                </div>
                <p class="mt-0.5 text-sm text-muted-foreground">
                    <span v-if="astronautName">{{ astronautName }} · </span
                    >{{ ago }} ·
                    <span
                        :class="{
                            'font-medium text-sky-600 dark:text-sky-400':
                                state === 'sent',
                        }"
                    >
                        {{ STATE_LABELS[state]
                        }}<template v-if="state === 'sent'">
                            (llega en
                            <span class="tabular-nums">{{ label }}</span
                            >)</template
                        >
                    </span>
                </p>
                <div class="mt-4 max-w-md">
                    <AlertTimeline :alert="alert" :now="now" />
                </div>
            </div>

            <div class="flex flex-wrap gap-2 sm:flex-col sm:items-end">
                <Button
                    v-if="canSend && state === 'pending'"
                    :disabled="busy"
                    class="bg-[var(--status-red)] text-white hover:bg-[var(--status-red)]/90"
                    @click="$emit('send')"
                >
                    <Spinner v-if="busy" />
                    <Send v-else />
                    Enviar a Tierra
                </Button>
                <Button
                    v-if="
                        canAcknowledge &&
                        (state === 'sent' || state === 'received')
                    "
                    :disabled="busy"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                    @click="$emit('acknowledge')"
                >
                    <Spinner v-if="busy" />
                    <CheckCheck v-else />
                    Confirmar recibida
                </Button>
                <Button
                    v-if="recommendation"
                    variant="ghost"
                    size="sm"
                    :aria-expanded="open"
                    @click="open = !open"
                >
                    Qué hacer
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
