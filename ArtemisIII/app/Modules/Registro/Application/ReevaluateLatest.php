<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application;

use App\Modules\Entorno\Contratos\CrewMember;
use App\Modules\Entorno\Contratos\CrewQuery;
use App\Modules\Entorno\Contratos\MissionQuery;
use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Registro\Application\Servicios\AfterSaving;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\UpdateMeasurementRepository;
use App\Shared\Persistencia\Transaction;

/**
 * Caso de uso: volver a poner el color a la ÚLTIMA medición de cada
 * indicador cuando cambian las reglas (el entorno de la misión o un
 * umbral), y recalcular el puntaje. El historial no se toca: cada valor
 * viejo conserva el color que tuvo cuando se midió.
 */
final readonly class ReevaluateLatest
{
    public function __construct(
        private CrewQuery $crew,
        private MissionQuery $missions,
        private FindMeasurementRepository $findMeasurement,
        private UpdateMeasurementRepository $updateMeasurement,
        private Evaluator $evaluator,
        private AfterSaving $afterSaving,
        private Transaction $transaction,
    ) {}

    /** Toda la tripulación de una misión (cambió su entorno). */
    public function forMission(int $missionId, ?string $onlyIndicator = null): void
    {
        $environment = $this->missions->environmentOf($missionId);

        if ($environment === null) {
            return;
        }

        $crew = array_filter($this->crew->all(), fn (CrewMember $member): bool => $member->missionId === $missionId);

        $this->transaction->run(function () use ($crew, $environment, $onlyIndicator): void {
            foreach ($crew as $member) {
                $this->forAstronaut($member->id, $environment, $onlyIndicator);
            }
        });
    }

    /** Las misiones que están en un entorno (cambió un umbral de ese entorno). */
    public function forEnvironment(string $environment, string $indicator): void
    {
        foreach (array_keys($this->missions->names()) as $missionId) {
            if ($this->missions->environmentOf($missionId) === $environment) {
                $this->forMission($missionId, $indicator);
            }
        }
    }

    private function forAstronaut(int $astronautId, string $environment, ?string $onlyIndicator): void
    {
        $changed = false;

        foreach ($this->findMeasurement->latestPerIndicator($astronautId) as $latest) {
            if ($onlyIndicator !== null && $latest->indicatorCode !== $onlyIndicator) {
                continue;
            }

            $status = $this->evaluator->evaluate($latest->indicatorCode, $latest->value, $environment);

            if ($status !== $latest->status) {
                $this->updateMeasurement->updateStatus($latest->id, $status);
                $changed = true;
            }
        }

        if ($changed) {
            $this->afterSaving->reassess($astronautId);
        }
    }
}
