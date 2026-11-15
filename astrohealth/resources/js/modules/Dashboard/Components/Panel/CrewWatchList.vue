<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import { ArrowRight, Users } from '@lucide/vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { getInitials } from '@/composables/useInitials';
import { tripulacion, tripulante } from '@/routes';
import type { WatchRow } from '../../Types/crewTrends';

const { t } = useI18n();

/**
 * Quién necesita atención: la tripulación ordenada por puntaje (el más bajo
 * arriba), con su estado y el resumen de M2.
 */
defineProps<{ rows: WatchRow[] }>();
</script>

<template>
    <section class="rounded-2xl border bg-card">
        <header
            class="flex items-center justify-between gap-3 border-b p-4 sm:px-6"
        >
            <h2 class="flex items-center gap-2 font-semibold">
                <Users class="size-4 text-sky-500" />
                {{ t('Dashboard.panel.whoNeedsAttention') }}
            </h2>
            <Link
                :href="tripulacion()"
                class="inline-flex items-center gap-1 text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
            >
                {{ t('Dashboard.panel.crew') }} <ArrowRight class="size-4" />
            </Link>
        </header>
        <ul class="divide-y">
            <li
                v-for="row in rows"
                :key="row.id"
                class="flex items-center gap-3 px-4 py-3 sm:px-6"
            >
                <span
                    class="flex size-9 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-xs font-semibold text-white"
                    aria-hidden="true"
                >
                    {{ getInitials(row.name) }}
                </span>
                <div class="min-w-0 flex-1">
                    <Link
                        :href="tripulante(row.id)"
                        class="block truncate text-sm font-semibold hover:text-sky-600 hover:underline dark:hover:text-sky-400"
                        >{{ row.name }}</Link
                    >
                    <p class="truncate text-xs text-muted-foreground">
                        {{ row.summary }}
                    </p>
                </div>
                <div class="flex shrink-0 flex-col items-end gap-1">
                    <span class="text-sm font-semibold tabular-nums">{{
                        row.score ?? '—'
                    }}</span>
                    <StatusBadge :status="row.status" />
                </div>
            </li>
        </ul>
    </section>
</template>
