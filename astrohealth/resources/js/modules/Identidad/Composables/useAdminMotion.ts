import gsap from 'gsap';
import { prefersReducedMotion } from './motionPreferences';

/**
 * Las cifras del resumen de administración cuentan hasta su valor.
 * (La entrada de las secciones es global: @/composables/useReveal.)
 */
export function countTo(
    target: { value: number },
    to: number,
    duration = 1.1,
): void {
    if (prefersReducedMotion()) {
        target.value = to;

        return;
    }

    const state = { value: target.value };
    gsap.to(state, {
        value: to,
        duration,
        ease: 'power2.out',
        onUpdate: () => {
            target.value = Math.round(state.value);
        },
    });
}
