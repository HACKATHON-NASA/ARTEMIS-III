<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ArrowRight } from '@lucide/vue';
import type { InertiaLinkProps } from '@inertiajs/vue3';
import StatusBadge from '@/components/StatusBadge.vue';
import { getInitials } from '@/composables/useInitials';
import { statusMeta } from '@/lib/status';
import type { MaybeStatus } from '@/types/health';

/**
 * Un tripulante: quién es, su puntaje y estado (de M2) y un acceso a su cuerpo.
 * Sin mediciones todavía, el puntaje sale vacío y en gris ("Sin datos").
 */
defineProps<{
    name: string;
    crewRole: string;
    score: number | null;
    status: MaybeStatus;
    summary: string;
    href: NonNullable<InertiaLinkProps['href']>;
}>();
</script>

<template>
    <article
        class="group relative flex flex-col gap-4 overflow-hidden rounded-2xl border bg-card p-5 transition duration-300 hover:-translate-y-0.5 hover:border-sky-500/40 hover:shadow-[0_12px_32px_-16px_rgb(14_165_233/0.45)]"
    >
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-1"
            :style="{ backgroundColor: statusMeta(status).color }"
        />
        <div class="flex items-center gap-3">
            <span
                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 font-semibold text-white"
                aria-hidden="true"
            >
                {{ getInitials(name) }}
            </span>
            <div class="min-w-0">
                <h2 class="truncate font-semibold">{{ name }}</h2>
                <p class="truncate text-sm text-muted-foreground">
                    {{ crewRole }}
                </p>
            </div>
        </div>

        <div class="flex items-end justify-between gap-3">
            <div>
                <p class="text-xs text-muted-foreground">Puntaje de salud</p>
                <p
                    v-if="score !== null"
                    class="text-3xl font-semibold tabular-nums"
                >
                    {{ score
                    }}<span class="text-base font-normal text-muted-foreground"
                        >/100</span
                    >
                </p>
                <p v-else class="text-3xl font-semibold text-muted-foreground">
                    —
                </p>
            </div>
            <StatusBadge :status="status" size="md" />
        </div>

        <p class="min-h-10 text-sm text-muted-foreground">{{ summary }}</p>

        <Link
            :href="href"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-sky-600 transition group-hover:gap-2.5 dark:text-sky-400"
        >
            Ver su cuerpo
            <ArrowRight class="size-4" />
        </Link>
    </article>
</template>
