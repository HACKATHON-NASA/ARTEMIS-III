import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api } from '@/lib/api';
import type { Advice, AdviceDraft } from '../Types/advice';
import { adviceAtSpeed, readSpeed } from '../Utils/demoSpeed';

/**
 * Las indicaciones del médico (M5): las de un tripulante (ficha del médico)
 * o las mías (Inicio del astronauta). Al astronauta solo se le muestran las
 * que ya llegaron a la nave.
 *
 * GET /api/v1/advice?astronaut= (el backend solo devuelve lo que la persona
 * puede ver), POST /api/v1/advice (advice.send; el autor sale de la sesión)
 * y PATCH /api/v1/advice/{id}/read.
 */
export const useAdviceStore = defineStore('advice', () => {
    const raw = ref<Advice[]>([]);
    /** Las de toda una tripulación (el historial por misión). */
    const rawHistory = ref<Advice[]>([]);
    const now = ref(Date.now());

    const items = computed(() =>
        raw.value.map((item) => adviceAtSpeed(item, readSpeed())),
    );
    const history = computed(() =>
        rawHistory.value.map((item) => adviceAtSpeed(item, readSpeed())),
    );

    /** Las que ya llegaron a la nave y no se han leído (para el astronauta). */
    const unread = computed(() =>
        items.value.filter(
            (item) =>
                !item.read_at &&
                new Date(item.arrives_at).getTime() <= now.value,
        ),
    );

    async function load(astronautId: number): Promise<void> {
        raw.value = await api<Advice[]>(`/advice?astronaut=${astronautId}`);
        now.value = Date.now();
    }

    async function loadForCrew(astronautIds: number[]): Promise<void> {
        const list = await api<Advice[]>('/advice');
        rawHistory.value = list.filter((item) =>
            astronautIds.includes(item.astronaut_id),
        );
        now.value = Date.now();
    }

    async function send(
        astronautId: number,
        _author: string,
        draft: AdviceDraft,
    ): Promise<Advice> {
        const advice = await api<Advice>('/advice', {
            method: 'POST',
            body: { ...draft, astronaut_id: astronautId },
        });
        await load(astronautId);
        rawHistory.value = [advice, ...rawHistory.value];

        return adviceAtSpeed(advice, readSpeed());
    }

    async function markRead(id: number): Promise<void> {
        const saved = await api<Advice>(`/advice/${id}/read`, {
            method: 'PATCH',
        });
        raw.value = raw.value.map((item) => (item.id === id ? saved : item));
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
