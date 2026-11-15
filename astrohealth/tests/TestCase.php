<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
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

    /**
     * Las pruebas piden en español, como la app sin idioma elegido (el
     * cliente de pruebas de Symfony manda "en-us" por defecto).
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('Accept-Language', 'es');
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
