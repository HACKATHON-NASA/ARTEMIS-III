import { indicatorName } from '@/lib/indicators';
import type { IndicatorCode } from '@/types/health';

/**
 * El nombre de cada indicador en el idioma de ahora (para el tooltip del
 * cuerpo, el detalle de una zona y el plan del día).
 */
export function indicatorLabel(code: IndicatorCode): string {
    return indicatorName(code);
}
