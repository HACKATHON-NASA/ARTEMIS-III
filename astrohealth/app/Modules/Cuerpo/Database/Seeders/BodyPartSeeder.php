<?php

declare(strict_types=1);

namespace App\Modules\Cuerpo\Database\Seeders;

use App\Modules\Cuerpo\Infrastructure\Repositorios\BodyPart\UpsertBodyPartRepository;
use Illuminate\Database\Seeder;

/**
 * Las 9 zonas del cuerpo: las 7 del plan más metabolismo (glucosa) y todo
 * el cuerpo (radiación). Los textos de riesgo resumen la NASA HRP
 * (https://www.nasa.gov/hrp/risks/). Se puede correr varias veces.
 */
final class BodyPartSeeder extends Seeder
{
    /** @var list<array{code: string, name: string, function_text: string, space_risk_text: string, hrp_hazard: string, sort_order: int}> */
    private const PARTS = [
        [
            'code' => 'heart',
            'name' => 'Corazón',
            'function_text' => 'Bombea la sangre que lleva oxígeno a todo el cuerpo.',
            'space_risk_text' => 'Sin gravedad, los líquidos suben hacia la cabeza y el corazón se adapta: puede perder fuerza y la presión cambia al volver a tener peso.',
            'hrp_hazard' => 'Gravedad alterada',
            'sort_order' => 1,
        ],
        [
            'code' => 'brain',
            'name' => 'Cerebro y mente',
            'function_text' => 'Controla el cuerpo, el sueño, el ánimo y las decisiones.',
            'space_risk_text' => 'El encierro, la distancia y los ciclos de luz alterados afectan el sueño y el ánimo, y con ellos la concentración.',
            'hrp_hazard' => 'Aislamiento y confinamiento',
            'sort_order' => 2,
        ],
        [
            'code' => 'eyes',
            'name' => 'Ojos',
            'function_text' => 'Permiten ver; dependen de la presión dentro del cráneo.',
            'space_risk_text' => 'El síndrome neuro-ocular (SANS) puede inflamar el nervio óptico y cambiar la visión en misiones largas.',
            'hrp_hazard' => 'Gravedad alterada',
            'sort_order' => 3,
        ],
        [
            'code' => 'lungs',
            'name' => 'Pulmones',
            'function_text' => 'Llevan oxígeno a la sangre y sacan el dióxido de carbono.',
            'space_risk_text' => 'En una nave cerrada, el aire depende de los sistemas de soporte vital: una falla baja el oxígeno en sangre.',
            'hrp_hazard' => 'Ambiente hostil y cerrado',
            'sort_order' => 4,
        ],
        [
            'code' => 'bones',
            'name' => 'Huesos y músculos',
            'function_text' => 'Sostienen el cuerpo y permiten moverse.',
            'space_risk_text' => 'Sin peso que cargar, los huesos pierden densidad y los músculos se atrofian. El ejercicio diario es la defensa.',
            'hrp_hazard' => 'Gravedad alterada',
            'sort_order' => 5,
        ],
        [
            'code' => 'ear',
            'name' => 'Oído interno',
            'function_text' => 'Controla el equilibrio y la orientación.',
            'space_risk_text' => 'Los cambios de gravedad confunden el sistema vestibular: mareo al llegar al espacio y al volver.',
            'hrp_hazard' => 'Gravedad alterada',
            'sort_order' => 6,
        ],
        [
            'code' => 'immune',
            'name' => 'Sistema inmune',
            'function_text' => 'Defiende al cuerpo de infecciones.',
            'space_risk_text' => 'El estrés y la radiación debilitan las defensas y pueden reactivar virus latentes.',
            'hrp_hazard' => 'Ambiente hostil y cerrado',
            'sort_order' => 7,
        ],
        [
            'code' => 'metabolism',
            'name' => 'Metabolismo',
            'function_text' => 'Convierte el alimento en la energía del cuerpo.',
            'space_risk_text' => 'En vuelo cambia el manejo del azúcar; los datos reales de Inspiration4 (NASA OSDR) lo muestran.',
            'hrp_hazard' => 'Gravedad alterada',
            'sort_order' => 8,
        ],
        [
            'code' => 'whole_body',
            'name' => 'Todo el cuerpo',
            'function_text' => 'La dosis de radiación afecta a todas las células.',
            'space_risk_text' => 'Fuera de la protección de la Tierra, la radiación aumenta el riesgo de cáncer y daño en tejidos. Se vigila la dosis diaria.',
            'hrp_hazard' => 'Radiación espacial',
            'sort_order' => 9,
        ],
    ];

    public function run(UpsertBodyPartRepository $upsert): void
    {
        foreach (self::PARTS as $part) {
            $upsert->upsertByCode($part);
        }
    }
}
