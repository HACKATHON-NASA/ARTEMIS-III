<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application;

use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Registro\Application\DTOs\BatchResult;
use App\Modules\Registro\Application\DTOs\IngestReading;
use App\Modules\Registro\Application\Servicios\AfterSaving;
use App\Modules\Registro\Application\Servicios\CrewContext;
use App\Modules\Registro\Domain\Entidades\Measurement;
use App\Modules\Registro\Domain\Enums\ErrorCode;
use App\Modules\Registro\Domain\Reglas\ReadingValidator;
use App\Modules\Registro\Domain\ValueObjects\BatchSummary;
use App\Modules\Registro\Infrastructure\Repositorios\Indicator\FindIndicatorRepository;
use App\Modules\Registro\Infrastructure\Repositorios\IngestBatch\FindIngestBatchRepository;
use App\Modules\Registro\Infrastructure\Repositorios\IngestBatch\InsertIngestBatchRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\InsertMeasurementRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: una máquina (el simulador o un sensor) envía un lote.
 *
 * - Lote repetido (mismo batch_id): devuelve el resumen de la primera vez.
 * - Aceptación parcial: una lectura mala va a "rejected" y el resto se guarda.
 * - El astronauta debe ser de la misión del dispositivo (y, si el
 *   dispositivo lleva astronauta, ese mismo).
 * - Un reading_id que ya existe no se guarda otra vez: cuenta en duplicates.
 * Después: puntaje y MeasurementsRecorded por cada astronauta.
 */
final readonly class IngestReadings
{
    public function __construct(
        private CurrentActor $currentActor,
        private CrewContext $crew,
        private FindIndicatorRepository $findIndicator,
        private FindIngestBatchRepository $findBatch,
        private InsertIngestBatchRepository $insertBatch,
        private InsertMeasurementRepository $insertMeasurement,
        private FindMeasurementRepository $findMeasurement,
        private Evaluator $evaluator,
        private AfterSaving $afterSaving,
        private Transaction $transaction,
    ) {}

    /**
     * @param  list<IngestReading>  $readings
     */
    public function execute(string $batchId, array $readings): BatchResult
    {
        $previous = $this->findBatch->findSummary($batchId);

        if ($previous !== null) {
            return new BatchResult($previous, false);
        }

        $device = $this->currentActor->claims();
        [$accepted, $rejected] = $this->check($readings, $device->missionId, $device->astronautId);

        return $this->transaction->run(function () use ($batchId, $readings, $device, $accepted, $rejected): BatchResult {
            $rowId = $this->insertBatch->start($batchId, $device->actorId, count($readings));
            $stored = $this->insertMeasurement->insertIgnoringDuplicates(array_map(
                fn (array $row): array => [...$row, 'ingest_batch_id' => $rowId],
                $accepted,
            ));
            $duplicates = count($accepted) - $stored;
            $this->insertBatch->finish($rowId, $stored, $duplicates, $rejected);

            $this->afterSave($this->findMeasurement->ofBatch($rowId));

            return new BatchResult(new BatchSummary($batchId, count($readings), $stored, $duplicates, $rejected), true);
        });
    }

    /**
     * Separa las lecturas que se pueden guardar (ya con su color) de las rechazadas.
     *
     * @param  list<IngestReading>  $readings
     * @return array{0: list<array{astronaut_id: int, indicator_id: int, value: float, status: string, source: string, measured_at: CarbonImmutable, reading_id: string}>, 1: list<array{reading_id: string, error_code: string, message: string}>}
     */
    private function check(array $readings, ?int $deviceMission, ?int $deviceAstronaut): array
    {
        $now = CarbonImmutable::now();
        $accepted = [];
        $rejected = [];

        foreach ($readings as $reading) {
            $indicator = $this->findIndicator->byCode($reading->indicator);
            $problem = ReadingValidator::check($indicator, $reading->unit, $reading->value, $reading->measuredAt, $now);

            $allowed = $deviceMission !== null
                && $this->crew->missionOf($reading->astronautId) === $deviceMission
                && ($deviceAstronaut === null || $deviceAstronaut === $reading->astronautId);

            if ($problem === null && ! $allowed) {
                $problem = ErrorCode::AstronautNotAllowed;
            }

            if ($indicator === null || $problem !== null) {
                $problem ??= ErrorCode::UnknownIndicator;
                $rejected[] = ['reading_id' => $reading->readingId, 'error_code' => $problem->value, 'message' => ReadingValidator::message($problem, $indicator, $reading->indicator)];

                continue;
            }

            [, $environment] = $this->crew->of($reading->astronautId);
            $accepted[] = [
                'astronaut_id' => $reading->astronautId,
                'indicator_id' => $indicator->id,
                'value' => $reading->value,
                'status' => $this->evaluator->evaluate($indicator->code, $reading->value, $environment),
                'source' => $reading->source,
                'measured_at' => $reading->measuredAt,
                'reading_id' => $reading->readingId,
            ];
        }

        return [$accepted, $rejected];
    }

    /**
     * Puntaje y evento por cada astronauta del lote.
     *
     * @param  list<Measurement>  $saved
     */
    private function afterSave(array $saved): void
    {
        $byAstronaut = [];

        foreach ($saved as $measurement) {
            $byAstronaut[$measurement->astronautId][] = $measurement;
        }

        foreach ($byAstronaut as $astronautId => $items) {
            $this->afterSaving->handle($astronautId, $this->crew->missionOf($astronautId) ?? 0, $items);
        }
    }
}
