<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { crewRoleLabel } from '../../Entorno/Utils/environments';
import { Link } from '@inertiajs/vue3';
import type { InertiaLinkProps } from '@inertiajs/vue3';
import StatusBadge from '@/components/StatusBadge.vue';
import { getInitials } from '@/composables/useInitials';
import { statusMeta } from '@/lib/status';
import type { BodyZoneCode, MaybeStatus } from '@/types/health';
import type { BodyPart, BodyStatus } from '../Types/body';
import BodyViewer from './BodyViewer.vue';

const { t } = useI18n();

/**
 * Un tripulante con su cuerpo (el esqueleto y los órganos pintados por
 * estado). Tocar un órgano avisa a la pantalla, que abre su ventana.
 */
defineProps<{
    name: string;
    crewRole: string;
    score: number | null;
    status: MaybeStatus;
    parts: BodyPart[];
    body: BodyStatus;
    selected: BodyZoneCode | null;
    /** La ficha del tripulante. */
    href: NonNullable<InertiaLinkProps['href']>;
}>();

defineEmits<{ select: [code: BodyZoneCode] }>();
</script>

<template>
    <article
        class="relative flex flex-col overflow-hidden rounded-2xl border bg-card transition hover:border-sky-500/40"
    >
        <div
            class="pointer-events-none absolute inset-x-0 top-0 h-1"
            :style="{ backgroundColor: statusMeta(status).color }"
        />
        <div class="flex flex-col gap-3 p-4 pb-0">
            <div class="flex items-center gap-3">
                <span
                    class="flex size-10 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-sm font-semibold text-white"
                    aria-hidden="true"
                >
                    {{ getInitials(name) }}
                </span>
                <div class="min-w-0">
                    <h2 class="truncate font-semibold">
                        <Link
                            :href="href"
                            class="hover:text-sky-600 hover:underline dark:hover:text-sky-400"
                            >{{ name }}</Link
                        >
                    </h2>
                    <p class="truncate text-xs text-muted-foreground">
                        {{ crewRoleLabel(crewRole) }}
                    </p>
                </div>
            </div>
            <div class="flex items-center justify-between gap-2">
                <p class="text-2xl leading-none font-semibold tabular-nums">
                    {{ score ?? '—'
                    }}<span class="text-xs font-normal text-muted-foreground">{{
                        score === null ? t('Cuerpo.card.noData') : '/100'
                    }}</span>
                </p>
                <StatusBadge :status="status" />
            </div>
        </div>

        <BodyViewer
            compact
            :parts="parts"
            :status="body"
            :selected="selected"
            @select="$emit('select', $event)"
        />
        <div class="flex items-center justify-between gap-2 px-4 pb-4 text-xs">
            <span class="text-muted-foreground">{{
                t('Cuerpo.card.tapOrgan')
            }}</span>
            <Link
                :href="href"
                class="font-medium text-sky-600 hover:underline dark:text-sky-400"
                >{{ t('Cuerpo.card.seeProfile') }}</Link
            >
        </div>
    </article>
</template>
