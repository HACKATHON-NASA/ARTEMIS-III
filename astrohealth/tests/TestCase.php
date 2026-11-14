<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Fortify\Features;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seguro de la base de datos: corre después de crear la aplicación y ANTES de
     * los traits (RefreshDatabase vacía la base). Si la base no es de pruebas,
     * se detiene sin tocar nada.
     *
     * @return array<class-string, class-string>
     */
    protected function setUpTraits()
    {
        $this->guardAgainstNonTestingDatabase();

        return parent::setUpTraits();
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }

    private function guardAgainstNonTestingDatabase(): void
    {
        $database = (string) config('database.connections.'.config('database.default').'.database');

        if (! str_ends_with($database, '_testing')) {
            throw new RuntimeException(
                "Las pruebas solo corren contra una base que termine en \"_testing\". Base actual: \"{$database}\"."
            );
        }
    }
}
