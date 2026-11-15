<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Entidades;

/**
 * "Qué hacer" ante un indicador en amarillo o en rojo.
 */
final readonly class Recommendation
{
    public function __construct(
        public string $indicatorCode,
        public string $status,
        public string $title,
        public string $actionText,
        public ?string $riskRef,
    ) {}
}
