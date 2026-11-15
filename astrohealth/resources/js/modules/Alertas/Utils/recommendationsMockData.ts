import type { Recommendation } from '../Types/alerts';

/**
 * ⚠️ DATOS DE PRUEBA de las recomendaciones (M5): qué hacer ante cada valor
 * en amarillo o rojo. Se borran cuando exista GET /api/v1/recommendations.
 */
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
    {
        indicator: 'spo2',
        status: 'yellow',
        title: 'Oxígeno algo bajo',
        action_text:
            'Siéntate y respira despacio por la nariz 2 minutos\nRevisa el sensor del oxímetro y vuelve a medir\nSi baja de 90 %, avisa al médico de la tripulación',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'radiation',
        status: 'yellow',
        title: 'Dosis de radiación alta',
        action_text:
            'Reduce el tiempo en los módulos menos blindados\nRevisa el pronóstico de clima espacial\nLee el dosímetro al final del día',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'glucose',
        status: 'yellow',
        title: 'Glucosa en precaución',
        action_text:
            'Mide de nuevo en ayunas mañana\nRevisa la ración de carbohidratos del día\nMantén el ejercicio de resistencia',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'mood',
        status: 'yellow',
        title: 'Ánimo bajo',
        action_text:
            'Habla con alguien de la tripulación o de Tierra\nToma un descanso fuera de tu estación de trabajo\nRegistra cómo te sientes al final del día',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
];
