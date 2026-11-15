<script setup lang="ts">
import { CheckCheck, Clock, Inbox } from '@lucide/vue';
import type { Advice } from '../Types/advice';
import { ADVICE_CATEGORIES } from '../Utils/adviceMock';

/**
 * Las indicaciones que se le han dado a un tripulante (ficha del médico):
 * el tema, los pasos y en qué va: viajando a la nave, recibida o entendida.
 */
defineProps<{ items: Advice[]; now: number }>();

function mmss(ms: number): string {
    const total = Math.ceil(ms / 1000);

    return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
}

function sentAgo(iso: string, now: number): string {
    const minutes = Math.round((now - new Date(iso).getTime()) / 60_000);

    return minutes < 60
        ? `hace ${Math.max(1, minutes)} min`
        : minutes < 1440
          ? `hace ${Math.round(minutes / 60)} h`
          : `hace ${Math.round(minutes / 1440)} d`;
}
</script>

<template>
    <ul v-if="items.length" class="flex flex-col gap-3">
        <li
            v-for="item in items"
            :key="item.id"
            class="rounded-xl border p-3"
            :class="{ 'border-[var(--status-red)]/40': item.important }"
        >
            <div class="flex items-start gap-3">
                <span
                    class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-sky-500/12 text-sky-600 dark:text-sky-300"
                >
                    <component
                        :is="ADVICE_CATEGORIES[item.category].icon"
                        class="size-4"
                    />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold">
                        {{ item.title }}
                        <span
                            v-if="item.important"
                            class="ml-1 rounded-full bg-[var(--status-red)]/15 px-1.5 py-0.5 text-[10px] font-medium text-red-600 dark:text-red-300"
                            >Importante</span
                        >
                    </p>
                    <p class="text-xs text-muted-foreground">
                        {{ ADVICE_CATEGORIES[item.category].label }} ·
                        {{ item.author }} · {{ sentAgo(item.sent_at, now) }}
                        <template v-if="item.valid_days">
                            · por {{ item.valid_days }} días</template
                        >
                    </p>
                    <ol
                        class="mt-2 list-decimal space-y-0.5 pl-4 text-sm text-muted-foreground"
                    >
                        <li v-for="step in item.body.split('\n')" :key="step">
                            {{ step }}
                        </li>
                    </ol>
                    <p
                        class="mt-2 flex items-center gap-1.5 text-xs font-medium"
                    >
                        <template v-if="item.read_at">
                            <CheckCheck
                                class="size-3.5 text-[var(--status-green)]"
                            />
                            Entendida por el tripulante
                        </template>
                        <template
                            v-else-if="
                                new Date(item.arrives_at).getTime() > now
                            "
                        >
                            <Clock class="size-3.5 text-sky-500" />
                            Viajando a la nave · llega en
                            <span class="tabular-nums">{{
                                mmss(new Date(item.arrives_at).getTime() - now)
                            }}</span>
                        </template>
                        <template v-else>
                            <Inbox class="size-3.5 text-sky-500" /> En la nave,
                            sin leer todavía
                        </template>
                    </p>
                </div>
            </div>
        </li>
    </ul>
    <p
        v-else
        class="rounded-xl border border-dashed p-6 text-center text-sm text-muted-foreground"
    >
        Aún no le has dado indicaciones.
    </p>
</template>
