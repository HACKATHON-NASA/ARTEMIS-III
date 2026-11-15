import gsap from 'gsap';
import { onBeforeUnmount } from 'vue';
import type { Ref } from 'vue';

/**
 * Las secciones marcadas con `data-reveal` aparecen en orden (fundido y
 * subida corta) al cargar una pantalla. Nada se mueve después.
 * Respeta "reducir movimiento".
 */
export function useReveal(root: Ref<HTMLElement | null>) {
    let context: gsap.Context | null = null;

    function reveal(): void {
        if (
            root.value === null ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
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
