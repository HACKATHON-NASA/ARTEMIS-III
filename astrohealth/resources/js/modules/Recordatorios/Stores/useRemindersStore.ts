import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { api, ApiError } from '@/lib/api';
import type {
    Reminder,
    ReminderDraft,
    ReminderState,
    StoredReminder,
} from '../Types/reminders';
import { t } from '@/lib/i18n';

/**
 * La rutina del día de un astronauta (M8): sus recordatorios con el estado
 * calculado a la hora de ahora (hecho hoy · ya es la hora · más tarde), y
 * crear, cambiar, borrar y marcar hecho. `now` lo adelanta el reloj
 * (useReminderClock) para que los estados cambien solos.
 *
 * GET/POST/PATCH/DELETE /api/v1/reminders y POST /reminders/{id}/done.
 * Siempre es la rutina de quien inició sesión. "Hecho hoy" es el día UTC
 * (como el día de misión); los puntos los suma M7 en el backend.
 */
const utcDay = (time: number | string) =>
    new Date(time).toISOString().slice(0, 10);

function minutesOf(time: string): number {
    const [hours, minutes] = time.split(':').map(Number);

    return hours * 60 + minutes;
}

function fail(error: unknown, fallback: string): void {
    toast.error(error instanceof ApiError ? error.message : fallback);
}

export const useRemindersStore = defineStore('reminders', () => {
    const items = ref<StoredReminder[]>([]);
    const now = ref(Date.now());

    function stateOf(item: StoredReminder): ReminderState {
        if (
            item.last_done_at !== null &&
            utcDay(item.last_done_at) === utcDay(now.value)
        ) {
            return 'done';
        }

        const date = new Date(now.value);

        return date.getHours() * 60 + date.getMinutes() >= minutesOf(item.time)
            ? 'due'
            : 'upcoming';
    }

    /** Los activos del día, ordenados por hora, con su estado de ahora. */
    const reminders = computed<Reminder[]>(() =>
        items.value
            .filter((item) => item.active)
            .map((item) => ({ ...item, state: stateOf(item) }))
            .sort((a, b) => a.time.localeCompare(b.time)),
    );

    const progress = computed(() => ({
        done: reminders.value.filter((item) => item.state === 'done').length,
        total: reminders.value.length,
    }));

    function put(item: StoredReminder): void {
        const index = items.value.findIndex((other) => other.id === item.id);

        if (index === -1) {
            items.value.push(item);
        } else {
            items.value[index] = item;
        }
    }

    /** La rutina de quien inició sesión (el id es el de su astronauta). */
    async function load(_astronautId?: number): Promise<void> {
        items.value = await api<StoredReminder[]>('/reminders');
        now.value = Date.now();
    }

    function tick(): void {
        now.value = Date.now();
    }

    /** Crea (sin id) o cambia un recordatorio. Devuelve si se guardó. */
    async function save(draft: ReminderDraft, id?: number): Promise<boolean> {
        try {
            put(
                await api<StoredReminder>(
                    id === undefined ? '/reminders' : `/reminders/${id}`,
                    {
                        method: id === undefined ? 'POST' : 'PATCH',
                        body: draft,
                    },
                ),
            );

            return true;
        } catch (error) {
            fail(error, t('Recordatorios.saveFailed'));

            return false;
        }
    }

    async function remove(id: number): Promise<boolean> {
        try {
            await api(`/reminders/${id}`, { method: 'DELETE' });
            items.value = items.value.filter((item) => item.id !== id);

            return true;
        } catch (error) {
            fail(error, t('Recordatorios.deleteFailed'));

            return false;
        }
    }

    /** Lo marca hecho hoy. Devuelve false si ya estaba hecho (no suma dos veces). */
    async function markDone(id: number): Promise<boolean> {
        try {
            put(
                await api<StoredReminder>(`/reminders/${id}/done`, {
                    method: 'POST',
                }),
            );

            return true;
        } catch (error) {
            if (error instanceof ApiError && error.status === 409) {
                await load();
            } else {
                fail(error, t('Recordatorios.markFailed'));
            }

            return false;
        }
    }

    return { reminders, progress, now, load, tick, save, remove, markDone };
});
