<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Database\Seeders;

use App\Modules\Gamificacion\Infrastructure\Repositorios\Achievement\UpsertAchievementRepository;
use Illuminate\Database\Seeder;

/**
 * Las 9 insignias: cómo se ganan, su bono y su meta. Se puede correr varias veces.
 */
final class AchievementSeeder extends Seeder
{
    /** @var list<array{code: string, name: string, icon: string, condition: string, bonus: int, target: int}> */
    private const BADGES = [
        ['code' => 'first_log', 'name' => 'Primer registro', 'icon' => '🚀', 'condition' => 'Registra tu primera medición', 'bonus' => 5, 'target' => 1],
        ['code' => 'orbit_7', 'name' => '7 días en órbita', 'icon' => '🛰️', 'condition' => 'Racha de 7 días con el registro completo', 'bonus' => 20, 'target' => 7],
        ['code' => 'steel_heart', 'name' => 'Corazón de acero', 'icon' => '❤️', 'condition' => '7 días seguidos con pulso y presión en verde', 'bonus' => 20, 'target' => 7],
        ['code' => 'space_sleeper', 'name' => 'Dormilón espacial', 'icon' => '😴', 'condition' => '7 noches seguidas de 7 h o más', 'bonus' => 20, 'target' => 7],
        ['code' => 'on_time', 'name' => 'Puntual', 'icon' => '⏰', 'condition' => '10 recordatorios cumplidos', 'bonus' => 15, 'target' => 10],
        ['code' => 'mind_check', 'name' => 'Mente al día', 'icon' => '🧠', 'condition' => 'Haz los 4 tests psicológicos de una semana', 'bonus' => 15, 'target' => 4],
        ['code' => 'iron_body', 'name' => 'Cuerpo de hierro', 'icon' => '💪', 'condition' => '14 días con 90 min de ejercicio o más', 'bonus' => 30, 'target' => 14],
        ['code' => 'swiss_clock', 'name' => 'Reloj suizo', 'icon' => '⌚', 'condition' => '100 recordatorios cumplidos', 'bonus' => 40, 'target' => 100],
        ['code' => 'mission_30', 'name' => 'Misión completa', 'icon' => '🏁', 'condition' => 'Racha de 30 días con el registro completo', 'bonus' => 100, 'target' => 30],
    ];

    public function run(UpsertAchievementRepository $upsert): void
    {
        foreach (self::BADGES as $index => $badge) {
            $upsert->upsertByCode([...$badge, 'sort_order' => $index + 1]);
        }
    }
}
