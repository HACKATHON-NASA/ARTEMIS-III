import { useCrewStore } from '../../Entorno/Stores/useCrewStore';
import { useAuthStore } from '@/stores/useAuthStore';
import type { Alert } from '../Types/alerts';
import { alertMessage } from '../Utils/alertText';

/**
 * El texto de una alerta con el nombre del tripulante: el de la
 * tripulación cargada o, si es del propio astronauta, el suyo.
 */
export function useAlertText(): (alert: Alert) => string {
    const crew = useCrewStore();
    const auth = useAuthStore();

    return (alert) => {
        const name =
            crew.find(alert.astronaut_id)?.name ??
            (alert.astronaut_id === auth.actor?.astronaut_id
                ? (auth.actor?.name ?? null)
                : null);

        return alertMessage(alert, name);
    };
}
