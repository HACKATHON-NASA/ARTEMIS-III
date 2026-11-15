/**
 * Se respeta "reducir movimiento" del sistema: sin bucles ni entradas.
 */
export function prefersReducedMotion(): boolean {
    return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}
