import { mockBodyStatusFor } from '@/mocks/crewHealth';
import type { BodyPart, BodyStatus } from '../Types/body';

/**
 * ⚠️ DATOS DE PRUEBA de M3 · Cuerpo. Se borran cuando existan
 * GET /api/v1/body-parts y GET /api/v1/astronauts/{id}/body-status.
 * Los textos de riesgo resumen la NASA HRP (https://www.nasa.gov/hrp/risks/).
 */

export const MOCK_PARTS: BodyPart[] = [
    {
        code: 'heart',
        name: 'Corazón',
        indicators: ['hr', 'bp_sys', 'bp_dia'],
        hrp_hazard: 'Gravedad alterada',
        function_text: 'Bombea la sangre que lleva oxígeno a todo el cuerpo.',
        space_risk_text:
            'Sin gravedad, los líquidos suben hacia la cabeza y el corazón se adapta: puede perder fuerza y la presión cambia al volver a tener peso.',
    },
    {
        code: 'brain',
        name: 'Cerebro y mente',
        indicators: ['sleep', 'mood'],
        hrp_hazard: 'Aislamiento y confinamiento',
        function_text:
            'Controla el cuerpo, el sueño, el ánimo y las decisiones.',
        space_risk_text:
            'El encierro, la distancia y los ciclos de luz alterados afectan el sueño y el ánimo, y con ellos la concentración.',
    },
    {
        code: 'eyes',
        name: 'Ojos',
        indicators: [],
        hrp_hazard: 'Gravedad alterada',
        function_text:
            'Permiten ver; dependen de la presión dentro del cráneo.',
        space_risk_text:
            'El síndrome neuro-ocular (SANS) puede inflamar el nervio óptico y cambiar la visión en misiones largas.',
    },
    {
        code: 'lungs',
        name: 'Pulmones',
        indicators: ['spo2'],
        hrp_hazard: 'Ambiente hostil y cerrado',
        function_text:
            'Llevan oxígeno a la sangre y sacan el dióxido de carbono.',
        space_risk_text:
            'En una nave cerrada, el aire depende de los sistemas de soporte vital: una falla baja el oxígeno en sangre.',
    },
    {
        code: 'bones',
        name: 'Huesos y músculos',
        indicators: ['exercise_min'],
        hrp_hazard: 'Gravedad alterada',
        function_text: 'Sostienen el cuerpo y permiten moverse.',
        space_risk_text:
            'Sin peso que cargar, los huesos pierden densidad y los músculos se atrofian. El ejercicio diario es la defensa.',
    },
    {
        code: 'ear',
        name: 'Oído interno',
        indicators: [],
        hrp_hazard: 'Gravedad alterada',
        function_text: 'Controla el equilibrio y la orientación.',
        space_risk_text:
            'Los cambios de gravedad confunden el sistema vestibular: mareo al llegar al espacio y al volver.',
    },
    {
        code: 'immune',
        name: 'Sistema inmune',
        indicators: ['temp'],
        hrp_hazard: 'Ambiente hostil y cerrado',
        function_text: 'Defiende al cuerpo de infecciones.',
        space_risk_text:
            'El estrés y la radiación debilitan las defensas y pueden reactivar virus latentes.',
    },
    {
        code: 'metabolism',
        name: 'Metabolismo',
        indicators: ['glucose'],
        hrp_hazard: 'Gravedad alterada',
        function_text: 'Convierte el alimento en la energía del cuerpo.',
        space_risk_text:
            'En vuelo cambia el manejo del azúcar; los datos reales de Inspiration4 (NASA OSDR) lo muestran.',
    },
    {
        code: 'whole_body',
        name: 'Todo el cuerpo',
        indicators: ['radiation'],
        hrp_hazard: 'Radiación espacial',
        function_text: 'La dosis de radiación afecta a todas las células.',
        space_risk_text:
            'Fuera de la protección de la Tierra, la radiación aumenta el riesgo de cáncer y daño en tejidos. Se vigila la dosis diaria.',
    },
];

/** El estado del cuerpo de cada astronauta (datos de prueba compartidos: @/mocks/crewHealth). */
export function mockBodyStatus(astronautId: number): BodyStatus {
    return mockBodyStatusFor(astronautId);
}
