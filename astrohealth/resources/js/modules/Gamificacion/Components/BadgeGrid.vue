<script setup lang="ts">
import { Lock } from '@lucide/vue';
import type { Badge } from '../Types/gamification';

/**
 * Las insignias: las ganadas a color con el día en que se ganaron; las
 * bloqueadas en gris, con cómo se ganan y cuánto falta.
 */
defineProps<{ badges: Badge[] }>();
</script>

<template>
    <ul class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
        <li
            v-for="badge in badges"
            :key="badge.code"
            class="flex items-center gap-4 rounded-2xl border p-4 transition"
            :class="
                badge.earned_day !== null
                    ? 'border-sky-500/30 bg-linear-to-br from-sky-500/10 to-card'
                    : 'bg-card'
            "
        >
            <span
                class="flex size-14 shrink-0 items-center justify-center rounded-2xl text-3xl"
                :class="
                    badge.earned_day !== null
                        ? 'bg-sky-500/15'
                        : 'bg-muted opacity-50 grayscale'
                "
                aria-hidden="true"
            >
                {{ badge.icon }}
            </span>
            <div class="min-w-0 flex-1">
                <p class="flex items-center gap-1.5 font-semibold">
                    {{ badge.name }}
                    <Lock
                        v-if="badge.earned_day === null"
                        class="size-3.5 text-muted-foreground"
                        aria-label="bloqueada"
                    />
                </p>
                <p
                    v-if="badge.earned_day !== null"
                    class="text-xs text-muted-foreground"
                >
                    Ganada el día {{ badge.earned_day }} · +{{ badge.bonus }}
                    pts
                </p>
                <template v-else>
                    <p class="text-xs text-muted-foreground">
                        {{ badge.condition }}
                    </p>
                    <div class="mt-2 flex items-center gap-2">
                        <div
                            class="h-1.5 flex-1 overflow-hidden rounded-full bg-muted"
                        >
                            <div
                                class="h-full rounded-full bg-sky-500"
                                :style="{
                                    width: `${(badge.progress.current / badge.progress.target) * 100}%`,
                                }"
                            />
                        </div>
                        <span
                            class="text-[11px] text-muted-foreground tabular-nums"
                        >
                            {{ badge.progress.current }}/{{
                                badge.progress.target
                            }}
                        </span>
                    </div>
                </template>
            </div>
        </li>
    </ul>
</template>
