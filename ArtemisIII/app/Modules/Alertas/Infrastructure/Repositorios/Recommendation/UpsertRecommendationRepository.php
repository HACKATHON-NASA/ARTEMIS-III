<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Infrastructure\Repositorios\Recommendation;

use App\Modules\Alertas\Domain\Entidades\Recommendation;
use App\Modules\Alertas\Infrastructure\Models\Recommendation as RecommendationModel;

/**
 * INSERTAR O ACTUALIZAR una recomendación por indicador + estado.
 */
final class UpsertRecommendationRepository
{
    public function save(Recommendation $recommendation): Recommendation
    {
        RecommendationModel::query()->updateOrCreate(
            ['indicator_code' => $recommendation->indicatorCode, 'status' => $recommendation->status],
            ['title' => $recommendation->title, 'action_text' => $recommendation->actionText, 'risk_ref' => $recommendation->riskRef],
        );

        return $recommendation;
    }

    /**
     * Solo si no existe: así el seeder no pisa lo que cambiaron los médicos.
     *
     * @param  array{indicator_code: string, status: string, title: string, action_text: string, risk_ref: string|null}  $data
     */
    public function createIfMissing(array $data): void
    {
        RecommendationModel::query()->firstOrCreate(
            ['indicator_code' => $data['indicator_code'], 'status' => $data['status']],
            $data,
        );
    }
}
