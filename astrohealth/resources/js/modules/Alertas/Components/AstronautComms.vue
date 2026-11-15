<script setup lang="ts">
import { LifeBuoy, MessageSquareText, Siren } from '@lucide/vue';
import type { NewMessage } from '../Types/alerts';

/**
 * Los tres botones del astronauta para hablar con Tierra: SOS (emergencia),
 * pedir ayuda médica y contar una novedad. Cada uno abre su formulario.
 * `compact` = versión en una sola fila (para el Inicio).
 */
withDefaults(defineProps<{ compact?: boolean }>(), { compact: false });

defineEmits<{ choose: [kind: NewMessage['kind']] }>();
</script>

<template>
    <div class="grid gap-3" :class="compact ? 'grid-cols-3' : 'sm:grid-cols-3'">
        <button
            type="button"
            class="group relative flex items-center gap-3 overflow-hidden rounded-2xl border border-[var(--status-red)]/50 bg-[var(--status-red)] p-4 text-left text-white shadow-[0_10px_30px_-12px_var(--status-red)] transition hover:brightness-110 focus-visible:ring-4 focus-visible:ring-[var(--status-red)]/40 focus-visible:outline-none"
            :class="{
                'flex-col justify-center gap-1.5 p-3 text-center': compact,
            }"
            @click="$emit('choose', 'sos')"
        >
            <Siren class="size-6 shrink-0" />
            <span>
                <span
                    class="block text-lg leading-tight font-bold tracking-wide"
                    >SOS</span
                >
                <span v-if="!compact" class="block text-xs text-white/85"
                    >Emergencia: avisa a Tierra ya</span
                >
            </span>
        </button>

        <button
            type="button"
            class="flex items-center gap-3 rounded-2xl border border-[var(--status-yellow)]/40 bg-[var(--status-yellow)]/10 p-4 text-left transition hover:bg-[var(--status-yellow)]/15 focus-visible:ring-4 focus-visible:ring-[var(--status-yellow)]/30 focus-visible:outline-none"
            :class="{
                'flex-col justify-center gap-1.5 p-3 text-center': compact,
            }"
            @click="$emit('choose', 'help')"
        >
            <LifeBuoy
                class="size-6 shrink-0 text-amber-600 dark:text-amber-300"
            />
            <span>
                <span class="block leading-tight font-semibold"
                    >Pedir ayuda</span
                >
                <span
                    v-if="!compact"
                    class="block text-xs text-muted-foreground"
                    >Un síntoma, una lesión, el ánimo…</span
                >
            </span>
        </button>

        <button
            type="button"
            class="flex items-center gap-3 rounded-2xl border bg-card p-4 text-left transition hover:border-sky-500/40 focus-visible:ring-4 focus-visible:ring-sky-500/25 focus-visible:outline-none"
            :class="{
                'flex-col justify-center gap-1.5 p-3 text-center': compact,
            }"
            @click="$emit('choose', 'report')"
        >
            <MessageSquareText class="size-6 shrink-0 text-sky-500" />
            <span>
                <span class="block leading-tight font-semibold">Novedad</span>
                <span
                    v-if="!compact"
                    class="block text-xs text-muted-foreground"
                    >Cuéntale algo al médico</span
                >
            </span>
        </button>
    </div>
</template>
