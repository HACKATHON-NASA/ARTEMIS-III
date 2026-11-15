<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { agoFromMinutes } from '@/lib/i18n';
import { Link } from '@inertiajs/vue3';
import type { InertiaLinkProps } from '@inertiajs/vue3';
import { CheckCheck, Clock, Inbox } from '@lucide/vue';
import type { Advice } from '../Types/advice';
import { ADVICE_CATEGORIES } from '../Utils/adviceCategories';

const { t } = useI18n();

/**
 * Las indicaciones que se le han dado a un tripulante (ficha del médico):
 * el tema, los pasos y en qué va: viajando a la nave, recibida o entendida.
 */
withDefaults(
    defineProps<{
        items: Advice[];
        now: number;
        /** En el historial por misión: a quién y el enlace a su ficha. */
        nameOf?: ((astronautId: number) => string) | null;
        hrefOf?:
            | ((astronautId: number) => NonNullable<InertiaLinkProps['href']>)
            | null;
        empty?: string;
    }>(),
    { nameOf: null, hrefOf: null, empty: '' },
);

function mmss(ms: number): string {
    const total = Math.ceil(ms / 1000);

    return `${String(Math.floor(total / 60)).padStart(2, '0')}:${String(total % 60).padStart(2, '0')}`;
}

function sentAgo(iso: string, now: number): string {
    return agoFromMinutes(
        Math.max(1, (now - new Date(iso).getTime()) / 60_000),
    );
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
                    <Link
                        v-if="nameOf && hrefOf"
                        :href="hrefOf(item.astronaut_id)"
                        class="text-xs font-semibold tracking-wide text-sky-600 uppercase hover:underline dark:text-sky-400"
                        >{{
                            t('Alertas.list.for', {
                                name: nameOf(item.astronaut_id),
                            })
                        }}</Link
                    >
                    <p class="text-sm font-semibold">
                        {{ item.title }}
                        <span
                            v-if="item.important"
                            class="ml-1 rounded-full bg-[var(--status-red)]/15 px-1.5 py-0.5 text-[10px] font-medium text-red-600 dark:text-red-300"
                            >{{ t('Alertas.list.important') }}</span
                        >
                    </p>
                    <p class="text-xs text-muted-foreground">
                        {{ ADVICE_CATEGORIES[item.category].label }} ·
                        {{ item.author }} · {{ sentAgo(item.sent_at, now) }}
                        <template v-if="item.valid_days">
                            {{
                                t(
                                    'Alertas.inbox.forDays',
                                    { n: item.valid_days },
                                    item.valid_days,
                                )
                            }}</template
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
                            {{ t('Alertas.list.understood') }}
                        </template>
                        <template
                            v-else-if="
                                new Date(item.arrives_at).getTime() > now
                            "
                        >
                            <Clock class="size-3.5 text-sky-500" />
                            {{ t('Alertas.list.traveling') }}
                            <span class="tabular-nums">{{
                                mmss(new Date(item.arrives_at).getTime() - now)
                            }}</span>
                        </template>
                        <template v-else>
                            <Inbox class="size-3.5 text-sky-500" />
                            {{ t('Alertas.list.unread') }}
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
        {{ empty || t('Alertas.list.empty') }}
    </p>
</template>
