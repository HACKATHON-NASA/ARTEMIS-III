import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { IndicatorCode, Status } from '@/types/health';
import type { Alert, Recommendation } from '../Types/alerts';
import {
    MOCK_RECOMMENDATIONS,
    mockAlerts,
    mockPendingTotal,
} from '../Utils/alertsMockData';

/**
 * Las alertas de un astronauta y las recomendaciones (M5).
 *
 * ⚠️ HOY con datos de prueba. sendToGround() imita lo que hará el backend
 * (sent_at y ground_eta_at = ahora + el retraso del entorno); cuando exista
 * POST /api/v1/alerts/{id}/send-to-ground, esos datos vienen de la API.
 */
export const useAlertsStore = defineStore('alerts', () => {
    const alerts = ref<Alert[]>([]);
    const recommendations = ref<Recommendation[]>([]);
    /** Alertas sin confirmar de toda la tripulación (contador del menú). */
    const pendingTotal = ref(0);

    /** La alerta roja más reciente que todavía no salió hacia Tierra (o viaja). */
    const active = computed(
        () =>
            alerts.value.find(
                (alert) =>
                    alert.level === 'red' && alert.acknowledged_at === null,
            ) ?? null,
    );

    async function load(astronautId: number): Promise<void> {
        await new Promise((resolve) => setTimeout(resolve, 250));
        alerts.value = mockAlerts(astronautId);
        recommendations.value = MOCK_RECOMMENDATIONS;
    }

    async function loadPendingTotal(): Promise<void> {
        await new Promise((resolve) => setTimeout(resolve, 150));
        pendingTotal.value = mockPendingTotal();
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

    async function sendToGround(id: number): Promise<Alert | null> {
        await new Promise((resolve) => setTimeout(resolve, 400));
        const alert = alerts.value.find((item) => item.id === id);

        if (!alert) {
            return null;
        }

        const now = Date.now();
        alert.sent_to_ground = true;
        alert.sent_at = new Date(now).toISOString();
        alert.ground_eta_at = new Date(
            now + alert.delay_seconds * 1000,
        ).toISOString();

        return alert;
    }

    return {
        alerts,
        recommendations,
        pendingTotal,
        active,
        load,
        loadPendingTotal,
        recommendationFor,
        recommendationsFor,
        sendToGround,
    };
});
