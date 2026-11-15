import { computed, reactive } from 'vue';
import type { Ref } from 'vue';
import type { IndicatorCode } from '@/types/health';
import type { Indicator, RecordPayload } from '../Types/measurement';

/**
 * El estado del formulario de registro: un valor por indicador (texto, como
 * lo escribe la persona), los errores y el payload con SOLO los campos
 * llenos (criterio 7 de la ficha M1). Valida el rango posible, que solo
 * atrapa errores de digitación: el color lo decide el backend.
 */
export function useMeasurementForm(indicators: Ref<Indicator[]>) {
    const values = reactive<Partial<Record<IndicatorCode, string>>>({});
    const errors = reactive<Partial<Record<IndicatorCode, string>>>({});

    const filled = computed(() =>
        indicators.value.filter(
            (indicator) => (values[indicator.code] ?? '').trim() !== '',
        ),
    );

    function parse(raw: string): number {
        return Number(raw.trim().replace(',', '.'));
    }

    function check(indicator: Indicator): string | null {
        const raw = (values[indicator.code] ?? '').trim();

        if (raw === '') {
            return null;
        }

        const value = parse(raw);

        if (Number.isNaN(value)) {
            return 'Escribe solo números.';
        }

        if (value < indicator.min || value > indicator.max) {
            return `Revisa el número: debe estar entre ${indicator.min} y ${indicator.max} ${indicator.unit}.`;
        }

        return null;
    }

    /** Valida un campo al salir de él (y borra el error al corregirlo). */
    function validateField(indicator: Indicator): void {
        const error = check(indicator);

        if (error) {
            errors[indicator.code] = error;
        } else {
            delete errors[indicator.code];
        }
    }

    /** Valida todo; devuelve true si se puede enviar. */
    function validate(): boolean {
        indicators.value.forEach(validateField);

        return filled.value.length > 0 && Object.keys(errors).length === 0;
    }

    function payload(measuredAt: string | null): RecordPayload {
        return {
            ...(measuredAt ? { measured_at: measuredAt } : {}),
            measurements: filled.value.map((indicator) => ({
                indicator: indicator.code,
                value: parse(values[indicator.code] ?? ''),
            })),
        };
    }

    function reset(): void {
        for (const code of Object.keys(values) as IndicatorCode[]) {
            delete values[code];
            delete errors[code];
        }
    }

    return {
        values,
        errors,
        filled,
        validateField,
        validate,
        payload,
        reset,
    };
}
