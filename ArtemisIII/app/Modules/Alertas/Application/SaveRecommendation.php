<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Domain\Entidades\Recommendation;
use App\Modules\Alertas\Infrastructure\Repositorios\Recommendation\UpsertRecommendationRepository;

/**
 * Caso de uso: un médico de vuelo cambia "qué hacer" desde la Guía de salud
 * (guide.manage). Los pasos se guardan uno por línea, sin líneas vacías.
 */
final readonly class SaveRecommendation
{
    public function __construct(private UpsertRecommendationRepository $upsert) {}

    public function execute(string $indicatorCode, string $status, string $title, string $actionText, ?string $riskRef): Recommendation
    {
        $steps = array_values(array_filter(array_map('trim', preg_split('/\R/', $actionText) ?: []), fn (string $step): bool => $step !== ''));

        return $this->upsert->save(new Recommendation($indicatorCode, $status, trim($title), implode("\n", $steps), $riskRef));
    }
}
