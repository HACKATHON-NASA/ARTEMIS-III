import { Activity, LifeBuoy, MessageSquareText, Siren } from '@lucide/vue';
import type { Component } from 'vue';
import { t } from '@/lib/i18n';
import type { AlertKind } from '../Types/alerts';

/**
 * Cómo se ve y se nombra cada tipo de alerta, los motivos para pedir ayuda
 * y las respuestas rápidas de Tierra (en el idioma de ahora).
 */
type KindInfo = { icon: Component; readonly label: string };

function kind(code: AlertKind, icon: Component): KindInfo {
    return {
        icon,
        get label() {
            return t(`Alertas.kinds.${code}`);
        },
    };
}

export const KIND_META: Record<AlertKind, KindInfo> = {
    vital: kind('vital', Activity),
    sos: kind('sos', Siren),
    help: kind('help', LifeBuoy),
    report: kind('report', MessageSquareText),
};

/**
 * Los motivos para pedir ayuda. Lo que se guarda es el valor de la
 * izquierda (así ya están en la base); en pantalla sale traducido.
 */
const HELP_KEYS: Record<string, string> = {
    'Síntoma o malestar': 'symptom',
    'Lesión o golpe': 'injury',
    'Ánimo o estrés': 'mood',
    Medicamento: 'medication',
    Otro: 'other',
};

export const HELP_CATEGORIES = Object.keys(HELP_KEYS);

/** Un motivo en el idioma de ahora (uno que no se conoce sale tal cual). */
export function helpCategoryLabel(value: string): string {
    const key = HELP_KEYS[value];

    return key ? t(`Alertas.helpCategories.${key}`) : value;
}

/** Las respuestas rápidas de Tierra, en el idioma de quien responde. */
export function quickReplies(): string[] {
    return [0, 1, 2, 3].map((index) => t(`Alertas.quickReplies.${index}`));
}
