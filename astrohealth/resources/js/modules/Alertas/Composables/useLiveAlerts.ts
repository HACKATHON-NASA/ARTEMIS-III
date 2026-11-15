import { router } from '@inertiajs/vue3';
import { onBeforeUnmount, onMounted } from 'vue';
import { toast } from 'vue-sonner';
import { alertas } from '@/routes';
import { useAlertsStore } from '../Stores/useAlertsStore';
import type { Alert } from '../Types/alerts';

/**
 * Las alertas en vivo: cada pocos segundos vuelve a pedirlas y, si llegó
 * algo nuevo, avisa con un toast (el SOS, en rojo y sin cerrarse solo).
 * Lo monta el menú lateral, así funciona en cualquier pantalla.
 *
 * `notify` = solo avisa quien atiende (Tierra y el médico de la nave); el
 * astronauta ve llegar las respuestas en su pantalla.
 * Con el backend: GET /api/v1/alerts (sondeo cada 5 s; un canal en tiempo real queda para después).
 */
const EVERY_MS = 5000;

export function useLiveAlerts(notify: () => boolean) {
    const store = useAlertsStore();
    let timer: ReturnType<typeof setInterval> | undefined;

    function announce(alert: Alert): void {
        const action = {
            label: 'Ver',
            onClick: () => router.visit(alertas()),
        };

        if (alert.kind === 'sos') {
            toast.error(alert.message, {
                description: alert.note ?? 'Emergencia: atiéndela ya.',
                duration: Infinity,
                action,
            });

            return;
        }

        const show =
            alert.level === 'red'
                ? toast.error
                : alert.level === 'yellow'
                  ? toast.warning
                  : toast.info;
        show(alert.message, {
            description: alert.note ?? 'Nueva alerta en el centro.',
            action,
        });
    }

    async function tick(): Promise<void> {
        try {
            const fresh = await store.refresh();

            if (notify()) {
                fresh.forEach(announce);
            }
        } catch {
            // Sin conexión un momento: se vuelve a intentar en el próximo ciclo.
        }
    }

    onMounted(() => {
        void tick();
        timer = setInterval(() => void tick(), EVERY_MS);
    });

    onBeforeUnmount(() => clearInterval(timer));
}
