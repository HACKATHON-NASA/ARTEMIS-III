import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type { Reminder } from '../Types/reminders';

/**
 * La rutina del día de un astronauta (M8).
 *
 * ⚠️ HOY con datos de prueba (los 5 recordatorios por defecto del plan).
 * Cuando existan GET /api/v1/reminders y POST /reminders/{id}/done, las
 * acciones pasan a usar `api()`.
 */
function mockReminders(): Reminder[] {
    const doneAt = new Date();
    doneAt.setHours(8, 3, 0, 0);

    return [
        {
            id: 11,
            type: 'measure_bp',
            title: 'Medir la presión',
            time: '08:00',
            state: 'done',
            last_done_at: doneAt.toISOString(),
        },
        {
            id: 12,
            type: 'exercise',
            title: 'Ejercicio de resistencia (2 h)',
            time: '10:00',
            state: 'due',
            last_done_at: null,
        },
        {
            id: 13,
            type: 'mood_check',
            title: 'Chequeo de ánimo',
            time: '13:00',
            state: 'due',
            last_done_at: null,
        },
        {
            id: 14,
            type: 'dosimeter',
            title: 'Leer el dosímetro',
            time: '18:00',
            state: 'upcoming',
            last_done_at: null,
        },
        {
            id: 15,
            type: 'log_sleep',
            title: 'Registrar el sueño',
            time: '21:30',
            state: 'upcoming',
            last_done_at: null,
        },
    ];
}

export const useRemindersStore = defineStore('reminders', () => {
    const reminders = ref<Reminder[]>([]);

    const progress = computed(() => ({
        done: reminders.value.filter((item) => item.state === 'done').length,
        total: reminders.value.length,
    }));

    async function load(astronautId: number): Promise<void> {
        void astronautId;
        await new Promise((resolve) => setTimeout(resolve, 200));
        reminders.value = mockReminders();
    }

    /** Marca el recordatorio como hecho hoy. Devuelve los puntos ganados (M7). */
    async function markDone(id: number): Promise<number> {
        await new Promise((resolve) => setTimeout(resolve, 300));
        const reminder = reminders.value.find((item) => item.id === id);

        if (!reminder || reminder.state === 'done') {
            return 0;
        }

        reminder.state = 'done';
        reminder.last_done_at = new Date().toISOString();

        return 5;
    }

    return { reminders, progress, load, markDone };
});
