import gsap from 'gsap';
import { onBeforeUnmount, onMounted } from 'vue';
import type { Ref } from 'vue';
import { canFollowPointer } from './motionPreferences';

/**
 * Paralaje: el elemento se mueve en sentido contrario al puntero.
 * Más `depth` = más cerca de la cámara (se mueve más). Varias capas con
 * distinta profundidad dan la sensación de espacio en 3D.
 */
export function usePointerParallax(
    target: Ref<HTMLElement | null>,
    depth: number,
): void {
    let stop: (() => void) | null = null;

    onMounted(() => {
        const el = target.value;

        if (el === null || !canFollowPointer()) {
            return;
        }

        const toX = gsap.quickTo(el, 'x', { duration: 1.4, ease: 'power3' });
        const toY = gsap.quickTo(el, 'y', { duration: 1.4, ease: 'power3' });

        const onMove = (event: PointerEvent): void => {
            toX(-(event.clientX / window.innerWidth - 0.5) * depth);
            toY(-(event.clientY / window.innerHeight - 0.5) * depth);
        };

        window.addEventListener('pointermove', onMove, { passive: true });
        stop = () => window.removeEventListener('pointermove', onMove);
    });

    onBeforeUnmount(() => stop?.());
}
