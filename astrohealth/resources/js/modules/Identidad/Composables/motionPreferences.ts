/**
 * Cuándo animar. Se respeta "reducir movimiento" del sistema y no se
 * inclina ni se mueve nada con el puntero en pantallas táctiles.
 */

export function prefersReducedMotion(): boolean {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}

export function canFollowPointer(): boolean {
    return (
        !prefersReducedMotion() && window.matchMedia('(pointer: fine)').matches
    );
}

export function clamp(value: number, min = -1, max = 1): number {
    return Math.min(max, Math.max(min, value));
}
