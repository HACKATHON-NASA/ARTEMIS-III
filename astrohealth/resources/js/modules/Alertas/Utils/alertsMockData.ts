import type { Alert, Recommendation } from '../Types/alerts';

/**
 * ⚠️ DATOS DE PRUEBA de M5 · Alertas (el ejemplo de la ficha: presión 148/94
 * en Marte). Se borran cuando existan las rutas de alertas y recomendaciones.
 */
export function mockAlerts(astronautId: number): Alert[] {
    return [
        {
            id: 57,
            astronaut_id: astronautId,
            indicator: 'bp_sys',
            level: 'red',
            message: 'Presión sistólica 148 mmHg (crítico)',
            sent_to_ground: false,
            sent_at: null,
            ground_eta_at: null,
            delay_seconds: 1200,
            acknowledged_at: null,
            created_at: new Date(Date.now() - 6 * 60_000).toISOString(),
        },
    ];
}

export const MOCK_RECOMMENDATIONS: Recommendation[] = [
    {
        indicator: 'bp_sys',
        status: 'red',
        title: 'Presión alta',
        action_text:
            'Reposa 10 minutos, sentado y sin hablar\nHidrátate: un vaso de agua\nVuelve a medir la presión\nSi sigue en rojo, envía la alerta a Tierra',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'bp_dia',
        status: 'red',
        title: 'Presión alta',
        action_text:
            'Reposa 10 minutos, sentado y sin hablar\nHidrátate: un vaso de agua\nVuelve a medir la presión',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'sleep',
        status: 'yellow',
        title: 'Poco sueño',
        action_text:
            'Apaga pantallas 1 hora antes de dormir\nUsa el antifaz y los tapones\nMantén el mismo horario de descanso',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'exercise_min',
        status: 'yellow',
        title: 'Falta ejercicio',
        action_text:
            'Completa la sesión de resistencia de hoy\nNo bajes de 90 minutos diarios\nRegistra el tiempo al terminar',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
];
