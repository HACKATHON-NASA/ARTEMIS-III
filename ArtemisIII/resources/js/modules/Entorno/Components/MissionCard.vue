<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import { Activity, CalendarDays, UserPlus } from '@lucide/vue';
import { computed } from 'vue';
import { getInitials } from '@/composables/useInitials';
import { salud, usuarios } from '@/routes';
import type { MissionView } from '../Types/missions';
import { crewRoleLabel, environmentInfo } from '../Utils/environments';
import { statusLabel, daysUntil, formatDate } from '../Utils/missionDates';

const { t } = useI18n();

/**
 * Una misión: dónde, cuándo, en qué día va y quiénes la tripulan. La en
 * La misión actual lleva al Panel de salud; sin tripulantes, invita a
 * asignarlos.
 */
const props = defineProps<{ mission: MissionView; current: boolean }>();

const env = computed(() => environmentInfo(props.mission.environment));
const progress = computed(() =>
    props.mission.current_day === null
        ? props.mission.status === 'finished'
            ? 100
            : 0
        : Math.round(
              (props.mission.current_day / props.mission.day_count) * 100,
          ),
);
const shownCrew = computed(() => props.mission.crew.slice(0, 4));

const STATUS_CLASS = {
    active: 'bg-sky-500/15 text-sky-700 dark:text-sky-300 border-sky-500/30',
    planned:
        'bg-indigo-500/12 text-indigo-700 dark:text-indigo-300 border-indigo-500/30',
    finished: 'bg-muted text-muted-foreground border-border',
} as const;
</script>

<template>
    <article
        class="flex flex-col overflow-hidden rounded-2xl border bg-card transition hover:border-sky-500/30 hover:shadow-[0_12px_32px_-18px_rgb(14_165_233/0.5)]"
    >
        <div
            class="relative bg-linear-to-br to-transparent px-5 pt-5 pb-4"
            :class="env.tint"
        >
            <div class="flex items-start justify-between gap-3">
                <span
                    class="flex size-11 items-center justify-center rounded-xl border bg-background/70 backdrop-blur"
                >
                    <component :is="env.icon" class="size-5" />
                </span>
                <div class="flex flex-wrap justify-end gap-1.5">
                    <span
                        v-if="current"
                        class="rounded-full border border-sky-500/30 bg-background/70 px-2.5 py-0.5 text-xs font-medium"
                    >
                        {{ t('Entorno.card.current') }}
                    </span>
                    <span
                        class="rounded-full border px-2.5 py-0.5 text-xs font-medium"
                        :class="STATUS_CLASS[mission.status]"
                    >
                        {{ statusLabel(mission.status) }}
                    </span>
                </div>
            </div>
            <h3 class="mt-3 text-xl font-semibold tracking-tight">
                {{ mission.name }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{
                    t('Entorno.envLine', {
                        name: env.name,
                        gravity: env.gravity,
                        delay: env.delay,
                    })
                }}
            </p>
        </div>

        <div class="flex flex-1 flex-col gap-4 px-5 pb-5">
            <p
                v-if="mission.objective"
                class="line-clamp-2 text-sm text-muted-foreground"
            >
                {{ mission.objective }}
            </p>

            <div>
                <div class="mb-1.5 flex justify-between text-xs">
                    <span class="font-medium">
                        <template v-if="mission.status === 'active'">
                            {{
                                t('Entorno.dayOf', {
                                    day: mission.current_day,
                                    total: mission.day_count,
                                })
                            }}
                        </template>
                        <template v-else-if="mission.status === 'planned'">
                            {{
                                t(
                                    'Entorno.startsIn',
                                    { n: daysUntil(mission.start_date) },
                                    daysUntil(mission.start_date),
                                )
                            }}
                        </template>
                        <template v-else>
                            {{
                                t('Entorno.card.completed', {
                                    n: mission.day_count,
                                })
                            }}
                        </template>
                    </span>
                    <span class="text-muted-foreground tabular-nums">
                        {{ progress }} %
                    </span>
                </div>
                <div class="h-1.5 overflow-hidden rounded-full bg-muted">
                    <div
                        class="h-full rounded-full bg-linear-to-r from-sky-400 to-blue-600 transition-[width] duration-700"
                        :class="{ 'opacity-40': mission.status === 'finished' }"
                        :style="{ width: `${progress}%` }"
                    />
                </div>
                <p
                    class="mt-2 flex items-center gap-1.5 text-xs text-muted-foreground"
                >
                    <CalendarDays class="size-3.5" />
                    {{ formatDate(mission.start_date) }} →
                    {{ formatDate(mission.end_date) }}
                </p>
            </div>

            <div class="mt-auto border-t pt-4">
                <p class="mb-2 text-xs font-medium text-muted-foreground">
                    {{ t('Entorno.card.crew', { n: mission.crew.length }) }}
                </p>
                <ul v-if="mission.crew.length" class="flex flex-col gap-2">
                    <li
                        v-for="member in shownCrew"
                        :key="member.id"
                        class="flex items-center gap-2.5 text-sm"
                    >
                        <span
                            class="flex size-7 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-[11px] font-semibold text-white"
                            aria-hidden="true"
                        >
                            {{ getInitials(member.name) }}
                        </span>
                        <span class="min-w-0 truncate">{{ member.name }}</span>
                        <span
                            class="ml-auto truncate text-xs text-muted-foreground"
                        >
                            {{ crewRoleLabel(member.crew_role) }}
                        </span>
                    </li>
                    <li
                        v-if="mission.crew.length > shownCrew.length"
                        class="text-xs text-muted-foreground"
                    >
                        {{
                            t('Entorno.card.more', {
                                n: mission.crew.length - shownCrew.length,
                            })
                        }}
                    </li>
                </ul>
                <Link
                    v-else-if="mission.status !== 'finished'"
                    :href="usuarios()"
                    class="flex items-center gap-2 rounded-lg border border-dashed px-3 py-2.5 text-sm text-muted-foreground transition hover:border-sky-500/50 hover:text-foreground"
                >
                    <UserPlus class="size-4" />
                    {{ t('Entorno.card.noCrewCreate') }}
                </Link>
                <p v-else class="text-sm text-muted-foreground">
                    {{ t('Entorno.card.noCrew') }}
                </p>

                <Link
                    v-if="current && mission.crew.length"
                    :href="salud()"
                    class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-sky-600 hover:underline dark:text-sky-400"
                >
                    <Activity class="size-4" />
                    {{ t('Entorno.card.seeHealth') }}
                </Link>
            </div>
        </div>
    </article>
</template>
