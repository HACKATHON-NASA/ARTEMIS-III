<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application;

use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Achievement\FindAchievementRepository;
use Illuminate\Contracts\Auth\Access\Gate;

/**
 * Caso de uso: el ranking de una tripulación. Solo tripulantes de misiones
 * que la persona puede ver (Gate mission.access): un astronauta ve el de su
 * misión, sin datos de salud (solo puntos, nivel, racha e insignias).
 */
final readonly class GetLeaderboard
{
    public function __construct(
        private GetProfile $profiles,
        private CrewQuery $crew,
        private FindAchievementRepository $achievements,
        private Gate $gate,
    ) {}

    /**
     * @param  list<int>  $astronautIds
     * @return list<array{astronaut_id: int, points: int, level: int, streak_days: int, badges: int}>
     */
    public function execute(array $astronautIds): array
    {
        $rows = [];

        foreach ($astronautIds as $id) {
            $missionId = $this->crew->missionOf($id);

            if ($missionId === null || ! $this->gate->allows('mission.access', [$missionId])) {
                continue;
            }

            $profile = $this->profiles->execute($id, withBadges: false);
            $rows[] = [
                'astronaut_id' => $id,
                'points' => $profile['points'],
                'level' => $profile['level'],
                'streak_days' => $profile['streak_days'],
                'badges' => count($this->achievements->earnedBy($id)),
            ];
        }

        usort($rows, fn (array $a, array $b): int => $b['points'] <=> $a['points']);

        return $rows;
    }
}
