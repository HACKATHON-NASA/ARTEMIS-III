<?php

declare(strict_types=1);

namespace App\Modules\Registro\Application\Servicios;

use App\Modules\Evaluacion\Contratos\Evaluator;
use App\Modules\Evaluacion\Contratos\LatestReading;
use App\Modules\Registro\Domain\Entidades\Measurement;
use App\Modules\Registro\Domain\Eventos\MeasurementsRecorded;
use App\Modules\Registro\Domain\Eventos\RecordedMeasurement;
use App\Modules\Registro\Infrastructure\Repositorios\Measurement\FindMeasurementRepository;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Lo que pasa después de guardar mediciones, igual a mano o por una
 * máquina: M2 recalcula el puntaje con el último valor de cada indicador y
 * se publica MeasurementsRecorded (sale después del commit).
 */
final readonly class AfterSaving
{
    public function __construct(
        private FindMeasurementRepository $findMeasurement,
        private Evaluator $evaluator,
        private Dispatcher $events,
    ) {}

    /**
     * @param  list<Measurement>  $saved
     */
    public function handle(int $astronautId, int $missionId, array $saved): void
    {
        if ($saved === []) {
            return;
        }

        $this->reassess($astronautId);

        $this->events->dispatch(new MeasurementsRecorded($astronautId, $missionId, array_map(
            fn (Measurement $m): RecordedMeasurement => new RecordedMeasurement($m->id, $m->indicatorCode, $m->value, $m->status, $m->bodyPart, $m->measuredAt, $m->source),
            $saved,
        )));
    }

    /** El puntaje con el último valor de cada indicador (M2 lo guarda). */
    public function reassess(int $astronautId): void
    {
        $this->evaluator->assess($astronautId, array_map(
            fn (Measurement $m): LatestReading => new LatestReading($m->indicatorCode, $m->status, $m->bodyPart),
            $this->findMeasurement->latestPerIndicator($astronautId),
        ));
    }
}
