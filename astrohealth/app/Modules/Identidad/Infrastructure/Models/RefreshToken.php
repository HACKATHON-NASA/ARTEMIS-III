<?php

declare(strict_types=1);

namespace App\Modules\Identidad\Infrastructure\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;

/**
 * Un refresh token. Solo se guarda su hash SHA-256, nunca el token.
 *
 * @property int $id
 * @property int $user_id
 * @property string $family_id
 * @property string $token_hash
 * @property CarbonImmutable $expires_at
 * @property CarbonImmutable|null $used_at
 * @property CarbonImmutable|null $revoked_at
 * @property string|null $ip
 * @property string|null $user_agent
 * @property CarbonImmutable|null $created_at
 */
final class RefreshToken extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['user_id', 'family_id', 'token_hash', 'expires_at', 'used_at', 'revoked_at', 'ip', 'user_agent'];

    protected $hidden = ['token_hash'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'expires_at' => 'immutable_datetime',
            'used_at' => 'immutable_datetime',
            'revoked_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
        ];
    }
}
