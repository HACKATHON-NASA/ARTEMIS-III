import { formatNumber, t } from '@/lib/i18n';
import { indicatorName, indicatorShort } from '@/lib/indicators';
import type { IndicatorCode } from '@/types/health';

/**
 * Cómo se nombran los indicadores en las gráficas de la tripulación. El
 * nombre sale del idioma de ahora (locales → indicators); aquí van la
 * unidad y los decimales de cada gráfica.
 */
export type IndicatorMeta = {
    code: IndicatorCode;
    readonly label: string;
    readonly short: string;
    readonly unit: string;
    decimals: number;
};

function meta(
    code: IndicatorCode,
    unit: string,
    decimals: number,
): IndicatorMeta {
    return {
        code,
        decimals,
        get label() {
            return indicatorName(code);
        },
        get short() {
            return indicatorShort(code);
        },
        // El ánimo se mide "de 5": esa unidad también se traduce.
        get unit() {
            return code === 'mood' ? t('Dashboard.outOf5') : unit;
        },
    };
}

export const CHART_INDICATORS: IndicatorMeta[] = [
    meta('sleep', 'h', 1),
    meta('radiation', 'mSv', 2),
    meta('bp_sys', 'mmHg', 0),
    meta('bp_dia', 'mmHg', 0),
    meta('hr', 'bpm', 0),
    meta('spo2', '%', 0),
    meta('exercise_min', 'min', 0),
    meta('mood', '', 0),
    meta('glucose', 'mg/dL', 0),
    meta('temp', '°C', 1),
];

export function indicatorMeta(code: IndicatorCode): IndicatorMeta {
    return (
        CHART_INDICATORS.find((item) => item.code === code) ??
        CHART_INDICATORS[0]
    );
}

export function formatValue(value: number, code: IndicatorCode): string {
    const { decimals } = indicatorMeta(code);

    return formatNumber(value, {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals,
    });
}

/** El color de identidad de cada tripulante, por su orden en la misión (8 como máximo). */
export function seriesColor(index: number): string {
    return `var(--series-${(index % 8) + 1})`;
}
