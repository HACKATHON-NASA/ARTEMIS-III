import gsap from 'gsap';
import { onBeforeUnmount, onMounted } from 'vue';
import type { Ref } from 'vue';
import { prefersReducedMotion } from './motionPreferences';

/**
 * Movimiento de cámara lento y continuo (acercamiento y deriva), para que
 * el espacio se sienta vivo aunque nadie mueva el puntero. Va en su propia
 * capa: no choca con la entrada ni con el paralaje.
 */
export function useAmbientDrift(target: Ref<HTMLElement | null>): void {
    let tween: gsap.core.Tween | null = null;

    onMounted(() => {
        const el = target.value;

        if (el === null || prefersReducedMotion()) {
            return;
        }

        tween = gsap.fromTo(
            el,
            { scale: 1.02, xPercent: 0, yPercent: 0 },
            {
                scale: 1.1,
                xPercent: -1.6,
                yPercent: -1.2,
                duration: 26,
                ease: 'sine.inOut',
                repeat: -1,
                yoyo: true,
                transformOrigin: '30% 55%',
            },
        );
    });

    onBeforeUnmount(() => tween?.kill());
}
