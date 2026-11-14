import gsap from 'gsap';
import { onBeforeUnmount } from 'vue';
import type { Ref } from 'vue';
import { prefersReducedMotion } from './motionPreferences';

/**
 * Movimiento de la vista de administración: las secciones aparecen en
 * orden (fundido + subida corta) y los números de las tarjetas cuentan
 * hasta su valor. Nada se mueve solo después de entrar.
 */
export function useAdminMotion(root: Ref<HTMLElement | null>) {
    let context: gsap.Context | null = null;

    function reveal(): void {
        if (root.value === null || prefersReducedMotion()) {
            return;
        }

        context = gsap.context(() => {
            gsap.from('[data-reveal]', {
                opacity: 0,
                y: 18,
                duration: 0.7,
                ease: 'power3.out',
                stagger: 0.08,
                clearProps: 'transform',
            });
        }, root.value);
    }

    onBeforeUnmount(() => context?.revert());

    return { reveal };
}

/** Anima un número de 0 (o del valor anterior) al nuevo valor. */
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
