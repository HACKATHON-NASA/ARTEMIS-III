import type { Recommendation } from '../Types/alerts';

/**
 * ⚠️ DATOS DE PRUEBA de las recomendaciones (M5): qué hacer ante cada valor
 * en amarillo o rojo. Se borran cuando exista GET /api/v1/recommendations.
 */
export const MOCK_RECOMMENDATIONS: Recommendation[] = [
    // Las 12 que completan las 20 de la ficha M5 (cada indicador en amarillo y en rojo).
    {
        indicator: 'hr',
        status: 'yellow',
        title: 'Pulso fuera de lo habitual',
        action_text:
            'Siéntate y descansa 5 minutos\nRespira despacio y vuelve a medir\nEvita el café hasta la próxima medición',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'hr',
        status: 'red',
        title: 'Pulso muy alto o muy bajo',
        action_text:
            'Deja lo que estés haciendo y siéntate\nAvisa al médico de la tripulación\nSi hay mareo o dolor en el pecho, envía SOS',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'bp_sys',
        status: 'yellow',
        title: 'Presión algo alta',
        action_text:
            'Reposa 10 minutos y vuelve a medir\nReduce la sal en las comidas de hoy\nMantén el ejercicio suave',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'bp_dia',
        status: 'yellow',
        title: 'Presión algo alta',
        action_text:
            'Reposa 10 minutos y vuelve a medir\nHidrátate con un vaso de agua\nRegistra cómo dormiste anoche',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'glucose',
        status: 'red',
        title: 'Glucosa fuera de rango',
        action_text:
            'Vuelve a medir con una tira nueva\nSi está baja, toma un jugo y vuelve a medir en 15 min\nAvisa al médico de la tripulación',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'temp',
        status: 'yellow',
        title: 'Temperatura un poco alta',
        action_text:
            'Descansa y bebe líquidos\nVuelve a medir en 1 hora\nAnota si tienes otros síntomas',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'temp',
        status: 'red',
        title: 'Fiebre',
        action_text:
            'Aíslate del resto de la tripulación si puedes\nAvisa al médico de la tripulación\nPide ayuda a Tierra con tus síntomas',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'spo2',
        status: 'red',
        title: 'Oxígeno bajo',
        action_text:
            'Revisa el soporte vital del módulo\nRespira lento y profundo, sentado\nSi sigue bajo de 90 %, envía SOS',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'sleep',
        status: 'red',
        title: 'Muy poco sueño',
        action_text:
            'Evita tareas críticas si puedes\nToma una siesta de 20 minutos\nAvisa al médico para ajustar tu turno',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'mood',
        status: 'red',
        title: 'Ánimo muy bajo',
        action_text:
            'Habla hoy con alguien de confianza\nPide una llamada con psicología de Tierra\nSi tienes pensamientos de hacerte daño, envía SOS',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'radiation',
        status: 'red',
        title: 'Radiación muy alta',
        action_text:
            'Ve al refugio blindado de la nave\nRegistra la lectura del dosímetro\nEspera las indicaciones de Tierra',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'exercise_min',
        status: 'red',
        title: 'Casi sin ejercicio',
        action_text:
            'Haz al menos 30 minutos de resistencia hoy\nPide ayuda para reorganizar tu turno\nRegistra el tiempo al terminar',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
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

const KEY = 'astrohealth.demo.recommendations.v1';

/** Las recomendaciones con lo editado en la Guía de salud (guardado en este navegador). */
export function mockRecommendations(): Recommendation[] {
    let edited: Recommendation[] = [];

    try {
        edited = JSON.parse(
            localStorage.getItem(KEY) ?? '[]',
        ) as Recommendation[];
    } catch {
        edited = [];
    }

    const same = (a: Recommendation, b: Recommendation) =>
        a.indicator === b.indicator && a.status === b.status;

    return [
        ...MOCK_RECOMMENDATIONS.filter(
            (item) => !edited.some((other) => same(item, other)),
        ),
        ...edited,
    ];
}

export function mockSaveRecommendation(recommendation: Recommendation): void {
    const others = mockRecommendations().filter(
        (item) =>
            item.indicator !== recommendation.indicator ||
            item.status !== recommendation.status,
    );
    const edited = [...others, recommendation].filter(
        (item) => !MOCK_RECOMMENDATIONS.includes(item),
    );

    try {
        localStorage.setItem(KEY, JSON.stringify(edited));
    } catch {
        // Sin localStorage: el cambio no se guarda.
    }
}
