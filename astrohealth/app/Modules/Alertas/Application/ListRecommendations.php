<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Application;

use App\Modules\Alertas\Domain\Entidades\Recommendation;
use App\Modules\Alertas\Infrastructure\Repositorios\Recommendation\FindRecommendationRepository;

/**
 * Caso de uso: "qué hacer" ante cada indicador en amarillo o en rojo.
 */
final readonly class ListRecommendations
{
    public function __construct(private FindRecommendationRepository $find) {}

    /**
     * @return list<Recommendation>
     */
    public function execute(): array
    {
        return $this->find->all();
    }
}
