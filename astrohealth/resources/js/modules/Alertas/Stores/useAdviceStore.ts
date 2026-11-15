import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { Advice, AdviceDraft } from '../Types/advice';
import {
    mockAdvice,
    mockReadAdvice,
    mockSendAdvice,
} from '../Utils/adviceMock';

/**
 * Las indicaciones del médico (M5): las de un tripulante (ficha del médico)
 * o las mías (Inicio del astronauta). Solo se muestran al astronauta las que
 * ya llegaron a la nave.
 *
 * ⚠️ HOY con datos de prueba (Utils/adviceMock.ts). Con el backend:
 * GET/POST /api/v1/advice y PATCH /api/v1/advice/{id}/read (advice.send).
 */
const wait = (ms = 250) => new Promise((resolve) => setTimeout(resolve, ms));

export const useAdviceStore = defineStore('advice', () => {
    const items = ref<Advice[]>([]);
    /** Las de toda una tripulación (el historial por misión). */
    const history = ref<Advice[]>([]);
    const now = ref(Date.now());

    /** Las que ya llegaron a la nave y no se han leído (para el astronauta). */
    const unread = computed(() =>
        items.value.filter(
            (item) =>
                !item.read_at &&
                new Date(item.arrives_at).getTime() <= now.value,
        ),
    );

    async function load(astronautId: number): Promise<void> {
        await wait();
        now.value = Date.now();
        items.value = mockAdvice()
            .filter((item) => item.astronaut_id === astronautId)
            .sort((a, b) => b.sent_at.localeCompare(a.sent_at));
    }

    async function loadForCrew(astronautIds: number[]): Promise<void> {
        await wait();
        now.value = Date.now();
        history.value = mockAdvice()
            .filter((item) => astronautIds.includes(item.astronaut_id))
            .sort((a, b) => b.sent_at.localeCompare(a.sent_at));
    }

    async function send(
        astronautId: number,
        author: string,
        draft: AdviceDraft,
    ): Promise<Advice> {
        await wait(400);
        const advice = mockSendAdvice(astronautId, author, draft);
        await load(astronautId);
        history.value = [advice, ...history.value];

        return advice;
    }

    async function markRead(id: number): Promise<void> {
        await wait(200);
        mockReadAdvice(id);
        const item = items.value.find((advice) => advice.id === id);

        if (item) {
            item.read_at = new Date().toISOString();
        }
    }

    function tick(): void {
        now.value = Date.now();
    }

    return {
        items,
        history,
        now,
        unread,
        load,
        loadForCrew,
        send,
        markRead,
        tick,
    };
});
