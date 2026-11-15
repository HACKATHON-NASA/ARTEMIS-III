<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Application;

use App\Modules\Evaluacion\Application\DTOs\ThresholdData;
use App\Modules\Evaluacion\Domain\Eventos\ThresholdUpdated;
use App\Modules\Evaluacion\Domain\Excepciones\ThresholdMissing;
use App\Modules\Evaluacion\Domain\ValueObjects\Threshold;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold\FindThresholdRepository;
use App\Modules\Evaluacion\Infrastructure\Repositorios\Threshold\UpsertThresholdRepository;
use Illuminate\Contracts\Events\Dispatcher;

/**
 * Caso de uso: un médico de vuelo (guide.manage) cambia los rangos de un
 * indicador en un entorno. Solo se editan indicadores que existen. Publica
 * ThresholdUpdated para que se vuelvan a evaluar las mediciones.
 */
final readonly class UpdateThreshold
{
    public function __construct(
        private FindThresholdRepository $findThreshold,
        private UpsertThresholdRepository $upsertThreshold,
        private Dispatcher $events,
    ) {}

    public function execute(string $indicatorCode, string $environment, ThresholdData $data): Threshold
    {
        $this->findThreshold->find($indicatorCode, $environment)
            ?? throw ThresholdMissing::notFound($indicatorCode, $environment);

        // El constructor valida que el verde quede dentro del amarillo.
        $threshold = new Threshold($indicatorCode, $environment, $data->greenMin, $data->greenMax, $data->yellowMin, $data->yellowMax);

        $saved = $this->upsertThreshold->save($threshold);
        $this->events->dispatch(new ThresholdUpdated($indicatorCode, $environment));

        return $saved;
    }
}
