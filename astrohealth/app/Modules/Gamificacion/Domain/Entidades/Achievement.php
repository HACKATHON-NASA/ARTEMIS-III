<?php

declare(strict_types=1);

namespace App\Modules\Gamificacion\Domain\Entidades;

/**
 * Una insignia: cómo se gana, su bono y su meta.
 */
final readonly class Achievement
{
    public function __construct(
        public int $id,
        public string $code,
        public string $name,
        public string $icon,
        public string $condition,
        public int $bonus,
        public int $target,
    ) {}
}
