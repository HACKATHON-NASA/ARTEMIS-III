import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { IndicatorCode, Status } from '@/types/health';
import type { Alert, NewMessage, Recommendation } from '../Types/alerts';
import { readSpeed, writeSpeed } from '../Utils/alertsMockDb';
import {
    mockAcknowledge,
    mockAlerts,
    mockAlertsFromMeasurements,
    mockPendingTotal,
    mockSendMessage,
    mockSimulatorTick,
} from '../Utils/alertsMockData';
import { MOCK_RECOMMENDATIONS } from '../Utils/recommendationsMockData';

/**
 * Las alertas (M5): las de un astronauta (`alerts`, para su Inicio y el Panel
 * de salud), el historial del centro (`history`), el contador del menú y las
 * recomendaciones. Todas salen hacia Tierra solas; el astronauta además
 * manda SOS, pide ayuda o cuenta una novedad, y Tierra responde.
 *
 * `refresh()` vuelve a pedir todo (lo llama useLiveAlerts cada pocos
 * segundos) y devuelve las alertas NUEVAS para avisar.
 *
 * ⚠️ HOY con datos de prueba (Utils/alertsMockData.ts). Con el backend:
 * GET /api/v1/alerts?since=, POST /api/v1/alerts y
 * PATCH /api/v1/alerts/{id}/acknowledge (o un canal en tiempo real).
 */
const wait = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

export const useAlertsStore = defineStore('alerts', () => {
    const alerts = ref<Alert[]>([]);
    const history = ref<Alert[]>([]);
    const recommendations = ref<Recommendation[]>(MOCK_RECOMMENDATIONS);
    const pendingTotal = ref(0);
    /** Velocidad de la demo (1 = real, 60 = el retraso de Marte en 20 s). */
    const speed = ref(readSpeed());

    let alertsScope: number | null = null;
    let historyScope: number | null | undefined;
    let pendingScope: number | null = null;
    const seen = new Set<number>();
    let primed = false;

    /** La alerta roja más reciente de `alerts` que Tierra aún no atendió (SOS incluido). */
    const active = computed(
        () =>
            alerts.value.find(
                (alert) =>
                    alert.level === 'red' && alert.acknowledged_at === null,
            ) ?? null,
    );

    async function load(astronautId: number): Promise<void> {
        alertsScope = astronautId;
        await wait(250);
        alerts.value = mockAlerts(astronautId);
    }

    /** El historial: de un astronauta o, con `null`, de toda la tripulación. */
    async function loadHistory(astronautId: number | null): Promise<void> {
        historyScope = astronautId;
        await wait(250);
        history.value = mockAlerts(astronautId);
    }

    async function loadPendingTotal(astronautId: number | null): Promise<void> {
        pendingScope = astronautId;
        await wait(150);
        pendingTotal.value = mockPendingTotal(astronautId);
    }

    /** Vuelve a pedir lo cargado. Devuelve las alertas que no se habían visto. */
    function refresh(): Alert[] {
        mockSimulatorTick();
        const all = mockAlerts(pendingScope);
        const fresh = primed ? all.filter((alert) => !seen.has(alert.id)) : [];
        all.forEach((alert) => seen.add(alert.id));
        primed = true;

        pendingTotal.value = mockPendingTotal(pendingScope);

        if (alertsScope !== null) {
            alerts.value = mockAlerts(alertsScope);
        }

        if (historyScope !== undefined) {
            history.value = mockAlerts(historyScope);
        }

        return fresh;
    }

    function recommendationFor(
        indicator: IndicatorCode | null,
        status: Status | 'info' | null,
    ): Recommendation | null {
        return (
            recommendations.value.find(
                (item) =>
                    item.indicator === indicator && item.status === status,
            ) ?? null
        );
    }

    /** Una recomendación por cada valor en amarillo o rojo, sin repetir título. */
    function recommendationsFor(
        values: { indicator: IndicatorCode; status: Status | null }[],
    ): Recommendation[] {
        const found = values
            .map((value) => recommendationFor(value.indicator, value.status))
            .filter((item) => item !== null);

        return found.filter(
            (item, index) =>
                found.findIndex((other) => other.title === item.title) ===
                index,
        );
    }

    /** El astronauta manda un SOS, pide ayuda o cuenta una novedad. */
    async function send(
        astronautId: number,
        message: NewMessage,
    ): Promise<Alert> {
        await wait(400);
        const alert = mockSendMessage(astronautId, message);
        seen.add(alert.id);
        refresh();

        return alert;
    }

    /** Tierra atiende la alerta y responde (la respuesta tarda en volver). */
    async function acknowledge(
        id: number,
        reply: string,
    ): Promise<Alert | null> {
        await wait(300);
        const alert = mockAcknowledge(id, reply);
        refresh();

        return alert;
    }

    /** Tras registrar mediciones (M1): las amarillas y rojas generan su alerta. */
    async function afterMeasurements(
        astronautId: number,
        measurements: Parameters<typeof mockAlertsFromMeasurements>[1],
    ): Promise<Alert[]> {
        const created = mockAlertsFromMeasurements(astronautId, measurements);
        created.forEach((alert) => seen.add(alert.id));
        await load(astronautId);
        refresh();

        return created;
    }

    function setSpeed(value: number): void {
        speed.value = value;
        writeSpeed(value);
    }

    return {
        alerts,
        history,
        recommendations,
        pendingTotal,
        active,
        speed,
        load,
        loadHistory,
        loadPendingTotal,
        refresh,
        recommendationFor,
        recommendationsFor,
        send,
        acknowledge,
        afterMeasurements,
        setSpeed,
    };
});
