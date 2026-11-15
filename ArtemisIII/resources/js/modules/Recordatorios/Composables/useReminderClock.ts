import { onBeforeUnmount, onMounted } from 'vue';
import { toast } from 'vue-sonner';
import { useNotifications } from '@/composables/useNotifications';
import { useRemindersStore } from '../Stores/useRemindersStore';

/**
 * El reloj de la rutina: cada 30 s adelanta la hora del store y, cuando un
 * recordatorio pasa a "ya es la hora", avisa UNA vez por recordatorio y día
 * (toast + notificación del navegador si hay permiso). Lo monta el menú
 * lateral, así funciona en cualquier pantalla. Lo que ya estaba pendiente al
 * abrir la app no se avisa (para no llenar la pantalla de avisos).
 */
const EVERY_MS = 30_000;
const KEY = 'astrohealth.demo.reminders.notified';

function readNotified(): Set<string> {
    try {
        return new Set(
            JSON.parse(localStorage.getItem(KEY) ?? '[]') as string[],
        );
    } catch {
        return new Set();
    }
}

function writeNotified(keys: Set<string>): void {
    try {
        localStorage.setItem(KEY, JSON.stringify([...keys].slice(-50)));
    } catch {
        // Sin localStorage: puede repetir el aviso al recargar.
    }
}

export function useReminderClock(astronautId: () => number | null) {
    const store = useRemindersStore();
    const { notify } = useNotifications();
    let timer: ReturnType<typeof setInterval> | undefined;

    function check(silent: boolean): void {
        store.tick();
        const today = new Date().toDateString();
        const notified = readNotified();

        for (const reminder of store.reminders) {
            const key = `${today}-${reminder.id}`;

            if (reminder.state !== 'due' || notified.has(key)) {
                continue;
            }

            notified.add(key);

            if (!silent) {
                toast(reminder.title, {
                    description: `Son las ${reminder.time}: es la hora.`,
                    duration: 10_000,
                });
                notify(reminder.title, `Son las ${reminder.time}: es la hora.`);
            }
        }

        writeNotified(notified);
    }

    onMounted(async () => {
        const id = astronautId();

        if (id === null) {
            return;
        }

        await store.load(id);
        check(true);
        timer = setInterval(() => check(false), EVERY_MS);
    });

    onBeforeUnmount(() => clearInterval(timer));
}
