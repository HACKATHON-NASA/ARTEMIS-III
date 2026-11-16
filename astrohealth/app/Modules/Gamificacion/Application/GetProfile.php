<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application;

use App\Modules\Gamificacion\Application\Servicios\BadgeFactsBuilder;
use App\Modules\Gamificacion\Application\Servicios\MissionClock;
use App\Modules\Gamificacion\Domain\Entidades\PointEntry;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Domain\Reglas\BadgeRule;
use App\Modules\Gamificacion\Domain\Reglas\LevelRule;
use App\Modules\Gamificacion\Domain\Reglas\StreakRule;
use App\Modules\Gamificacion\Infrastructure\Configuracion\GamificationSettings;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Achievement\FindAchievementRepository;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Points\FindPointsRepository;

/**
 * Caso de uso: los puntos, el nivel, la racha, las insignias (con su avance)
 * y lo último que sumó un tripulante.
 */
final readonly class GetProfile
{
    public function __construct(
        private FindPointsRepository $findPoints,
        private FindAchievementRepository $findAchievements,
        private BadgeFactsBuilder $facts,
        private MissionClock $clock,
        private GamificationSettings $settings,
    ) {}

    /**
     * @return array{astronaut_id: int, points: int, level: int, level_progress: int, level_size: int, streak_days: int, badges: list<array<string, mixed>>, recent: list<PointEntry>, earning: list<array{reason: string, points: int}>}
     */
    public function execute(int $astronautId, bool $withBadges = true): array
    {
        $log = $this->findPoints->ofAstronaut($astronautId);
        $points = array_sum(array_map(fn (PointEntry $e): int => $e->points, $log));
        $level = LevelRule::of($points, $this->settings->levelSize());

        $complete = [];

        foreach ($log as $entry) {
            if ($entry->reason === PointReason::DailyComplete) {
                $complete[$entry->day] = true;
            }
        }

        $badges = [];

        if ($withBadges) {
            $facts = $this->facts->build($astronautId, array_values(array_filter($log, fn (PointEntry $e): bool => $e->reason !== PointReason::Badge)));
            $earned = $this->findAchievements->earnedBy($astronautId);

            foreach ($this->findAchievements->all() as $badge) {
                $progress = BadgeRule::progress($badge->code, $badge->target, $facts);
                $badges[] = [
                    'code' => $badge->code,
                    'name' => $badge->name,
                    'icon' => $badge->icon,
                    'condition' => $badge->condition,
                    'bonus' => $badge->bonus,
                    'earned_day' => $earned[$badge->id] ?? null,
                    'progress' => ['current' => isset($earned[$badge->id]) ? $badge->target : $progress['best'], 'target' => $badge->target],
                ];
            }
        }

        return [
            'astronaut_id' => $astronautId,
            'points' => $points,
            'level' => $level['level'],
            'level_progress' => $level['progress'],
            'level_size' => $this->settings->levelSize(),
            'streak_days' => StreakRule::current($complete, max(1, $this->clock->today($astronautId))),
            'badges' => $badges,
            'recent' => array_slice($log, 0, 8),
            'earning' => $this->earning(),
        ];
    }

    /**
     * Cómo se ganan los puntos (config/gamification.php), para la pantalla.
     *
     * @return list<array{reason: string, points: int}>
     */
    private function earning(): array
    {
        $earning = [];

        foreach (PointReason::cases() as $reason) {
            if ($reason !== PointReason::Badge) {
                $earning[] = ['reason' => $reason->value, 'points' => $this->settings->pointsFor($reason)];
            }
        }

        return $earning;
    }
}
