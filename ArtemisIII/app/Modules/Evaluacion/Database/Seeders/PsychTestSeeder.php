<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Database\Seeders;

use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\FindPsychTestRepository;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Psych\UpsertPsychTestRepository;
use Illuminate\Database\Seeder;

/**
 * Los tests de base de la guía psicológica. PHQ-9 y GAD-7 (Pfizer, de uso
 * libre) y PSS-4 (Cohen, uso no comercial), en español; el PVT es una
 * versión corta del test de vigilancia que usa la NASA. Las bandas del
 * PSS-4 y del PVT son referencias de la demo, no puntos de corte clínicos.
 * Ninguno reemplaza la evaluación de un profesional. Idempotente (por código):
 * no pisa lo que el equipo médico ya cambió.
 */
final class PsychTestSeeder extends Seeder
{
    private const FREQUENCY = [
        ['label' => 'Nunca', 'points' => 0],
        ['label' => 'Varios días', 'points' => 1],
        ['label' => 'Más de la mitad de los días', 'points' => 2],
        ['label' => 'Casi todos los días', 'points' => 3],
    ];

    private const TWO_WEEKS = 'Durante las últimas 2 semanas, ¿qué tan seguido ha tenido molestias por los siguientes problemas?';

    public function run(FindPsychTestRepository $find, UpsertPsychTestRepository $upsert): void
    {
        foreach ($this->catalog() as $test) {
            if ($find->findByCode($test['code']) !== null) {
                continue;
            }

            $upsert->upsertByCode($test['code'], $test);
        }
    }

    /**
     * @return array{id: string, text: string, reverse: bool, critical: bool}
     */
    private static function q(string $id, string $text, bool $reverse = false, bool $critical = false): array
    {
        return ['id' => $id, 'text' => $text, 'reverse' => $reverse, 'critical' => $critical];
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function catalog(): array
    {
        return [
            [
                'code' => 'PHQ-9',
                'name' => 'Estado de ánimo (PHQ-9)',
                'kind' => 'psychological',
                'description' => 'Detecta síntomas de depresión: interés, ánimo, sueño, energía y concentración.',
                'instructions' => self::TWO_WEEKS,
                'frequency_days' => 7,
                'unit' => 'puntos',
                'scale' => self::FREQUENCY,
                'questions' => [
                    self::q('phq1', 'Poco interés o placer en hacer cosas'),
                    self::q('phq2', 'Sentirse decaído(a), deprimido(a) o sin esperanzas'),
                    self::q('phq3', 'Dificultad para dormir o permanecer dormido(a), o dormir demasiado'),
                    self::q('phq4', 'Sentirse cansado(a) o con poca energía'),
                    self::q('phq5', 'Sin apetito o comer en exceso'),
                    self::q('phq6', 'Sentirse mal con usted mismo(a), o que es un fracaso o que ha quedado mal con usted o con su familia'),
                    self::q('phq7', 'Dificultad para concentrarse en cosas como leer o ver un video'),
                    self::q('phq8', 'Moverse o hablar tan lento que otros lo notan, o lo contrario: estar muy inquieto(a)'),
                    self::q('phq9', 'Pensamientos de que estaría mejor muerto(a) o de lastimarse de alguna manera', critical: true),
                ],
                'bands' => [
                    ['max' => 4, 'label' => 'Mínimo', 'status' => 'green'],
                    ['max' => 9, 'label' => 'Leve', 'status' => 'yellow'],
                    ['max' => 14, 'label' => 'Moderado', 'status' => 'yellow'],
                    ['max' => 19, 'label' => 'Moderadamente severo', 'status' => 'red'],
                    ['max' => 27, 'label' => 'Severo', 'status' => 'red'],
                ],
                'source' => 'PHQ-9 · Kroenke, Spitzer y Williams (2001), de uso libre',
            ],
            [
                'code' => 'GAD-7',
                'name' => 'Ansiedad (GAD-7)',
                'kind' => 'psychological',
                'description' => 'Mide la ansiedad: nervios, preocupación, irritabilidad y miedo.',
                'instructions' => self::TWO_WEEKS,
                'frequency_days' => 7,
                'unit' => 'puntos',
                'scale' => self::FREQUENCY,
                'questions' => [
                    self::q('gad1', 'Sentirse nervioso(a), ansioso(a) o con los nervios de punta'),
                    self::q('gad2', 'No poder dejar de preocuparse o no poder controlar la preocupación'),
                    self::q('gad3', 'Preocuparse demasiado por diferentes cosas'),
                    self::q('gad4', 'Dificultad para relajarse'),
                    self::q('gad5', 'Estar tan inquieto(a) que es difícil permanecer sentado(a)'),
                    self::q('gad6', 'Molestarse o ponerse irritable fácilmente'),
                    self::q('gad7', 'Sentir miedo, como si algo terrible fuera a pasar'),
                ],
                'bands' => [
                    ['max' => 4, 'label' => 'Mínima', 'status' => 'green'],
                    ['max' => 9, 'label' => 'Leve', 'status' => 'yellow'],
                    ['max' => 14, 'label' => 'Moderada', 'status' => 'yellow'],
                    ['max' => 21, 'label' => 'Severa', 'status' => 'red'],
                ],
                'source' => 'GAD-7 · Spitzer y otros (2006), de uso libre',
            ],
            [
                'code' => 'PSS-4',
                'name' => 'Estrés percibido (PSS-4)',
                'kind' => 'psychological',
                'description' => 'Qué tanto siente que la vida es impredecible, incontrolable o sobrecargada.',
                'instructions' => 'En el último mes, ¿con qué frecuencia…?',
                'frequency_days' => 7,
                'unit' => 'puntos',
                'scale' => [
                    ['label' => 'Nunca', 'points' => 0],
                    ['label' => 'Casi nunca', 'points' => 1],
                    ['label' => 'A veces', 'points' => 2],
                    ['label' => 'Con bastante frecuencia', 'points' => 3],
                    ['label' => 'Muy a menudo', 'points' => 4],
                ],
                'questions' => [
                    self::q('pss1', '…ha sentido que no podía controlar las cosas importantes de su vida?'),
                    self::q('pss2', '…se ha sentido seguro(a) de su capacidad para manejar sus problemas personales?', reverse: true),
                    self::q('pss3', '…ha sentido que las cosas le van bien?', reverse: true),
                    self::q('pss4', '…ha sentido que las dificultades se acumulan tanto que no puede superarlas?'),
                ],
                'bands' => [
                    ['max' => 5, 'label' => 'Estrés bajo', 'status' => 'green'],
                    ['max' => 10, 'label' => 'Estrés moderado', 'status' => 'yellow'],
                    ['max' => 16, 'label' => 'Estrés alto', 'status' => 'red'],
                ],
                'source' => 'PSS-4 · Cohen, Kamarck y Mermelstein (1983)',
            ],
            [
                'code' => 'PVT',
                'name' => 'Atención y reacción (PVT)',
                'kind' => 'cognitive',
                'description' => 'Mide la atención sostenida y la fatiga: qué tan rápido reacciona a una señal.',
                'instructions' => 'Cuando aparezca el círculo, tócalo (o pulsa Espacio) lo más rápido que puedas. Son 8 señales.',
                'frequency_days' => 7,
                'unit' => 'ms',
                'scale' => [],
                'questions' => [],
                'bands' => [
                    ['max' => 300, 'label' => 'Atención normal', 'status' => 'green'],
                    ['max' => 400, 'label' => 'Atención lenta', 'status' => 'yellow'],
                    ['max' => 9999, 'label' => 'Fatiga marcada', 'status' => 'red'],
                ],
                'source' => 'Versión corta del PVT (Dinges y Powell, 1985), usado por la NASA',
            ],
        ];
    }
}
