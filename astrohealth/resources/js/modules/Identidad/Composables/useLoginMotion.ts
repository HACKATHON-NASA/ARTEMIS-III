import gsap from 'gsap';
import { onBeforeUnmount } from 'vue';
import type { Ref } from 'vue';
import { prefersReducedMotion } from './motionPreferences';

/**
 * La coreografía de la pantalla de acceso. Los componentes marcan sus
 * piezas con data-intro="…" y aquí se animan en orden:
 *
 * - intro(): el espacio se acerca, la marca entra desde la izquierda y la
 *   tarjeta "aterriza" girando en 3D; después, los campos uno a uno.
 * - shake(): la tarjeta niega con la cabeza (credenciales malas).
 * - exit(): la tarjeta se aleja y la cámara viaja hacia el espacio.
 */
export function useLoginMotion(root: Ref<HTMLElement | null>) {
    let context: gsap.Context | null = null;

    function select(name: string): Element[] {
        return root.value === null
            ? []
            : gsap.utils.toArray<Element>(`[data-intro="${name}"]`, root.value);
    }

    function intro(): void {
        if (root.value === null) {
            return;
        }

        context = gsap.context(() => {
            if (prefersReducedMotion()) {
                gsap.from('[data-intro]', { opacity: 0, duration: 0.5 });

                return;
            }

            gsap.timeline({ defaults: { ease: 'expo.out' } })
                .from('[data-intro="space"]', {
                    scale: 1.2,
                    opacity: 0,
                    duration: 2.4,
                })
                .from('[data-intro="stars"]', { opacity: 0, duration: 2 }, 0.2)
                .from(
                    '[data-intro="pulse"]',
                    { opacity: 0, x: 60, duration: 1.4 },
                    0.5,
                )
                .from(
                    '[data-intro="brand"] > *',
                    {
                        x: -56,
                        opacity: 0,
                        filter: 'blur(12px)',
                        duration: 1.3,
                        stagger: 0.14,
                    },
                    0.35,
                )
                .from(
                    '[data-intro="card"]',
                    {
                        y: 90,
                        z: -260,
                        rotationX: 24,
                        opacity: 0,
                        transformPerspective: 1400,
                        transformOrigin: '50% 100%',
                        duration: 1.6,
                    },
                    0.45,
                )
                .from(
                    '[data-intro="field"]',
                    { y: 22, opacity: 0, duration: 1, stagger: 0.09 },
                    1,
                );
        }, root.value);
    }

    function shake(): void {
        if (prefersReducedMotion()) {
            return;
        }

        gsap.to(select('card'), {
            keyframes: { x: [0, -16, 13, -9, 5, 0] },
            duration: 0.6,
            ease: 'power2.out',
        });
    }

    function exit(): Promise<void> {
        return new Promise((resolve) => {
            if (prefersReducedMotion() || root.value === null) {
                resolve();

                return;
            }

            gsap.timeline({ onComplete: () => resolve() })
                .to(select('card'), {
                    scale: 0.9,
                    z: -200,
                    opacity: 0,
                    duration: 0.7,
                    ease: 'power3.in',
                })
                .to(
                    select('brand'),
                    { x: -40, opacity: 0, duration: 0.6, ease: 'power3.in' },
                    0,
                )
                .to(
                    select('space'),
                    { scale: 1.45, duration: 1.2, ease: 'power2.in' },
                    0,
                )
                .to(root.value, { opacity: 0, duration: 0.4 }, 0.8);
        });
    }

    onBeforeUnmount(() => context?.revert());

    return { intro, shake, exit };
}
