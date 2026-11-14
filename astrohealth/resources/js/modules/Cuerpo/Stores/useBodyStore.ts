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
export const useBodyStore = defineStore('body', () => {
    const parts = ref<BodyPart[]>([]);
    const status = ref<BodyStatus | null>(null);
    const selected = ref<BodyZoneCode | null>(null);

    const selectedPart = computed(
        () => parts.value.find((part) => part.code === selected.value) ?? null,
    );

    async function load(astronautId: number): Promise<void> {
        void astronautId;
        await new Promise((resolve) => setTimeout(resolve, 300));
        parts.value = MOCK_PARTS;
        status.value = mockBodyStatus();
    }

    function select(code: BodyZoneCode | null): void {
        selected.value = code;
    }

    return { parts, status, selected, selectedPart, load, select };
});
