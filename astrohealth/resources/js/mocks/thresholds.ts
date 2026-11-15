import type { IndicatorCode, Status } from '@/types/health';

/**
 * ⚠️ DATOS DE PRUEBA que quedan de M2: los umbrales para que los OTROS
 * datos de prueba (series, perfiles, registro) pongan su color como lo hace
 * el backend. Se borra en F3, cuando las mediciones vengan con su color.
 *
 * Los umbrales reales ya vienen de GET /api/v1/thresholds: al cargarlos, la
 * Guía de salud llama a primeThresholds() y estos datos usan esos mismos
 * valores (así lo que se cambia en la Guía se ve en el resto).
 */
export type MockEnvironment = 'iss' | 'transit' | 'moon' | 'mars';

/** [verde mín, verde máx, amarillo mín, amarillo máx] */
export type MockBand = [number, number, number, number];

/** Valores de arranque: los mismos del ThresholdSeeder (plan §2). */
const BASE: Record<IndicatorCode, MockBand> = {
    hr: [60, 100, 50, 120],
    bp_sys: [90, 119, 90, 139],
    bp_dia: [60, 79, 60, 89],
    glucose: [70, 99, 60, 125],
    temp: [36.1, 37.2, 35.5, 38],
    spo2: [95, 100, 90, 100],
    sleep: [7, 9, 6, 24],
    mood: [4, 5, 3, 5],
    exercise_min: [90, 600, 60, 600],
    radiation: [0, 0.6, 0, 1.2],
};

const RADIATION_BASE: Record<MockEnvironment, number> = {
    iss: 0.5,
    transit: 1.8,
    moon: 1.4,
    mars: 0.6,
};

/** Lo que llegó del backend, por entorno. */
const primed: Partial<
    Record<MockEnvironment, Record<IndicatorCode, MockBand>>
> = {};

/** Guarda los umbrales que devolvió la API para que los usen los datos de prueba. */
export function primeThresholds(
    env: MockEnvironment,
    list: {
        indicator: IndicatorCode;
        green_min: number;
        green_max: number;
        yellow_min: number;
        yellow_max: number;
    }[],
): void {
    primed[env] = Object.fromEntries(
        list.map((item) => [
            item.indicator,
            [item.green_min, item.green_max, item.yellow_min, item.yellow_max],
        ]),
    ) as Record<IndicatorCode, MockBand>;
}

/** Los umbrales de un entorno: los del backend si ya llegaron; si no, los de arranque. */
export function mockThresholds(
    env: MockEnvironment = 'mars',
): Record<IndicatorCode, MockBand> {
    const base = RADIATION_BASE[env];

    return {
        ...BASE,
        radiation: [0, base, 0, base * 2],
        ...primed[env],
    };
}

/** Verde dentro del rango verde, amarillo dentro de la banda amarilla, si no rojo. */
export function mockEvaluate(
    indicator: IndicatorCode,
    value: number,
    env: MockEnvironment = 'mars',
): Status {
    const [greenMin, greenMax, yellowMin, yellowMax] =
        mockThresholds(env)[indicator];

    if (value >= greenMin && value <= greenMax) {
        return 'green';
    }

    return value >= yellowMin && value <= yellowMax ? 'yellow' : 'red';
}
