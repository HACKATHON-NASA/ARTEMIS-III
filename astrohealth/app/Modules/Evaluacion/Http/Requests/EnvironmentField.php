<?php

declare(strict_types=1);

namespace App\Modules\Evaluacion\Http\Requests;

/**
 * El entorno de la petición (?environment=), validado contra
 * config/environments.php (M2 no depende de M6: lee la configuración).
 */
trait EnvironmentField
{
    /**
     * @return list<string>
     */
    protected function environments(): array
    {
        $environments = config('environments');

        return is_array($environments) ? array_map('strval', array_keys($environments)) : [];
    }

    /**
     * @return array<string, string>
     */
    protected function environmentMessages(): array
    {
        return [
            'environment.required' => 'Indica el entorno (iss, transit, moon o mars).',
            'environment.in' => 'El entorno no es válido.',
        ];
    }

    public function environment(): string
    {
        return $this->string('environment')->toString();
    }
}
