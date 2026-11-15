import type { Advice } from '../Types/advice';
import type { Alert } from '../Types/alerts';

/**
 * La velocidad de la demo: 1 = real (Marte tarda 20 min), 60 = el viaje de
 * la señal se ve en 20 s. Solo cambia lo que se MUESTRA en este navegador:
 * el backend guarda los tiempos reales. Se recuerda en el navegador.
 */
const KEY = 'astrohealth.demo.speed';

export function readSpeed(): number {
    try {
        return Number(localStorage.getItem(KEY)) === 60 ? 60 : 1;
    } catch {
        return 1;
    }
}

export function writeSpeed(speed: number): void {
    try {
        localStorage.setItem(KEY, String(speed));
    } catch {
        // Sin localStorage: la velocidad no se recuerda.
    }
}

function shifted(start: string, seconds: number): string {
    return new Date(new Date(start).getTime() + seconds * 1000).toISOString();
}

/** La alerta como se ve con esa velocidad (el retraso dividido). */
export function atSpeed(alert: Alert, speed: number): Alert {
    if (speed === 1) {
        return alert;
    }

    const delay = Math.round(alert.delay_seconds / speed);

    return {
        ...alert,
        delay_seconds: delay,
        ground_eta_at: shifted(alert.sent_at, delay),
    };
}

/** La indicación como se ve con esa velocidad. */
export function adviceAtSpeed(advice: Advice, speed: number): Advice {
    if (speed === 1) {
        return advice;
    }

    const delay = Math.round(
        (new Date(advice.arrives_at).getTime() -
            new Date(advice.sent_at).getTime()) /
            1000 /
            speed,
    );

    return { ...advice, arrives_at: shifted(advice.sent_at, delay) };
}
