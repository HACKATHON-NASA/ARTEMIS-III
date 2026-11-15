<?php

declare(strict_types=1);

namespace App\Modules\Registro\Http\Resources;

use App\Modules\Registro\Domain\ValueObjects\BatchSummary;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * El resumen de un lote de la ingesta.
 *
 * @property BatchSummary $resource
 */
final class BatchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'batch_id' => $this->resource->batchId,
            'received' => $this->resource->received,
            'stored' => $this->resource->stored,
            'duplicates' => $this->resource->duplicates,
            'rejected' => $this->resource->rejected,
        ];
    }
}
