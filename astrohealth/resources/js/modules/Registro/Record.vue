<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { dashboard, registrar } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { BodyZoneCode } from '@/types/health';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import BodyViewer from '../Cuerpo/Components/BodyViewer.vue';
import { useBodyStore } from '../Cuerpo/Stores/useBodyStore';
import { useMissionStore } from '../Entorno/Stores/useMissionStore';
import { useGamificationStore } from '../Gamificacion/Stores/useGamificationStore';
import FlyingValue from './Components/FlyingValue.vue';
import HowToMeasureDialog from './Components/HowToMeasureDialog.vue';
import MeasurementForm from './Components/MeasurementForm.vue';
import RecordSummary from './Components/RecordSummary.vue';
import { useBodyLink } from './Composables/useBodyLink';
import { useMeasurementsStore } from './Stores/useMeasurementsStore';
import type { Flight, Indicator, RecordPayload } from './Types/measurement';

/**
 * Registrar (/registrar, M1): el astronauta anota sus indicadores del día y
 * cada valor vuela a su órgano en el cuerpo (M3). Los amarillos y rojos
 * generan su alerta (M5). ⚠️ Por ahora con datos de prueba (ver los stores).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Registrar datos', href: registrar() }] },
});

const auth = useAuthStore();
const store = useMeasurementsStore();
const body = useBodyStore();
const alerts = useAlertsStore();
const mission = useMissionStore();
const gamification = useGamificationStore();

const root = ref<HTMLElement | null>(null);
const bodyHost = ref<HTMLElement | null>(null);
const form = ref<InstanceType<typeof MeasurementForm> | null>(null);
const { reveal } = useReveal(root);
const { focusZoneField, planFlights } = useBodyLink(root, bodyHost);

const ready = ref(false);
const saving = ref(false);
const zone = ref<BodyZoneCode | null>(null);
const flights = ref<Flight[]>([]);
const alertsCreated = ref(0);
const help = ref<{ open: boolean; indicator: Indicator | null }>({
    open: false,
    indicator: null,
});

const astronautId = computed(() => auth.actor?.astronaut_id ?? null);

/** El último valor de cada indicador (sale del cuerpo, M3). */
const latest = computed(() =>
    Object.fromEntries(
        Object.values(body.status ?? {})
            .flatMap((item) => item.values)
            .map((value) => [value.indicator, value]),
    ),
);

function openHelp(indicator: Indicator): void {
    help.value = { open: true, indicator };
}

/** Tocar un órgano lo ilumina y lleva al primer campo de esa zona. */
function selectZone(code: BodyZoneCode): void {
    zone.value = code;
    focusZoneField(code);
}

async function submit(payload: RecordPayload): Promise<void> {
    if (astronautId.value === null) {
        return;
    }

    saving.value = true;
    const saved = await store.record(astronautId.value, payload);
    flights.value = planFlights(saved);
    form.value?.reset();

    if (flights.value.length === 0) {
        await finish();
    }
}

/** Cuando los valores llegan al cuerpo: se repinta y salen las alertas. */
async function finish(): Promise<void> {
    if (astronautId.value === null) {
        return;
    }

    const [created] = await Promise.all([
        alerts.afterMeasurements(astronautId.value, store.lastRecorded),
        body.load(astronautId.value),
    ]);
    alertsCreated.value = created.length;
    await awardPoints();
    saving.value = false;
    zone.value = null;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

/** Lo que suma puntos al registrar (M7): el día completo, dormir 7 h y 90 min de ejercicio. */
async function awardPoints(): Promise<void> {
    await mission.load();
    const day = mission.current?.day ?? 1;
    const value = (code: string) =>
        store.lastRecorded.find((item) => item.indicator === code)?.value ?? 0;
    const items: Parameters<typeof gamification.awardMany>[1] = [];

    if (body.measuredToday.done >= body.measuredToday.total) {
        items.push(['daily_complete', `${day}`]);
    }

    if (value('sleep') >= 7) items.push(['sleep_7h', `${day}`]);
    if (value('exercise_min') >= 90) items.push(['exercise', `${day}`]);

    const { gained } = await gamification.awardMany(
        astronautId.value ?? 0,
        items,
        day,
    );

    if (gained > 0) {
        toast.success(`+${gained} puntos`, {
            description: 'Por cuidarte hoy.',
        });
    }
}

function again(): void {
    store.clearLast();
    alertsCreated.value = 0;
}

onMounted(async () => {
    if (!auth.can('measurements.write.own') || astronautId.value === null) {
        toast.error('El registro es para la tripulación.');
        router.visit(dashboard());

        return;
    }

    store.clearLast();
    await Promise.all([store.loadIndicators(), body.load(astronautId.value)]);
    ready.value = true;
    await nextTick();
    reveal();
});
</script>

<template>
    <Head title="Registrar datos" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            data-reveal
        >
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    Registro del día
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    ¿Cómo está tu cuerpo hoy?
                </h1>
                <p class="mt-2 max-w-2xl text-muted-foreground">
                    Llena lo que hayas medido; no hace falta todo. Al guardar,
                    cada valor viaja a su órgano y toma su color.
                </p>
            </div>
            <div
                v-if="ready"
                class="flex items-center gap-3 rounded-xl border bg-card px-4 py-3"
            >
                <span class="text-2xl font-semibold tabular-nums">
                    {{ body.measuredToday.done
                    }}<span class="text-base text-muted-foreground"
                        >/{{ body.measuredToday.total }}</span
                    >
                </span>
                <span class="text-xs leading-tight text-muted-foreground">
                    indicadores<br />medidos hoy
                </span>
            </div>
        </header>

        <div
            v-if="!ready"
            class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]"
        >
            <Skeleton class="h-[640px] rounded-2xl" />
            <Skeleton class="h-[560px] rounded-2xl" />
        </div>

        <div
            v-else
            class="grid grid-cols-1 items-start gap-6 xl:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)]"
        >
            <div data-reveal>
                <RecordSummary
                    v-if="store.lastRecorded.length && !saving"
                    :measurements="store.lastRecorded"
                    :indicators="store.indicators"
                    :alerts-created="alertsCreated"
                    @again="again"
                />
                <MeasurementForm
                    v-show="!store.lastRecorded.length || saving"
                    ref="form"
                    :indicators="store.indicators"
                    :latest="latest"
                    :saving="saving"
                    @submit="submit"
                    @focus-zone="zone = $event"
                    @help="openHelp"
                />
            </div>

            <aside
                class="relative overflow-hidden rounded-2xl border bg-card p-4 sm:p-6 xl:sticky xl:top-4"
                data-reveal
            >
                <div
                    class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_50%_40%,rgb(14_165_233/0.12),transparent_65%)]"
                />
                <div class="relative">
                    <h2 class="text-lg font-semibold">Tu cuerpo, en vivo</h2>
                    <p class="mb-4 text-sm text-muted-foreground">
                        Se ilumina el órgano del campo que estás llenando. Toca
                        un órgano para ir a sus campos.
                    </p>
                    <div ref="bodyHost">
                        <BodyViewer
                            v-if="body.status"
                            :parts="body.parts"
                            :status="body.status"
                            :selected="zone"
                            @select="selectZone"
                        />
                    </div>
                </div>
            </aside>
        </div>

        <FlyingValue
            v-if="flights.length"
            :flights="flights"
            @landed="finish"
            @done="flights = []"
        />
        <HowToMeasureDialog
            v-model:open="help.open"
            :indicator="help.indicator"
        />
    </div>
</template>
