import type { IndicatorCode, Status } from '@/types/health';

/**
 * ⚠️ DATOS DE PRUEBA: los umbrales de M2 por entorno (ficha M2), para que los
 * mocks pongan el color como lo hará el BACKEND (M2 Evaluator). Los cambios
 * de la Guía de salud se guardan en localStorage (sobreviven a recargar).
 * El frontend real nunca evalúa: esto se borra con el backend de M2.
 */
export type MockEnvironment = 'iss' | 'transit' | 'moon' | 'mars';

/** [verde mín, verde máx, amarillo mín, amarillo máx] */
export type MockBand = [number, number, number, number];

const KEY = 'astrohealth.demo.thresholds.v1';

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

/** La radiación depende del entorno: verde hasta la dosis base, amarillo hasta el doble. */
const RADIATION_BASE: Record<MockEnvironment, number> = {
    iss: 0.5,
    transit: 1.8,
    moon: 1.4,
    mars: 0.6,
};

type Overrides = Partial<
    Record<MockEnvironment, Partial<Record<IndicatorCode, MockBand>>>
>;

function readOverrides(): Overrides {
    try {
        return JSON.parse(localStorage.getItem(KEY) ?? '{}') as Overrides;
    } catch {
        return {};
    }
}

/** Los umbrales vigentes de un entorno (los de la ficha + lo cambiado en la Guía). */
export function mockThresholds(
    env: MockEnvironment = 'mars',
): Record<IndicatorCode, MockBand> {
    const base = RADIATION_BASE[env];

    return {
        ...BASE,
        radiation: [0, base, 0, base * 2],
        ...readOverrides()[env],
    };
}

export function mockSaveThreshold(
    env: MockEnvironment,
    indicator: IndicatorCode,
    band: MockBand,
): void {
    const overrides = readOverrides();
    overrides[env] = { ...overrides[env], [indicator]: band };

    try {
        localStorage.setItem(KEY, JSON.stringify(overrides));
    } catch {
        // Sin localStorage: el cambio no se guarda.
    }
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
