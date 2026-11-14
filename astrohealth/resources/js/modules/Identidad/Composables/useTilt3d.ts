import gsap from 'gsap';
import { onBeforeUnmount, onMounted } from 'vue';
import type { Ref } from 'vue';
import { canFollowPointer, clamp } from './motionPreferences';

/**
 * Inclinación 3D que sigue al puntero, con inercia (quickTo de GSAP).
 * También deja la posición del puntero en --glare-x / --glare-y para
 * pintar un brillo que se mueve sobre el vidrio de la tarjeta.
 */
export function useTilt3d(
    target: Ref<HTMLElement | null>,
    maxDegrees = 6,
): void {
    let stop: (() => void) | null = null;

    onMounted(() => {
        const el = target.value;

        if (el === null || !canFollowPointer()) {
            return;
        }

        gsap.set(el, { transformPerspective: 1400 });

        const tween = { duration: 0.9, ease: 'power3' };
        const toRotateX = gsap.quickTo(el, 'rotationX', tween);
        const toRotateY = gsap.quickTo(el, 'rotationY', tween);

        const onMove = (event: PointerEvent): void => {
            const rect = el.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            toRotateY(
                clamp((event.clientX - centerX) / (window.innerWidth / 2)) *
                    maxDegrees,
            );
            toRotateX(
                -clamp((event.clientY - centerY) / (window.innerHeight / 2)) *
                    maxDegrees,
            );

            const glareX = ((event.clientX - rect.left) / rect.width) * 100;
            const glareY = ((event.clientY - rect.top) / rect.height) * 100;
            el.style.setProperty('--glare-x', `${glareX}%`);
            el.style.setProperty('--glare-y', `${glareY}%`);
        };

        const onLeave = (): void => {
            toRotateX(0);
            toRotateY(0);
        };

        window.addEventListener('pointermove', onMove, { passive: true });
        document.documentElement.addEventListener('pointerleave', onLeave);

        stop = () => {
            window.removeEventListener('pointermove', onMove);
            document.documentElement.removeEventListener(
                'pointerleave',
                onLeave,
            );
        };
    });

    onBeforeUnmount(() => stop?.());
}
