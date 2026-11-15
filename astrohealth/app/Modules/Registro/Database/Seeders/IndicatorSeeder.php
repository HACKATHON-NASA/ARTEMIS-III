<?php

declare(strict_types=1);

namespace App\Modules\Registro\Database\Seeders;

use App\Modules\Registro\Infrastructure\Repositorios\Indicator\UpsertIndicatorRepository;
use Illuminate\Database\Seeder;

/**
 * El catálogo de los 10 indicadores: unidad, zona del cuerpo, rango
 * físicamente posible (solo rechaza errores de digitación, no es clínico)
 * y el "¿Cómo me mido?". Textos simples basados en la NASA HRP
 * (https://www.nasa.gov/hrp/risks/). Se puede correr varias veces.
 */
final class IndicatorSeeder extends Seeder
{
    /** @var list<array{code: string, name: string, unit: string, body_part: string, min_possible: int|float, max_possible: int|float, step: int|float, sort_order: int, help_how: string, help_why: string}> */
    private const INDICATORS = [
        [
            'code' => 'hr',
            'name' => 'Frecuencia cardíaca',
            'unit' => 'bpm',
            'body_part' => 'heart',
            'min_possible' => 20,
            'max_possible' => 250,
            'step' => 1,
            'sort_order' => 1,
            'help_how' => 'En reposo, sentado 5 minutos. Ponte el sensor en el dedo o la muñeca y anota el número cuando se quede quieto.',
            'help_why' => 'Sin gravedad el corazón trabaja distinto y puede perder fuerza. El pulso en reposo muestra cómo se está adaptando.',
        ],
        [
            'code' => 'bp_sys',
            'name' => 'Presión sistólica',
            'unit' => 'mmHg',
            'body_part' => 'heart',
            'min_possible' => 50,
            'max_possible' => 260,
            'step' => 1,
            'sort_order' => 2,
            'help_how' => 'Brazalete en el brazo izquierdo, a la altura del corazón, sin hablar. Es el número de arriba (el más alto).',
            'help_why' => 'En el espacio los líquidos suben hacia la cabeza y la presión cambia. Una presión alta sostenida daña el corazón y los vasos.',
        ],
        [
            'code' => 'bp_dia',
            'name' => 'Presión diastólica',
            'unit' => 'mmHg',
            'body_part' => 'heart',
            'min_possible' => 30,
            'max_possible' => 160,
            'step' => 1,
            'sort_order' => 3,
            'help_how' => 'Sale en la misma medición del brazalete: es el número de abajo (el más bajo).',
            'help_why' => 'Es la presión cuando el corazón descansa entre latidos. Junto con la sistólica dice si la circulación está bien.',
        ],
        [
            'code' => 'spo2',
            'name' => 'Saturación de oxígeno',
            'unit' => '%',
            'body_part' => 'lungs',
            'min_possible' => 50,
            'max_possible' => 100,
            'step' => 1,
            'sort_order' => 4,
            'help_how' => 'Oxímetro en el dedo índice, con la mano quieta y caliente. Espera 30 segundos a que el número se estabilice.',
            'help_why' => 'En la nave el aire lo producen las máquinas. Si el oxígeno en sangre baja, puede ser el cuerpo o el soporte vital.',
        ],
        [
            'code' => 'temp',
            'name' => 'Temperatura',
            'unit' => '°C',
            'body_part' => 'immune',
            'min_possible' => 30,
            'max_possible' => 45,
            'step' => 0.1,
            'sort_order' => 5,
            'help_how' => 'Termómetro en la frente o bajo la lengua, antes de comer o hacer ejercicio.',
            'help_why' => 'El estrés y la radiación debilitan las defensas. La fiebre es la primera señal de una infección en un lugar cerrado.',
        ],
        [
            'code' => 'glucose',
            'name' => 'Glucosa (ayunas)',
            'unit' => 'mg/dL',
            'body_part' => 'metabolism',
            'min_possible' => 20,
            'max_possible' => 600,
            'step' => 1,
            'sort_order' => 6,
            'help_how' => 'En ayunas, al despertar. Limpia el dedo, pincha a un lado de la yema y pon la gota en la tira del glucómetro.',
            'help_why' => 'En vuelo cambia cómo el cuerpo maneja el azúcar; los datos de la misión Inspiration4 (NASA OSDR) lo muestran.',
        ],
        [
            'code' => 'sleep',
            'name' => 'Sueño',
            'unit' => 'h',
            'body_part' => 'brain',
            'min_possible' => 0,
            'max_possible' => 24,
            'step' => 0.1,
            'sort_order' => 7,
            'help_how' => 'Las horas que dormiste anoche, según tu reloj o lo que recuerdes. 6 h y 30 min se escriben 6.5.',
            'help_why' => 'Sin día ni noche naturales el sueño se desordena, y con él la concentración y las decisiones.',
        ],
        [
            'code' => 'mood',
            'name' => 'Estado de ánimo',
            'unit' => '1-5',
            'body_part' => 'brain',
            'min_possible' => 1,
            'max_possible' => 5,
            'step' => 1,
            'sort_order' => 8,
            'help_how' => 'Elige la carita que más se parece a cómo te sientes hoy. No hay respuesta correcta.',
            'help_why' => 'El aislamiento y la distancia de casa pesan. Notar a tiempo un ánimo bajo ayuda a toda la tripulación.',
        ],
        [
            'code' => 'exercise_min',
            'name' => 'Ejercicio',
            'unit' => 'min',
            'body_part' => 'bones',
            'min_possible' => 0,
            'max_possible' => 600,
            'step' => 1,
            'sort_order' => 9,
            'help_how' => 'Suma los minutos de bicicleta, cinta y máquina de resistencia de hoy.',
            'help_why' => 'Sin peso que cargar, los huesos pierden densidad y los músculos se atrofian. El ejercicio diario es la defensa.',
        ],
        [
            'code' => 'radiation',
            'name' => 'Radiación diaria',
            'unit' => 'mSv',
            'body_part' => 'whole_body',
            'min_possible' => 0,
            'max_possible' => 100,
            'step' => 0.01,
            'sort_order' => 10,
            'help_how' => 'Lee la dosis del día en tu dosímetro personal (el que llevas en el pecho) al final de la jornada.',
            'help_why' => 'Fuera de la protección de la Tierra la radiación aumenta el riesgo de cáncer y daño en tejidos. Se vigila día a día.',
        ],
    ];

    public function run(UpsertIndicatorRepository $upsert): void
    {
        foreach (self::INDICATORS as $indicator) {
            $upsert->upsertByCode($indicator);
        }
    }
}
