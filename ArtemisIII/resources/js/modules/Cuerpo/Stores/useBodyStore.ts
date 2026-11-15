import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api } from '@/lib/api';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus } from '../Types/body';
import { localizePart } from '../Utils/zoneValues';

/**
 * El cuerpo de un astronauta (M3): las zonas, su estado y la zona elegida.
 *
 * GET /api/v1/body-parts (una vez) y GET /api/v1/astronauts/{id}/body-status:
 * el último valor de cada indicador y el estado de cada zona, que decide
 * el backend (M2: el peor de sus lecturas).
 */
const DAY_MS = 24 * 60 * 60 * 1000;

export const useBodyStore = defineStore('body', () => {
    const parts = ref<BodyPart[]>([]);
    const status = ref<BodyStatus | null>(null);
    const selected = ref<BodyZoneCode | null>(null);
    /** El cuerpo de cada tripulante de una misión (Tripulación). */
    const crew = ref<Record<number, BodyStatus>>({});

    const selectedPart = computed(
        () => parts.value.find((part) => part.code === selected.value) ?? null,
    );

    /** La primera zona en rojo o, si no hay, en amarillo (para abrir de entrada). */
    const mostUrgent = computed<BodyZoneCode | null>(() => {
        if (!status.value) {
            return null;
        }

        const codes = parts.value.map((part) => part.code);

        return (
            codes.find((code) => status.value?.[code]?.status === 'red') ??
            codes.find((code) => status.value?.[code]?.status === 'yellow') ??
            codes[0] ??
            null
        );
    });

    /** Cuántos indicadores se midieron en las últimas 24 h, de cuántos hay. */
    const measuredToday = computed(() => {
        const values = Object.values(status.value ?? {}).flatMap(
            (zone) => zone.values,
        );
        const total = parts.value.reduce(
            (sum, part) => sum + part.indicators.length,
            0,
        );

        return {
            done: values.filter(
                (value) =>
                    Date.now() - new Date(value.measured_at).getTime() < DAY_MS,
            ).length,
            total: total || values.length,
        };
    });

    async function loadParts(): Promise<void> {
        if (parts.value.length === 0) {
            parts.value = (await api<BodyPart[]>('/body-parts')).map(
                localizePart,
            );
        }
    }

    function statusOf(astronautId: number): Promise<BodyStatus> {
        return api<BodyStatus>(`/astronauts/${astronautId}/body-status`);
    }

    async function load(astronautId: number): Promise<void> {
        const [, body] = await Promise.all([
            loadParts(),
            statusOf(astronautId),
        ]);
        status.value = body;
    }

    async function loadCrew(astronautIds: number[]): Promise<void> {
        const [, bodies] = await Promise.all([
            loadParts(),
            Promise.all(astronautIds.map((id) => statusOf(id))),
        ]);
        crew.value = Object.fromEntries(
            astronautIds.map((id, index) => [id, bodies[index]]),
        );
    }

    function select(code: BodyZoneCode | null): void {
        selected.value = code;
    }

    return {
        parts,
        status,
        selected,
        selectedPart,
        mostUrgent,
        measuredToday,
        crew,
        load,
        loadCrew,
        select,
    };
});
