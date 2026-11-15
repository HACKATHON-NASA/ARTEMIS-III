<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Requests;

use App\Modules\Registro\Application\DTOs\IngestReading;
use App\Modules\Registro\Domain\Enums\Source;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

/**
 * POST /api/v1/ingest/readings · la FORMA del lote. Un error aquí rechaza
 * el lote entero (422); un problema de contenido rechaza solo esa lectura.
 */
final class IngestReadingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string|In>>
     */
    public function rules(): array
    {
        return [
            'batch_id' => ['required', 'uuid'],
            'device_time' => ['nullable', 'date'],
            'readings' => ['required', 'array', 'min:1', 'max:500'],
            'readings.*.reading_id' => ['required', 'uuid'],
            'readings.*.astronaut_id' => ['required', 'integer', 'min:1'],
            'readings.*.indicator' => ['required', 'string', 'max:20'],
            'readings.*.value' => ['required', 'numeric'],
            'readings.*.unit' => ['required', 'string', 'max:10'],
            'readings.*.measured_at' => ['required', 'date'],
            'readings.*.source' => ['required', Rule::in(Source::machineValues())],
        ];
    }

    public function batchId(): string
    {
        return $this->string('batch_id')->lower()->toString();
    }

    /**
     * @return list<IngestReading>
     */
    public function readings(): array
    {
        $items = $this->input('readings', []);

        return array_values(array_map(fn (array $item): IngestReading => new IngestReading(
            readingId: strtolower((string) $item['reading_id']),
            astronautId: (int) $item['astronaut_id'],
            indicator: (string) $item['indicator'],
            value: (float) $item['value'],
            unit: (string) $item['unit'],
            measuredAt: CarbonImmutable::parse((string) $item['measured_at'])->utc(),
            source: (string) $item['source'],
        ), is_array($items) ? array_filter($items, 'is_array') : []));
    }
}
