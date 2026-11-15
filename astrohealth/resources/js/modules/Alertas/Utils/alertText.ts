import { t } from '@/lib/i18n';
import { indicatorName } from '@/lib/indicators';
import {
    formatValue,
    indicatorMeta,
} from '../../Dashboard/Utils/indicatorMeta';
import type { Alert } from '../Types/alerts';
import { helpCategoryLabel } from './alertKinds';

/**
 * El texto de una alerta en el idioma de quien la lee. Se arma con lo que
 * guardó el backend (tipo, indicador, valor, nivel, motivo) y el nombre del
 * tripulante. Si falta algún dato, sale el texto original (`message`).
 */
export function alertMessage(alert: Alert, name: string | null = null): string {
    const first = name?.split(' ')[0] ?? null;

    if (alert.kind === 'vital') {
        if (alert.indicator === null || alert.value === null) {
            return alert.message;
        }

        const unit = indicatorMeta(alert.indicator).unit;

        return t('Alertas.msg.vital', {
            indicator: indicatorName(alert.indicator),
            value: `${formatValue(alert.value, alert.indicator)} ${unit}`.trim(),
            level: t(
                alert.level === 'red'
                    ? 'Alertas.msg.critical'
                    : 'Alertas.msg.caution',
            ),
        });
    }

    const text = first
        ? t(`Alertas.msg.${alert.kind}`, { name: first })
        : t(`Alertas.msg.${alert.kind}Anon`);

    return alert.kind === 'help' && alert.category
        ? t('Alertas.msg.withCategory', {
              text,
              category: helpCategoryLabel(alert.category),
          })
        : text;
}
