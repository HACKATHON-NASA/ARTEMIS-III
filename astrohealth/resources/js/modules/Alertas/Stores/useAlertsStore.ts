import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { IndicatorCode, Status } from '@/types/health';
import type { Alert, Recommendation } from '../Types/alerts';
import {
    MOCK_RECOMMENDATIONS,
    mockAlerts,
    mockFindAlert,
    mockPendingTotal,
} from '../Utils/alertsMockData';

/**
 * Las alertas (M5): las de un astronauta (`alerts`, para su Inicio y el Panel
 * de salud), el historial del centro de alertas (`history`) y las
 * recomendaciones.
 *
 * ⚠️ HOY con datos de prueba en memoria. sendToGround() y acknowledge()
 * imitan lo que hará el backend; cuando existan las rutas de M5
 * (GET /alerts, POST /alerts/{id}/send-to-ground, PATCH /alerts/{id}/acknowledge)
 * las acciones pasan a usar `api()`.
 */
const wait = (ms: number) => new Promise((resolve) => setTimeout(resolve, ms));

export const useAlertsStore = defineStore('alerts', () => {
    const alerts = ref<Alert[]>([]);
    const history = ref<Alert[]>([]);
    const recommendations = ref<Recommendation[]>(MOCK_RECOMMENDATIONS);
    /** Alertas sin confirmar (contador del menú). */
    const pendingTotal = ref(0);
    let pendingScope: number | null = null;

    /** La alerta roja más reciente de `alerts` que Tierra aún no confirmó. */
    const active = computed(
        () =>
            alerts.value.find(
                (alert) =>
                    alert.level === 'red' && alert.acknowledged_at === null,
            ) ?? null,
    );

    async function load(astronautId: number): Promise<void> {
        await wait(250);
        alerts.value = mockAlerts(astronautId);
    }

    /** El historial: de un astronauta o, con `null`, de toda la tripulación. */
    async function loadHistory(astronautId: number | null): Promise<void> {
        await wait(250);
        history.value = mockAlerts(astronautId);
    }

    async function loadPendingTotal(astronautId: number | null): Promise<void> {
        pendingScope = astronautId;
        await wait(150);
        pendingTotal.value = mockPendingTotal(astronautId);
    }

    function recommendationFor(
        indicator: IndicatorCode,
        status: Status | null,
    ): Recommendation | null {
        return (
            recommendations.value.find(
                (item) =>
                    item.indicator === indicator && item.status === status,
            ) ?? null
        );
    }

    /**
     * Las recomendaciones para una lista de valores (los de una zona): una por
     * cada valor en amarillo o rojo, sin repetir título (gana la primera).
     */
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

    /** Cambia una alerta donde esté cargada (así se actualiza en todas las vistas). */
    function update(id: number, change: (alert: Alert) => void): Alert | null {
        const alert =
            alerts.value.find((item) => item.id === id) ??
            history.value.find((item) => item.id === id) ??
            mockFindAlert(id);

        if (!alert) {
            return null;
        }

        change(alert);
        pendingTotal.value = mockPendingTotal(pendingScope);

        return alert;
    }

    async function sendToGround(id: number): Promise<Alert | null> {
        await wait(400);

        return update(id, (alert) => {
            const now = Date.now();
            alert.sent_to_ground = true;
            alert.sent_at = new Date(now).toISOString();
            alert.ground_eta_at = new Date(
                now + alert.delay_seconds * 1000,
            ).toISOString();
        });
    }

    async function acknowledge(id: number): Promise<Alert | null> {
        await wait(300);

        return update(id, (alert) => {
            alert.acknowledged_at = new Date().toISOString();
        });
    }

    return {
        alerts,
        history,
        recommendations,
        pendingTotal,
        active,
        load,
        loadHistory,
        loadPendingTotal,
        recommendationFor,
        recommendationsFor,
        sendToGround,
        acknowledge,
    };
});
