<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Database\Seeders;

use App\Modules\Alertas\Application\Servicios\CrewScope;
use App\Modules\Alertas\Domain\Reglas\GroundLink;
use App\Modules\Alertas\Infrastructure\Models\Advice;
use App\Modules\Alertas\Infrastructure\Models\Alert;
use App\Modules\Alertas\Infrastructure\Repositorios\Advice\InsertAdviceRepository;
use App\Modules\Alertas\Infrastructure\Repositorios\Alert\InsertAlertRepository;
use App\Modules\Entorno\Contratos\CrewMember;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

/**
 * El historial de alertas e indicaciones de Ares-1 para la demo, coherente
 * con los últimos valores (DemoReadingsSeeder) y las notas clínicas. Solo
 * fuera de producción y solo si las tablas están vacías.
 */
final class DemoAlertsSeeder extends Seeder
{
    private const DOCTOR = 'Dra. Sofía Ramírez';

    /** [tripulante, tipo, indicador, valor, nivel, mensaje, nota, origen, minutos atrás] */
    private const ALERTS = [
        [0, 'vital', 'bp_sys', 148, 'red', 'Presión sistólica 148 mmHg (crítico)', null, 'device', 6],
        [1, 'report', null, null, 'info', 'Novedad de Kenji', 'Dolor de cabeza leve desde que me levanté. Ya tomé agua.', 'astronaut', 26],
        [2, 'vital', 'spo2', 93, 'yellow', 'Saturación de oxígeno 93 % (precaución)', null, 'device', 70],
        [2, 'help', null, null, 'yellow', 'Amara pide ayuda · Síntoma o malestar', 'Me falta un poco el aire al hacer ejercicio.', 'astronaut', 180],
        [0, 'vital', 'sleep', 6.2, 'yellow', 'Sueño 6,2 h (precaución)', null, 'device', 540],
        [1, 'vital', 'mood', 3, 'yellow', 'Estado de ánimo 3 de 5 (precaución)', null, 'device', 1800],
        [2, 'vital', 'radiation', 0.94, 'yellow', 'Radiación diaria 0,94 mSv (precaución)', null, 'device', 3000],
    ];

    /** Las que Tierra ya atendió: posición en ALERTS → [minutos atrás, respuesta]. */
    private const REPLIES = [
        3 => [120, 'Recibido. Baja la intensidad y mide el oxígeno antes y después.'],
        4 => [480, 'Sigue la rutina de sueño de esta noche.'],
        5 => [1740, 'Agendamos una llamada con tu familia.'],
        6 => [2940, 'Reduce el tiempo en el módulo menos blindado.'],
    ];

    /** [tripulante, tema, título, pasos, minutos atrás, leída, importante, días de vigencia] */
    private const ADVICE = [
        [0, 'medication', 'Control de la presión', "Mide la presión mañana y tarde durante 3 días\nReduce la sal de las raciones\nSi pasa de 160, avisa enseguida", 90, false, true, 3],
        [1, 'sleep', 'Higiene del sueño', "Apaga pantallas 1 h antes de dormir\nUsa el antifaz y los tapones\nMantén la misma hora de acostarte", 1560, true, false, 7],
        [2, 'exercise', 'Ejercicio con oxímetro', "Baja la intensidad de la bicicleta un 20 %\nMide el oxígeno antes y después", 3000, true, false, 5],
    ];

    public function run(CrewScope $scope, InsertAlertRepository $alerts, InsertAdviceRepository $advice): void
    {
        if (app()->isProduction()) {
            return;
        }

        $all = $scope->all();
        $crew = array_values(array_filter($all, fn (CrewMember $member): bool => $member->missionId === ($all[0]->missionId ?? 0)));
        $now = CarbonImmutable::now();

        if (! Alert::query()->exists()) {
            foreach (self::ALERTS as $position => [$index, $kind, $indicator, $value, $level, $message, $note, $source, $minutesAgo]) {
                $member = $crew[$index] ?? null;
                $ack = self::REPLIES[$position] ?? null;

                if ($member === null) {
                    continue;
                }

                $delay = $scope->delayFor($member->id);
                $sentAt = $now->subMinutes($minutesAgo);
                $alerts->insert([
                    'astronaut_id' => $member->id,
                    'kind' => $kind,
                    'indicator_code' => $indicator,
                    'value' => $value,
                    'level' => $level,
                    'message' => $message,
                    'category' => $kind === 'help' ? 'Síntoma o malestar' : null,
                    'note' => $note,
                    'source' => $source,
                    'delay_seconds' => $delay,
                    'sent_at' => $sentAt,
                    'ground_eta_at' => GroundLink::arrival($sentAt, $delay),
                    'acknowledged_at' => $ack === null ? null : $now->subMinutes($ack[0]),
                    'acknowledged_by_name' => $ack === null ? null : self::DOCTOR,
                    'reply' => $ack === null ? null : $ack[1],
                ]);
            }
        }

        if (! Advice::query()->exists()) {
            foreach (self::ADVICE as [$index, $category, $title, $body, $minutesAgo, $read, $important, $validDays]) {
                $member = $crew[$index] ?? null;

                if ($member === null) {
                    continue;
                }

                $delay = $scope->delayFor($member->id);
                $sentAt = $now->subMinutes($minutesAgo);
                $advice->insert([
                    'astronaut_id' => $member->id,
                    'author_id' => null,
                    'author_name' => self::DOCTOR,
                    'category' => $category,
                    'title' => $title,
                    'body' => $body,
                    'important' => $important,
                    'valid_days' => $validDays,
                    'delay_seconds' => $delay,
                    'sent_at' => $sentAt,
                    'arrives_at' => GroundLink::arrival($sentAt, $delay),
                    'read_at' => $read ? $sentAt->addMinutes(30) : null,
                ]);
            }
        }
    }
}
