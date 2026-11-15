import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus } from '../Types/body';
import { MOCK_PARTS, mockBodyStatus } from '../Utils/bodyMockData';

/**
 * El cuerpo de un astronauta (M3): las zonas, su estado y la zona elegida.
 *
 * ⚠️ HOY con datos de prueba. Cuando existan las rutas de M3, load() pasa a
 * usar `api()`; los componentes no cambian.
 */
const DAY_MS = 24 * 60 * 60 * 1000;

export const useBodyStore = defineStore('body', () => {
    const parts = ref<BodyPart[]>([]);
    const status = ref<BodyStatus | null>(null);
    const selected = ref<BodyZoneCode | null>(null);

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
            codes.find((code) => status.value?.[code].status === 'red') ??
            codes.find((code) => status.value?.[code].status === 'yellow') ??
            codes[0] ??
            null
        );
    });

    /** Cuántos indicadores se midieron en las últimas 24 h, de cuántos hay. */
    const measuredToday = computed(() => {
        const values = Object.values(status.value ?? {}).flatMap(
            (zone) => zone.values,
        );

        return {
            done: values.filter(
                (value) =>
                    Date.now() - new Date(value.measured_at).getTime() < DAY_MS,
            ).length,
            total: values.length,
        };
    });

    async function load(astronautId: number): Promise<void> {
        await new Promise((resolve) => setTimeout(resolve, 300));
        parts.value = MOCK_PARTS;
        status.value = mockBodyStatus(astronautId);
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
        load,
        select,
    };
});
