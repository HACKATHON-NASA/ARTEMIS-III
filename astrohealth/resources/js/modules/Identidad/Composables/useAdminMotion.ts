import gsap from 'gsap';
import { prefersReducedMotion } from './motionPreferences';

/**
 * Las cifras del resumen de administración cuentan hasta su valor.
 * (La entrada de las secciones es global: @/composables/useReveal.)
 * Si la cifra cambia mientras cuenta, la cuenta anterior se cancela (si no,
 * la vieja terminaría después y dejaría el valor viejo).
 */
const running = new WeakMap<object, gsap.core.Tween>();

export function countTo(
    target: { value: number },
    to: number,
    duration = 1.1,
): void {
    running.get(target)?.kill();

    if (prefersReducedMotion()) {
        target.value = to;

        return;
    }

    const state = { value: target.value };
    const tween = gsap.to(state, {
        value: to,
        duration,
        ease: 'power2.out',
        onUpdate: () => {
            target.value = Math.round(state.value);
        },
    });
    running.set(target, tween);
}
