import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api } from '@/lib/api';
import type { IndicatorCode, Status } from '@/types/health';
import type { Alert, NewMessage, Recommendation } from '../Types/alerts';
import { atSpeed, readSpeed, writeSpeed } from '../Utils/demoSpeed';

/**
 * Las alertas (M5): las de un astronauta (`alerts`, para su Inicio y el Panel
 * de salud), el historial del centro (`history`), el contador del menú y las
 * recomendaciones.
 *
 * GET /api/v1/alerts?astronaut= (el backend solo devuelve lo que la persona
 * puede ver), POST /api/v1/alerts (SOS, ayuda, novedad), PATCH
 * /api/v1/alerts/{id}/acknowledge y GET/PUT /api/v1/recommendations.
 * Las alertas vitales las crea el backend solo al guardar un valor en
 * amarillo o rojo. `refresh()` lo llama useLiveAlerts cada pocos segundos.
 */
export const useAlertsStore = defineStore('alerts', () => {
    const raw = ref<Alert[]>([]);
    const rawHistory = ref<Alert[]>([]);
    const recommendations = ref<Recommendation[]>([]);
    const pendingTotal = ref(0);
    /** Velocidad de la demo (1 = real, 60 = el retraso de Marte en 20 s). */
    const speed = ref(readSpeed());

    let alertsScope: number | null = null;
    let historyScope: number | null | undefined;
    let pendingScope: number | null = null;
    const seen = new Set<number>();
    let primed = false;
    let recommendationsLoad: Promise<void> | null = null;

    const alerts = computed(() =>
        raw.value.map((alert) => atSpeed(alert, speed.value)),
    );
    const history = computed(() =>
        rawHistory.value.map((alert) => atSpeed(alert, speed.value)),
    );

    /** La alerta roja más reciente de `alerts` que Tierra aún no atendió (SOS incluido). */
    const active = computed(
        () =>
            alerts.value.find(
                (alert) =>
                    alert.level === 'red' && alert.acknowledged_at === null,
            ) ?? null,
    );

    function fetchAlerts(astronautId: number | null): Promise<Alert[]> {
        return api<Alert[]>(
            astronautId === null
                ? '/alerts'
                : `/alerts?astronaut=${astronautId}`,
        );
    }

    /** "Qué hacer": se pide una vez (lo usan varias pantallas sin esperar). */
    function loadRecommendations(): Promise<void> {
        recommendationsLoad ??= api<Recommendation[]>('/recommendations')
            .then((list) => {
                recommendations.value = list;
            })
            .catch((error: unknown) => {
                recommendationsLoad = null;
                throw error;
            });

        return recommendationsLoad;
    }

    async function load(astronautId: number): Promise<void> {
        alertsScope = astronautId;
        [raw.value] = await Promise.all([
            fetchAlerts(astronautId),
            loadRecommendations(),
        ]);
    }

    /** El historial: de un astronauta o, con `null`, de todo lo que puede ver. */
    async function loadHistory(astronautId: number | null): Promise<void> {
        historyScope = astronautId;
        [rawHistory.value] = await Promise.all([
            fetchAlerts(astronautId),
            loadRecommendations(),
        ]);
    }

    async function loadPendingTotal(astronautId: number | null): Promise<void> {
        pendingScope = astronautId;
        const [list] = await Promise.all([
            fetchAlerts(astronautId),
            loadRecommendations(),
        ]);
        pendingTotal.value = list.filter(
            (a) => a.acknowledged_at === null,
        ).length;
    }

    /** Vuelve a pedir lo cargado. Devuelve las alertas que no se habían visto. */
    async function refresh(): Promise<Alert[]> {
        const all = await fetchAlerts(pendingScope);
        const fresh = primed ? all.filter((alert) => !seen.has(alert.id)) : [];
        all.forEach((alert) => seen.add(alert.id));
        primed = true;
        pendingTotal.value = all.filter(
            (a) => a.acknowledged_at === null,
        ).length;

        const [own, past] = await Promise.all([
            alertsScope === null ? null : fetchAlerts(alertsScope),
            historyScope === undefined ? null : fetchAlerts(historyScope),
        ]);

        if (own !== null) {
            raw.value = own;
        }

        if (past !== null) {
            rawHistory.value = past;
        }

        return fresh.map((alert) => atSpeed(alert, speed.value));
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

    /** El astronauta manda un SOS, pide ayuda o cuenta una novedad (sale de la sesión). */
    async function send(
        _astronautId: number,
        message: NewMessage,
    ): Promise<Alert> {
        const alert = await api<Alert>('/alerts', {
            method: 'POST',
            body: {
                kind: message.kind,
                category: message.category,
                note: message.note,
                urgent: message.urgent ?? false,
            },
        });
        seen.add(alert.id);
        await refresh();

        return atSpeed(alert, speed.value);
    }

    /** Tierra atiende la alerta y responde (la respuesta tarda en volver). */
    async function acknowledge(
        id: number,
        reply: string,
    ): Promise<Alert | null> {
        const alert = await api<Alert>(`/alerts/${id}/acknowledge`, {
            method: 'PATCH',
            body: { reply },
        });
        await refresh();

        return atSpeed(alert, speed.value);
    }

    /** Tras registrar mediciones (M1): el backend ya creó las alertas; aquí se traen. */
    async function afterMeasurements(
        astronautId: number,
        measurements: { id: number }[],
    ): Promise<Alert[]> {
        await load(astronautId);
        const ids = new Set(measurements.map((item) => item.id));
        const created = alerts.value.filter(
            (alert) =>
                alert.measurement_id !== null && ids.has(alert.measurement_id),
        );
        created.forEach((alert) => seen.add(alert.id));
        await refresh();

        return created;
    }

    /** La Guía de salud cambia "qué hacer" (permiso guide.manage). */
    async function saveRecommendation(
        recommendation: Recommendation,
    ): Promise<void> {
        const saved = await api<Recommendation>(
            `/recommendations/${recommendation.indicator}/${recommendation.status}`,
            {
                method: 'PUT',
                body: {
                    title: recommendation.title,
                    action_text: recommendation.action_text,
                    risk_ref: recommendation.risk_ref,
                },
            },
        );
        recommendations.value = [
            ...recommendations.value.filter(
                (item) =>
                    item.indicator !== saved.indicator ||
                    item.status !== saved.status,
            ),
            saved,
        ];
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
        saveRecommendation,
        setSpeed,
    };
});
