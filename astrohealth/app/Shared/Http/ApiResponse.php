<?php

declare(strict_types=1);

namespace App\Shared\Http;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

/**
 * Único constructor de respuestas de la API /api/v1.
 *
 * Éxito: {"data": …, "message": "ok"} · Error: {"data": null, "message", "error_code", "errors"}.
 */
final class ApiResponse
{
    /** Tildes y barras legibles: "inválidas" y no "inválidas". */
    private const JSON_FLAGS = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

    public static function ok(mixed $data, string $message = 'ok'): JsonResponse
    {
        return self::success($data, $message, 200);
    }

    public static function created(mixed $data, string $message = 'ok'): JsonResponse
    {
        return self::success($data, $message, 201);
    }

    public static function noContent(): Response
    {
        return new Response(status: 204);
    }

    /**
     * Lista paginada: agrega "meta" con los datos de la paginación.
     *
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     */
    public static function paginated(JsonResource $collection, LengthAwarePaginator $paginator, string $message = 'ok'): JsonResponse
    {
        return new JsonResponse([
            'data' => $collection->resolve(),
            'message' => $message,
            'meta' => [
                'page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ], 200, [], self::JSON_FLAGS);
    }

    /**
     * @param  array<string, list<string>>  $errors
     * @param  array<string, mixed>  $debug
     * @param  array<string, string>  $headers
     */
    public static function error(
        string $errorCode,
        string $message,
        int $status,
        array $errors = [],
        array $debug = [],
        array $headers = [],
    ): JsonResponse {
        $body = [
            'data' => null,
            'message' => $message,
            'error_code' => $errorCode,
            'errors' => $errors === [] ? new stdClass : $errors,
        ];

        if ($debug !== []) {
            $body['debug'] = $debug;
        }

        return new JsonResponse($body, $status, $headers, self::JSON_FLAGS);
    }

    private static function success(mixed $data, string $message, int $status): JsonResponse
    {
        if ($data instanceof JsonResource) {
            $data = $data->resolve();
        }

        return new JsonResponse(['data' => $data, 'message' => $message], $status, [], self::JSON_FLAGS);
    }
}
