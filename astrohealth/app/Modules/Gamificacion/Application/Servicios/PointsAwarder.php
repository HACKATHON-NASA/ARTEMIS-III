<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Application\Servicios;

use App\Modules\Gamificacion\Domain\Entidades\PointEntry;
use App\Modules\Gamificacion\Domain\Enums\PointReason;
use App\Modules\Gamificacion\Domain\Reglas\BadgeRule;
use App\Modules\Gamificacion\Domain\Reglas\StreakRule;
use App\Modules\Gamificacion\Infrastructure\Configuracion\GamificationSettings;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Achievement\FindAchievementRepository;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Achievement\UpsertAchievementRepository;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Points\FindPointsRepository;
use App\Modules\Gamificacion\Infrastructure\Repositorios\Points\InsertPointsRepository;
use Carbon\CarbonImmutable;

/**
 * Suma puntos (una sola vez por acción), la racha de 7 días y las insignias
 * nuevas con su bono. Lo usan los eventos de M1 y, mientras M8 y los tests
 * no tienen backend, el reclamo del tripulante.
 */
final readonly class PointsAwarder
{
    public function __construct(
        private InsertPointsRepository $insertPoints,
        private FindPointsRepository $findPoints,
        private FindAchievementRepository $findAchievements,
        private UpsertAchievementRepository $upsertAchievements,
        private BadgeFactsBuilder $facts,
        private GamificationSettings $settings,
    ) {}

    /** Devuelve si sumó (false si esa acción ya había sumado). */
    public function award(int $astronautId, PointReason $reason, string $reference, int $day): bool
    {
        $now = CarbonImmutable::now();
        $added = $this->insertPoints->add($astronautId, $reason, $reference, $this->settings->pointsFor($reason), $day, $now);

        if (! $added) {
            return false;
        }

        if ($reason === PointReason::DailyComplete) {
            $this->streakBonus($astronautId, $day, $now);
        }

        $this->checkBadges($astronautId, $now);

        return true;
    }

    /** Cada 7 días seguidos con el registro completo: +50. */
    private function streakBonus(int $astronautId, int $day, CarbonImmutable $now): void
    {
        $complete = [];

        foreach ($this->findPoints->ofAstronaut($astronautId) as $entry) {
            if ($entry->reason === PointReason::DailyComplete) {
                $complete[$entry->day] = true;
            }
        }

        $streak = StreakRule::current($complete, $day);

        if ($streak > 0 && $streak % 7 === 0) {
            $this->insertPoints->add($astronautId, PointReason::Streak7, (string) $day, $this->settings->pointsFor(PointReason::Streak7), $day, $now);
        }
    }

    /** Las insignias que se acaban de ganar: se registran con su bono. */
    public function checkBadges(int $astronautId, ?CarbonImmutable $now = null): void
    {
        $now ??= CarbonImmutable::now();
        $log = array_values(array_filter($this->findPoints->ofAstronaut($astronautId), fn (PointEntry $e): bool => $e->reason !== PointReason::Badge));
        $facts = $this->facts->build($astronautId, $log);
        $earned = $this->findAchievements->earnedBy($astronautId);

        foreach ($this->findAchievements->all() as $badge) {
            if (isset($earned[$badge->id])) {
                continue;
            }

            $progress = BadgeRule::progress($badge->code, $badge->target, $facts);

            if ($progress['day'] !== null && $this->upsertAchievements->earn($astronautId, $badge->id, $progress['day'], $now)) {
                $this->insertPoints->add($astronautId, PointReason::Badge, $badge->code, $badge->bonus, $progress['day'], $now);
            }
        }
    }
}
