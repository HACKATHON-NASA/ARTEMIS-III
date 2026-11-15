import { Activity, LifeBuoy, MessageSquareText, Siren } from '@lucide/vue';
import type { Component } from 'vue';
import type { AlertKind } from '../Types/alerts';

/**
 * Cómo se ve y se nombra cada tipo de alerta, los motivos para pedir ayuda
 * y las respuestas rápidas de Tierra.
 */
export const KIND_META: Record<AlertKind, { label: string; icon: Component }> =
    {
        vital: { label: 'Signos vitales', icon: Activity },
        sos: { label: 'SOS', icon: Siren },
        help: { label: 'Pide ayuda', icon: LifeBuoy },
        report: { label: 'Novedad', icon: MessageSquareText },
    };

export const HELP_CATEGORIES = [
    'Síntoma o malestar',
    'Lesión o golpe',
    'Ánimo o estrés',
    'Medicamento',
    'Otro',
];

export const QUICK_REPLIES = [
    'Recibido. El médico de vuelo te contacta en la próxima ventana.',
    'Sigue los pasos de la recomendación y vuelve a medir.',
    'Reposa y avísanos si empeora.',
    'Activa el protocolo de emergencia con el médico de la tripulación.',
];
