<?php

declare(strict_types=1);

namespace App\Modules\Identidad;

use App\Modules\Identidad\Console\CreateDeviceCommand;
use App\Modules\Identidad\Console\JwtSecretCommand;
use App\Modules\Identidad\Contratos\Actor;
use App\Modules\Identidad\Contratos\CurrentActor;
use App\Modules\Identidad\Domain\ValueObjects\PermissionMatrix;
use App\Modules\Identidad\Infrastructure\AuthCurrentActor;
use App\Modules\Identidad\Infrastructure\Jwt\JwtConfig;
use App\Modules\Identidad\Infrastructure\Jwt\JwtGuard;
use App\Modules\Identidad\Infrastructure\Jwt\JwtVerifier;
use App\Modules\Identidad\Infrastructure\Jwt\RevokedTokens;
use App\Modules\Identidad\Infrastructure\Repositorios\Actor\FindActorRepository;
use App\Shared\ModuleServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use InvalidArgumentException;

/**
 * M0 · Identidad y acceso: guard JWT, permisos por rol, límites de peticiones y comandos.
 */
final class IdentidadServiceProvider extends ModuleServiceProvider
{
    protected function module(): string
    {
        return 'Identidad';
    }

    public function register(): void
    {
        $this->app->singleton(JwtConfig::class, fn (): JwtConfig => JwtConfig::fromArray(self::configArray('jwt')));

        $this->app->singleton(PermissionMatrix::class, fn (): PermissionMatrix => new PermissionMatrix(self::permissionsConfig()));

        $this->app->bind(CurrentActor::class, AuthCurrentActor::class);
    }

    public function boot(): void
    {
        parent::boot();

        $this->registerJwtGuard();
        $this->registerGates();
        $this->registerRateLimiters();

        if ($this->app->runningInConsole()) {
            $this->commands([JwtSecretCommand::class, CreateDeviceCommand::class]);
        }
    }

    private function registerJwtGuard(): void
    {
        Auth::extend('jwt', function (Application $app): JwtGuard {
            $guard = new JwtGuard(
                $app->make(JwtVerifier::class),
                $app->make(RevokedTokens::class),
                $app->make(FindActorRepository::class),
                $app->make(Request::class),
            );

            // Cada petición nueva reinicia el guard (varias peticiones en una misma prueba).
            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    /**
     * Un Gate por permiso de config/permissions.php, más "health.read" (lo suyo o de cualquiera).
     */
    private function registerGates(): void
    {
        $matrix = $this->app->make(PermissionMatrix::class);

        foreach ($matrix->permissions() as $permission) {
            Gate::define($permission, fn (Authenticatable $user): bool => self::allows($matrix, $user, $permission));
        }

        Gate::define('health.read', fn (Authenticatable $user, int|string $astronautId): bool => self::allows($matrix, $user, 'health.read.any')
            || (self::allows($matrix, $user, 'health.read.own')
                && $user instanceof Actor
                && $user->actorAstronautId() === (int) $astronautId));
    }

    private function registerRateLimiters(): void
    {
        RateLimiter::for('api-login', fn (Request $request): Limit => Limit::perMinute(5)
            ->by($request->string('email')->lower()->trim()->toString().'|'.$request->ip()));

        RateLimiter::for('api-refresh', fn (Request $request): Limit => Limit::perMinute(30)->by((string) $request->ip()));

        RateLimiter::for('api-device', fn (Request $request): Limit => Limit::perMinute(10)->by((string) $request->ip()));
    }

    private static function allows(PermissionMatrix $matrix, Authenticatable $user, string $permission): bool
    {
        if (! $user instanceof Actor) {
            return false;
        }

        $role = $user->actorRole();

        return $role !== null && $user->actorIsActive() && $matrix->allows($role, $permission);
    }

    /**
     * @return array<string, mixed>
     */
    private static function configArray(string $key): array
    {
        $value = config($key);

        if (! is_array($value)) {
            throw new InvalidArgumentException("Falta config/{$key}.php.");
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    /**
     * @return array<string, list<string>>
     */
    private static function permissionsConfig(): array
    {
        $matrix = [];

        foreach (self::configArray('permissions') as $permission => $roles) {
            if (! is_array($roles) || array_filter($roles, 'is_string') !== $roles) {
                throw new InvalidArgumentException("config/permissions.php: \"{$permission}\" debe ser una lista de roles.");
            }

            /** @var list<string> $roles */
            $matrix[$permission] = $roles;
        }

        return $matrix;
    }
}
