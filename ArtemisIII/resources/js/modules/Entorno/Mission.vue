<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { CalendarDays, Eye, Repeat } from '@lucide/vue';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { Skeleton } from '@/components/ui/skeleton';
import { getInitials } from '@/composables/useInitials';
import { ApiError } from '@/lib/api';
import { useReveal } from '@/composables/useReveal';
import { dashboard, mision } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import EnvironmentArt from './Components/EnvironmentArt.vue';
import EnvironmentPicker from './Components/EnvironmentPicker.vue';
import EnvironmentStats from './Components/EnvironmentStats.vue';
import RiskList from './Components/RiskList.vue';
import { useCrewStore } from './Stores/useCrewStore';
import { useMissionStore } from './Stores/useMissionStore';
import type { EnvironmentCode } from './Types/mission';
import { ENVIRONMENT_FACTS } from './Utils/environmentFacts';
import { environmentInfo } from './Utils/environments';
import { formatDate } from './Utils/missionDates';

/**
 * Mi misión (/mision, M6): dónde está la nave y qué significa para el
 * cuerpo (gravedad, retraso con Tierra, radiación y riesgos), el día de
 * misión y la tripulación. Se pueden explorar los otros entornos; quien
 * tiene mission.update puede cambiar la misión a otro.
 * ⚠️ Por ahora con datos de prueba (ver Stores/useMissionStore.ts).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Mi misión', href: mision() }] },
});

const auth = useAuthStore();
const mission = useMissionStore();
const crew = useCrewStore();

const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);
const ready = ref(false);
const preview = ref<EnvironmentCode | null>(null);
const confirming = ref(false);
const changing = ref(false);

const shown = computed<EnvironmentCode>(
    () => preview.value ?? mission.current?.environment ?? 'mars',
);
const isPreview = computed(() => shown.value !== mission.current?.environment);
const info = computed(() => environmentInfo(shown.value));
const facts = computed(() => ENVIRONMENT_FACTS[shown.value]);
const progress = computed(() =>
    mission.current
        ? Math.round((mission.current.day / mission.current.day_count) * 100)
        : 0,
);

async function change(): Promise<void> {
    if (!confirming.value) {
        confirming.value = true;

        return;
    }

    changing.value = true;

    try {
        await mission.changeEnvironment(shown.value);
        preview.value = null;
        toast.success(`La misión ahora está: ${facts.value.label}`, {
            description:
                'Cambian la gravedad, el retraso con Tierra y la radiación de referencia.',
        });
    } catch (error) {
        toast.error(
            error instanceof ApiError
                ? error.message
                : 'No se pudo cambiar el entorno.',
        );
    } finally {
        changing.value = false;
        confirming.value = false;
    }
}

onMounted(async () => {
    if (!auth.can('crew.read')) {
        router.visit(dashboard());

        return;
    }

    await Promise.all([
        mission.load(),
        crew.load(auth.actor?.astronaut_id ?? null),
    ]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Mi misión" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <template v-if="!ready || !mission.current">
            <Skeleton class="h-72 rounded-2xl" />
            <div class="grid gap-4 md:grid-cols-3">
                <Skeleton v-for="n in 3" :key="n" class="h-40 rounded-2xl" />
            </div>
        </template>

        <template v-else>
            <section
                class="relative grid grid-cols-1 overflow-hidden rounded-2xl border bg-linear-to-br to-card p-6 sm:p-8 md:grid-cols-[minmax(0,1fr)_16rem] md:items-center"
                :class="info.tint"
                data-reveal
            >
                <div class="relative z-10">
                    <p
                        class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                    >
                        {{ isPreview ? 'Vista previa' : 'Tu misión' }}
                    </p>
                    <h1
                        class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                    >
                        {{ mission.current.name }} ·
                        {{
                            isPreview
                                ? info.name
                                : mission.current.environment_label
                        }}
                    </h1>
                    <p
                        v-if="crew.mission?.objective"
                        class="mt-2 max-w-xl text-muted-foreground"
                    >
                        {{ crew.mission.objective }}
                    </p>
                    <div class="mt-5 max-w-md">
                        <div class="mb-1.5 flex justify-between text-sm">
                            <span class="font-medium"
                                >Día {{ mission.current.day }} de
                                {{ mission.current.day_count }}</span
                            >
                            <span class="text-muted-foreground tabular-nums"
                                >{{ progress }} %</span
                            >
                        </div>
                        <div
                            class="h-2 overflow-hidden rounded-full bg-background/60"
                        >
                            <div
                                class="h-full rounded-full bg-linear-to-r from-sky-400 to-blue-600"
                                :style="{ width: `${progress}%` }"
                            />
                        </div>
                        <p
                            v-if="crew.mission"
                            class="mt-2 flex items-center gap-1.5 text-xs text-muted-foreground"
                        >
                            <CalendarDays class="size-3.5" />
                            {{ formatDate(crew.mission.start_date) }} →
                            {{ formatDate(crew.mission.end_date) }}
                        </p>
                    </div>
                </div>
                <div class="mx-auto mt-6 size-56 md:mt-0">
                    <EnvironmentArt :environment="shown" />
                </div>
            </section>

            <section class="flex flex-col gap-3" data-reveal>
                <h2 class="text-lg font-semibold">
                    Lo que cambia para tu cuerpo
                </h2>
                <EnvironmentStats :info="info" :facts="facts" />
            </section>

            <section class="flex flex-col gap-3" data-reveal>
                <h2 class="text-lg font-semibold">
                    Riesgos del entorno · {{ info.name }}
                </h2>
                <RiskList :risks="facts.risks" />
            </section>

            <div
                class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)]"
            >
                <section
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <h2 class="mb-3 font-semibold">
                        Tripulación · {{ crew.members.length }}
                    </h2>
                    <ul class="flex flex-col gap-3">
                        <li
                            v-for="member in crew.members"
                            :key="member.id"
                            class="flex items-center gap-3"
                        >
                            <span
                                class="flex size-9 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-xs font-semibold text-white"
                                aria-hidden="true"
                            >
                                {{ getInitials(member.name) }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold">
                                    {{ member.name }}
                                    <span
                                        v-if="
                                            member.id ===
                                            auth.actor?.astronaut_id
                                        "
                                        class="text-xs font-normal text-sky-600 dark:text-sky-400"
                                        >(tú)</span
                                    >
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ member.crew_role }}
                                </p>
                            </div>
                        </li>
                    </ul>
                </section>

                <section
                    class="rounded-2xl border bg-card p-4 sm:p-6"
                    data-reveal
                >
                    <h2 class="flex items-center gap-2 font-semibold">
                        <Eye class="size-4 text-sky-500" /> Explorar entornos
                    </h2>
                    <p
                        id="explore-env"
                        class="mb-3 text-sm text-muted-foreground"
                    >
                        ¿Cómo sería tu misión en otro lugar? Toca uno para
                        comparar.
                    </p>
                    <EnvironmentPicker
                        :model-value="shown"
                        labelledby="explore-env"
                        @update:model-value="
                            preview = $event;
                            confirming = false;
                        "
                    />
                    <div
                        v-if="isPreview"
                        class="mt-4 flex flex-wrap items-center gap-2"
                    >
                        <Button
                            variant="outline"
                            size="sm"
                            @click="
                                preview = null;
                                confirming = false;
                            "
                            >Volver a mi misión</Button
                        >
                        <Button
                            v-if="auth.can('mission.update')"
                            size="sm"
                            :disabled="changing"
                            class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                            @click="change"
                        >
                            <Repeat />
                            {{
                                confirming
                                    ? `¿Seguro? Cambiar a ${info.name}`
                                    : 'Cambiar la misión a este entorno'
                            }}
                        </Button>
                    </div>
                </section>
            </div>
        </template>
    </div>
</template>
