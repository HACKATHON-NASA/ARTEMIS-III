import type { IndicatorCode } from '@/types/health';

/**
 * Cómo se nombran los indicadores en las gráficas de la tripulación.
 * ⚠️ Cuando exista GET /api/v1/indicators (M1), el nombre y la unidad
 * vienen de ahí.
 */
export type IndicatorMeta = {
    code: IndicatorCode;
    label: string;
    short: string;
    unit: string;
    decimals: number;
};

export const CHART_INDICATORS: IndicatorMeta[] = [
    { code: 'sleep', label: 'Sueño', short: 'Sueño', unit: 'h', decimals: 1 },
    {
        code: 'radiation',
        label: 'Radiación diaria',
        short: 'Radiación',
        unit: 'mSv',
        decimals: 2,
    },
    {
        code: 'bp_sys',
        label: 'Presión sistólica',
        short: 'P. sistólica',
        unit: 'mmHg',
        decimals: 0,
    },
    {
        code: 'bp_dia',
        label: 'Presión diastólica',
        short: 'P. diastólica',
        unit: 'mmHg',
        decimals: 0,
    },
    {
        code: 'hr',
        label: 'Frecuencia cardíaca',
        short: 'Pulso',
        unit: 'bpm',
        decimals: 0,
    },
    {
        code: 'spo2',
        label: 'Oxígeno en sangre',
        short: 'Oxígeno',
        unit: '%',
        decimals: 0,
    },
    {
        code: 'exercise_min',
        label: 'Ejercicio',
        short: 'Ejercicio',
        unit: 'min',
        decimals: 0,
    },
    {
        code: 'mood',
        label: 'Estado de ánimo',
        short: 'Ánimo',
        unit: 'de 5',
        decimals: 0,
    },
    {
        code: 'glucose',
        label: 'Glucosa',
        short: 'Glucosa',
        unit: 'mg/dL',
        decimals: 0,
    },
    {
        code: 'temp',
        label: 'Temperatura',
        short: 'Temp.',
        unit: '°C',
        decimals: 1,
    },
];

export function indicatorMeta(code: IndicatorCode): IndicatorMeta {
    return (
        CHART_INDICATORS.find((item) => item.code === code) ??
        CHART_INDICATORS[0]
    );
}

export function formatValue(value: number, code: IndicatorCode): string {
    const { decimals } = indicatorMeta(code);

    return value.toLocaleString('es', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

/** El color de identidad de cada tripulante, por su orden en la misión (8 como máximo). */
export function seriesColor(index: number): string {
    return `var(--series-${(index % 8) + 1})`;
}
