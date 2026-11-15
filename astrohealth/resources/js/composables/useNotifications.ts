import { ref } from 'vue';

/**
 * Las notificaciones del navegador: pedir el permiso (solo con un botón,
 * nunca al cargar) y mostrar una. Si el navegador no las tiene o la
 * persona no dio permiso, no hace nada (queda el toast de la app).
 */
const supported = typeof window !== 'undefined' && 'Notification' in window;
const permission = ref<NotificationPermission | 'unsupported'>(
    supported ? Notification.permission : 'unsupported',
);

export function useNotifications() {
    async function request(): Promise<void> {
        if (supported) {
            permission.value = await Notification.requestPermission();
        }
    }

    function notify(title: string, body: string): void {
        if (supported && permission.value === 'granted') {
            new Notification(title, { body, icon: '/favicon.ico' });
        }
    }

    return { permission, request, notify };
}
