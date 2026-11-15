<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { Medal, Trophy } from '@lucide/vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, logros } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import { useCrewStore } from '../Entorno/Stores/useCrewStore';
import { useMissionStore } from '../Entorno/Stores/useMissionStore';
import BadgeGrid from './Components/BadgeGrid.vue';
import Leaderboard from './Components/Leaderboard.vue';
import RecentPoints from './Components/RecentPoints.vue';
import StreakCounter from './Components/StreakCounter.vue';
import XpBar from './Components/XpBar.vue';
import { useGamificationStore } from './Stores/useGamificationStore';

/**
 * Logros (/logros, M7): nivel y racha → insignias → ranking de la
 * tripulación, y cómo se ganan los puntos. Cuidarse como un hábito.
 * ⚠️ Por ahora con datos de prueba (ver Stores/useGamificationStore.ts).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Logros', href: logros() }] },
});

const auth = useAuthStore();
const store = useGamificationStore();
const crew = useCrewStore();
const mission = useMissionStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);

const own = computed(() => auth.actor?.astronaut_id ?? null);
const today = computed(() => mission.current?.day ?? 1);
const earned = computed(
    () =>
        store.profile?.badges.filter((badge) => badge.earned_day !== null)
            .length ?? 0,
);

onMounted(async () => {
    if (!auth.can('gamification.read')) {
        router.visit(dashboard());

        return;
    }

    await Promise.all([mission.load(), crew.load(own.value)]);
    await Promise.all([
        own.value !== null
            ? store.load(own.value, today.value)
            : Promise.resolve(),
        store.loadLeaderboard(crew.members, today.value),
    ]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Logros" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header data-reveal>
            <p
                class="flex items-center gap-2 text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
            >
                <Trophy class="size-3.5" /> {{ crew.mission?.name ?? 'Misión' }}
            </p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">
                Logros
            </h1>
            <p class="mt-2 max-w-2xl text-muted-foreground">
                Cuidarte suma: registrar tus datos, dormir bien, hacer ejercicio
                y cumplir tu rutina te dan puntos, rachas e insignias.
            </p>
        </header>

        <template v-if="!ready">
            <Skeleton class="h-36 rounded-2xl" />
            <Skeleton class="h-72 rounded-2xl" />
        </template>

        <template v-else>
            <section
                v-if="store.profile"
                class="grid gap-6 rounded-2xl border bg-linear-to-br from-sky-500/10 via-card to-card p-6 sm:p-8 lg:grid-cols-[minmax(0,1.6fr)_auto_auto] lg:items-center"
                data-reveal
            >
                <XpBar
                    :points="store.profile.points"
                    :level="store.profile.level"
                    :progress="store.profile.level_progress"
                />
                <StreakCounter :days="store.profile.streak_days" />
                <div class="flex items-center gap-3">
                    <Medal class="size-9 text-sky-500" />
                    <div>
                        <p
                            class="text-2xl leading-none font-semibold tabular-nums"
                        >
                            {{ earned
                            }}<span
                                class="text-base font-normal text-muted-foreground"
                                >/{{ store.profile.badges.length }}</span
                            >
                        </p>
                        <p class="mt-1 text-xs text-muted-foreground">
                            insignias
                        </p>
                    </div>
                </div>
            </section>

            <section
                v-if="store.profile"
                class="flex flex-col gap-3"
                data-reveal
            >
                <h2 class="text-lg font-semibold">Insignias</h2>
                <BadgeGrid :badges="store.profile.badges" />
            </section>

            <div
                class="grid items-start gap-6 lg:grid-cols-[minmax(0,1.3fr)_minmax(0,1fr)]"
            >
                <section class="rounded-2xl border bg-card" data-reveal>
                    <h2 class="border-b p-4 font-semibold sm:px-6">
                        Ranking de la tripulación
                    </h2>
                    <Leaderboard :rows="store.leaderboard" :me="own" />
                </section>
                <section
                    v-if="store.profile"
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <h2 class="mb-3 font-semibold">Tus últimos puntos</h2>
                    <RecentPoints
                        :recent="store.profile.recent"
                        :today="today"
                    />
                </section>
            </div>
        </template>
    </div>
</template>
