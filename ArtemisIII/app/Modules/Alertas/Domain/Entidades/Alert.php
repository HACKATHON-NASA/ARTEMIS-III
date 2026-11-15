<?php

declare(strict_types=1);

namespace App\Modules\Alertas\Domain\Entidades;

use App\Modules\Alertas\Domain\Enums\AlertKind;
use App\Modules\Alertas\Domain\Enums\AlertLevel;
use App\Modules\Alertas\Domain\Enums\AlertSource;
use Carbon\CarbonImmutable;

/**
 * Una alerta, tal como la ve M5 (sin Eloquent).
 */
final readonly class Alert
{
    public function __construct(
        public int $id,
        public int $astronautId,
        public ?int $measurementId,
        public AlertKind $kind,
        public ?string $indicatorCode,
        /** Solo en las vitales: el valor que la creó. */
        public ?float $value,
        public AlertLevel $level,
        public string $message,
        public ?string $category,
        public ?string $note,
        public AlertSource $source,
        public int $delaySeconds,
        public CarbonImmutable $sentAt,
        public CarbonImmutable $groundEtaAt,
        public ?CarbonImmutable $acknowledgedAt,
        public ?string $reply,
        public CarbonImmutable $createdAt,
    ) {}
}
