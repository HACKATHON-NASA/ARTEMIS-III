import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type {
    Reminder,
    ReminderDraft,
    ReminderState,
} from '../Types/reminders';
import { mockReadReminders, mockWriteReminders } from '../Utils/remindersMock';

/**
 * La rutina del día de un astronauta (M8): sus recordatorios con el estado
 * calculado a la hora de ahora (hecho hoy · ya es la hora · más tarde), y
 * crear, cambiar, borrar y marcar hecho. `now` lo adelanta el reloj
 * (useReminderClock) para que los estados cambien solos.
 *
 * ⚠️ HOY con datos de prueba (Utils/remindersMock.ts, guardados en este
 * navegador). Con el backend: GET/POST/PATCH/DELETE /api/v1/reminders y
 * POST /reminders/{id}/done. Los puntos (M7) los coordina la página.
 */
const wait = (ms = 250) => new Promise((resolve) => setTimeout(resolve, ms));

function isToday(iso: string | null, now: number): boolean {
    return (
        iso !== null &&
        new Date(iso).toDateString() === new Date(now).toDateString()
    );
}

function minutesOf(time: string): number {
    const [hours, minutes] = time.split(':').map(Number);

    return hours * 60 + minutes;
}

export const useRemindersStore = defineStore('reminders', () => {
    const items = ref<Omit<Reminder, 'state'>[]>([]);
    const now = ref(Date.now());
    const astronautId = ref<number | null>(null);

    function stateOf(item: Omit<Reminder, 'state'>): ReminderState {
        if (isToday(item.last_done_at, now.value)) {
            return 'done';
        }

        const date = new Date(now.value);

        return date.getHours() * 60 + date.getMinutes() >= minutesOf(item.time)
            ? 'due'
            : 'upcoming';
    }

    /** Los del día, ordenados por hora, con su estado de ahora. */
    const reminders = computed<Reminder[]>(() =>
        items.value
            .map((item) => ({ ...item, state: stateOf(item) }))
            .sort((a, b) => a.time.localeCompare(b.time)),
    );

    const progress = computed(() => ({
        done: reminders.value.filter((item) => item.state === 'done').length,
        total: reminders.value.length,
    }));

    function persist(): void {
        if (astronautId.value !== null) {
            mockWriteReminders(astronautId.value, items.value);
        }
    }

    async function load(id: number): Promise<void> {
        await wait(200);
        astronautId.value = id;
        items.value = mockReadReminders(id);
        now.value = Date.now();
    }

    function tick(): void {
        now.value = Date.now();
    }

    /** Crea (sin id) o cambia un recordatorio. */
    async function save(draft: ReminderDraft, id?: number): Promise<void> {
        await wait();
        const existing = items.value.find((item) => item.id === id);

        if (existing) {
            Object.assign(existing, draft);
        } else {
            items.value.push({
                ...draft,
                id: Math.max(0, ...items.value.map((item) => item.id)) + 1,
                last_done_at: null,
            });
        }

        persist();
    }

    async function remove(id: number): Promise<void> {
        await wait();
        items.value = items.value.filter((item) => item.id !== id);
        persist();
    }

    /** Lo marca hecho hoy. Devuelve false si ya estaba hecho (no suma dos veces). */
    async function markDone(id: number): Promise<boolean> {
        await wait(300);
        const item = items.value.find((reminder) => reminder.id === id);

        if (!item || isToday(item.last_done_at, Date.now())) {
            return false;
        }

        item.last_done_at = new Date().toISOString();
        persist();

        return true;
    }

    return { reminders, progress, now, load, tick, save, remove, markDone };
});
