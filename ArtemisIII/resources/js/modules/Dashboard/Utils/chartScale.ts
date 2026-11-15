/**
 * Escalas de las gráficas SVG hechas a mano (sin librería): lineal y
 * marcas "redondas" para el eje.
 */
export function linear(
    [d0, d1]: [number, number],
    [r0, r1]: [number, number],
): (value: number) => number {
    const span = d1 - d0 || 1;

    return (value) => r0 + ((value - d0) / span) * (r1 - r0);
}

/** Unas `count` marcas redondas (1, 2, 2.5 o 5 × 10^n) que cubren [min, max]. */
export function niceTicks(min: number, max: number, count = 5): number[] {
    const raw = (max - min || Math.abs(max) || 1) / count;
    const power = 10 ** Math.floor(Math.log10(raw));
    const step =
        [1, 2, 2.5, 5, 10].map((m) => m * power).find((s) => s >= raw) ??
        10 * power;
    const ticks: number[] = [];

    // La última marca siempre cubre el máximo (si no, la línea se sale).
    for (let tick = Math.floor(min / step) * step; ; tick += step) {
        ticks.push(Math.round(tick * 1e6) / 1e6);

        if (tick >= max) {
            break;
        }
    }

    return ticks;
}
