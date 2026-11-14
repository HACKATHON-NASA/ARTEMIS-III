import gsap from 'gsap';
import { onBeforeUnmount } from 'vue';
import type { Ref } from 'vue';
import { prefersReducedMotion } from './motionPreferences';

/**
 * Movimiento de la pantalla de acceso, a propósito discreto: el fondo y el
 * formulario NO se animan con el puntero ni se mueven solos.
 *
 * - intro(): todo aparece con un fundido corto, sin giros ni desplazamientos.
 * - exit(): la pantalla se desvanece antes de ir al panel.
 *
 * El único elemento vivo es el electrocardiograma (HeartbeatLine).
 */
export function useLoginMotion(root: Ref<HTMLElement | null>) {
    let context: gsap.Context | null = null;

    function intro(): void {
        if (root.value === null || prefersReducedMotion()) {
            return;
        }

        context = gsap.context(() => {
            gsap.from('[data-intro]', {
                opacity: 0,
                duration: 0.6,
                ease: 'power2.out',
                stagger: 0.08,
            });
        }, root.value);
    }

    function exit(): Promise<void> {
        return new Promise((resolve) => {
            if (prefersReducedMotion() || root.value === null) {
                resolve();

                return;
            }

            gsap.to(root.value, {
                opacity: 0,
                duration: 0.35,
                ease: 'power2.in',
                onComplete: () => resolve(),
            });
        });
    }

    onBeforeUnmount(() => context?.revert());

    return { intro, exit };
}
