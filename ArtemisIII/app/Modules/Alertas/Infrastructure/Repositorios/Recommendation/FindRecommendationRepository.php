<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Recommendation;

use App\Modules\Alertas\Domain\Entidades\Recommendation;
use App\Modules\Alertas\Infrastructure\Models\Recommendation as RecommendationModel;

/**
 * BUSCAR recomendaciones (tabla recommendations). Solo lectura.
 */
final class FindRecommendationRepository
{
    /**
     * @return list<Recommendation>
     */
    public function all(): array
    {
        return array_values(RecommendationModel::query()
            ->orderBy('indicator_code')
            ->orderByDesc('status')
            ->get()
            ->map(fn (RecommendationModel $row): Recommendation => new Recommendation($row->indicator_code, $row->status, $row->title, $row->action_text, $row->risk_ref))
            ->all());
    }
}
