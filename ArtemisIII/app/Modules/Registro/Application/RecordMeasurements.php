<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application;

use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Registro\Application\DTOs\ReadingInput;
use App\Modules\Registro\Application\Servicios\AfterSaving;
use App\Modules\Registro\Application\Servicios\CrewContext;
use App\Modules\Registro\Domain\Entidades\Measurement;
use App\Modules\Registro\Domain\Enums\ErrorCode;
use App\Modules\Registro\Domain\Enums\Source;
use App\Modules\Registro\Domain\Excepciones\InvalidReading;
use App\Modules\Registro\Domain\Excepciones\NotCrewMember;
use App\Modules\Registro\Domain\Reglas\ReadingValidator;
use App\Modules\Registro\Infrastructure\Repositorios\Indicator\FindIndicatorRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\InsertMeasurementRepository;
use App\Shared\Persistencia\Transaction;
use Carbon\CarbonImmutable;

/**
 * Caso de uso: el tripulante registra sus datos a mano.
 *
 * El astronauta sale del token (nunca del cuerpo). Cada valor se valida
 * (si uno falla, no se guarda nada y el error va en su campo), M2 le pone
 * el color con los umbrales del entorno de su misión, se guarda con
 * source "manual", se recalcula el puntaje y se publica MeasurementsRecorded.
 */
final readonly class RecordMeasurements
{
    public function __construct(
        private CurrentActor $currentActor,
        private CrewContext $crew,
        private FindIndicatorRepository $findIndicator,
        private InsertMeasurementRepository $insertMeasurement,
        private FindMeasurementRepository $findMeasurement,
        private Evaluator $evaluator,
        private AfterSaving $afterSaving,
        private Transaction $transaction,
    ) {}

    /**
     * @param  list<ReadingInput>  $readings
     * @return list<Measurement>
     */
    public function execute(array $readings, ?CarbonImmutable $measuredAt): array
    {
        $astronautId = $this->currentActor->claims()->astronautId ?? throw new NotCrewMember;
        [$missionId, $environment] = $this->crew->of($astronautId);
        $now = CarbonImmutable::now();
        $measuredAt ??= $now;
        $rows = [];

        foreach ($readings as $index => $reading) {
            $indicator = $this->findIndicator->byCode($reading->indicator);
            $problem = ReadingValidator::check($indicator, null, $reading->value, $measuredAt, $now);

            if ($indicator === null || $problem !== null) {
                $problem ??= ErrorCode::UnknownIndicator;
                $field = $problem === ErrorCode::FutureTimestamp ? 'measured_at' : "measurements.{$index}.value";

                throw new InvalidReading(ReadingValidator::message($problem, $indicator, $reading->indicator), $problem, $field);
            }

            $rows[] = [
                'astronaut_id' => $astronautId,
                'indicator_id' => $indicator->id,
                'value' => $reading->value,
                'status' => $this->evaluator->evaluate($indicator->code, $reading->value, $environment),
                'source' => Source::Manual->value,
                'measured_at' => $measuredAt,
            ];
        }

        return $this->transaction->run(function () use ($rows, $astronautId, $missionId): array {
            $saved = $this->findMeasurement->byIds($this->insertMeasurement->insertMany($rows));
            $this->afterSaving->handle($astronautId, $missionId, $saved);

            return $saved;
        });
    }
}
