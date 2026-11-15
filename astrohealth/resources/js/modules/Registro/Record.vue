<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { useReveal } from '@/composables/useReveal';
import { ApiError } from '@/lib/api';
import { dashboard, registrar } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { BodyZoneCode } from '@/types/health';
import { useAlertsStore } from '../Alertas/Stores/useAlertsStore';
import BodyViewer from '../Cuerpo/Components/BodyViewer.vue';
import { useBodyStore } from '../Cuerpo/Stores/useBodyStore';
import { useGamificationStore } from '../Gamificacion/Stores/useGamificationStore';
import FlyingValue from './Components/FlyingValue.vue';
import HowToMeasureDialog from './Components/HowToMeasureDialog.vue';
import MeasurementForm from './Components/MeasurementForm.vue';
import RecordSummary from './Components/RecordSummary.vue';
import { useBodyLink } from './Composables/useBodyLink';
import { useMeasurementsStore } from './Stores/useMeasurementsStore';
import type {
    Flight,
    Indicator,
    Measurement,
    RecordPayload,
} from './Types/measurement';

const { t } = useI18n();

/**
 * Registrar (/registrar, M1): el astronauta anota sus indicadores del día y
 * cada valor vuela a su órgano en el cuerpo (M3). Los amarillos y rojos
 * generan su alerta (M5). ⚠️ Por ahora con datos de prueba (ver los stores).
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'nav.record', href: registrar() }] },
});

const auth = useAuthStore();
const store = useMeasurementsStore();
const body = useBodyStore();
const alerts = useAlertsStore();
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
    let saved: Measurement[];

    try {
        saved = await store.record(astronautId.value, payload);
    } catch (error) {
        saving.value = false;
        // El backend tiene la última palabra (rango posible, fecha, permisos).
        toast.error(t('Registro.notSaved'), {
            description:
                error instanceof ApiError
                    ? (Object.values(error.errors)[0]?.[0] ?? error.message)
                    : t('Registro.checkConnection'),
        });

        return;
    }

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

/**
 * Los puntos del registro (día completo, sueño, ejercicio, racha e
 * insignias) los suma M7 en el backend al guardar; aquí solo se celebran.
 */
async function awardPoints(): Promise<void> {
    const { gained } = await gamification.refresh(astronautId.value ?? 0);

    if (gained > 0) {
        toast.success(t('Registro.points', { n: gained }), {
            description: t('Registro.pointsHint'),
        });
    }
}

function again(): void {
    store.clearLast();
    alertsCreated.value = 0;
}

onMounted(async () => {
    if (!auth.can('measurements.write.own') || astronautId.value === null) {
        toast.error(t('Registro.crewOnly'));
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
    <Head :title="t('Registro.pageTitle')" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <header
            class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"
            data-reveal
        >
            <div>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    {{ t('Registro.eyebrow') }}
                </p>
                <h1
                    class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl"
                >
                    {{ t('Registro.title') }}
                </h1>
                <p class="mt-2 max-w-2xl text-muted-foreground">
                    {{ t('Registro.subtitle') }}
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
                    {{ t('Registro.measuredToday') }}
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
                    <h2 class="text-lg font-semibold">
                        {{ t('Registro.liveBody') }}
                    </h2>
                    <p class="mb-4 text-sm text-muted-foreground">
                        {{ t('Registro.liveBodyHint') }}
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
