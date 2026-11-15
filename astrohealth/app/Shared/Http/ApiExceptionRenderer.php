<?php

declare(strict_types=1);

namespace App\Shared\Http;

use App\Shared\Excepciones\BusinessError;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

/**
 * Convierte cualquier excepción de /api/* en el formato de error único.
 *
 * Se registra en bootstrap/app.php. El reporte al log lo sigue haciendo
 * Laravel: aquí solo se decide la respuesta. Los mensajes (escritos en
 * español) salen en el idioma de la petición: __() los busca en
 * lang/<idioma>.json (SetLocaleFromHeader eligió el idioma).
 */
final class ApiExceptionRenderer
{
    public function render(Throwable $e): JsonResponse
    {
        return match (true) {
            $e instanceof BusinessError => ApiResponse::error(
                $e->errorCode(), self::translate($e->getMessage()), $e->httpStatus(), $this->translateErrors($e->errors()), headers: $e->headers(),
            ),
            $e instanceof ValidationException => $this->general(
                GeneralCode::ValidationFailed, $this->validationErrors($e),
            ),
            $e instanceof AuthenticationException => $this->general(GeneralCode::Unauthenticated),
            $e instanceof AuthorizationException => $this->general(GeneralCode::Forbidden),
            $e instanceof ModelNotFoundException => $this->general(GeneralCode::NotFound),
            $e instanceof HttpExceptionInterface => $this->httpException($e),
            default => $this->unexpected($e),
        };
    }

    /**
     * @param  array<string, list<string>>  $errors
     * @param  array<string, string>  $headers
     */
    private function general(GeneralCode $code, array $errors = [], array $headers = []): JsonResponse
    {
        return ApiResponse::error($code->value, self::translate($code->message()), $code->httpStatus(), $this->translateErrors($errors), headers: $headers);
    }

    private function httpException(HttpExceptionInterface $e): JsonResponse
    {
        $code = GeneralCode::fromStatus($e->getStatusCode());

        /** @var array<string, string> $headers */
        $headers = $e->getHeaders();

        return ApiResponse::error($code->value, self::translate($code->message()), $e->getStatusCode(), headers: $headers);
    }

    private function unexpected(Throwable $e): JsonResponse
    {
        $code = GeneralCode::InternalError;

        $debug = config('app.debug') === true ? [
            'exception' => $e::class,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ] : [];

        return ApiResponse::error($code->value, self::translate($code->message()), $code->httpStatus(), debug: $debug);
    }

    private static function translate(string $message): string
    {
        $translated = __($message);

        return is_string($translated) ? $translated : $message;
    }

    /**
     * @param  array<string, list<string>>  $errors
     * @return array<string, list<string>>
     */
    private function translateErrors(array $errors): array
    {
        return array_map(
            fn (array $messages): array => array_map(self::translate(...), $messages),
            $errors,
        );
    }

    /**
     * @return array<string, list<string>>
     */
    private function validationErrors(ValidationException $e): array
    {
        /** @var array<string, list<string>> $errors */
        $errors = $e->errors();

        return $errors;
    }
}
