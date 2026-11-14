# 🧱 Shared · Piezas técnicas comunes · Backend

> [← Backend](README.md) · Código: `astrohealth/app/Shared/`

| Estado | Fase | Responsable |
|---|:---:|---|
| ✅ Terminado (2026-11-14) | **1** | Backend |

**Qué hace:** lo que usan **todos** los módulos y que **no** tiene reglas de negocio: el formato JSON, la base de los errores, las transacciones y la clase base de los *providers*. Si algo de aquí empieza a tener lógica de un módulo, está en el lugar equivocado.

## 1. Estructura

```
app/Shared/
├── ModuleServiceProvider.php        # Base: carga routes.php (prefijo api/v1) y Database/Migrations del módulo
├── Http/
│   ├── ApiResponse.php              # ok() · created() · noContent() · paginated() · error()
│   ├── ApiExceptionRenderer.php     # excepción → formato de error (lo llama bootstrap/app.php)
│   ├── GeneralCode.php              # enum: código, estado HTTP y mensaje en español
│   └── Middleware/ForceJsonResponse.php   # Accept: application/json en todo /api
├── Excepciones/
│   └── BusinessError.php            # abstract: errorCode(), httpStatus(), field(), errors(), headers()
└── Persistencia/
    ├── Transaction.php              # interface: run(callable, int $attempts = 1)
    └── DatabaseTransaction.php      # DB::transaction() — el único lugar con DB:: fuera de los repositorios
```

## 2. `ApiResponse`

```php
return ApiResponse::ok(MeasurementResource::collection($result->items));          // 200
return ApiResponse::created(new BatchResource($result));                          // 201
return ApiResponse::noContent();                                                  // 204
return ApiResponse::paginated(AlertResource::collection($page), $page);           // 200 + meta
```

Siempre produce `{ "data": …, "message": "ok" }` y, en las listas, `meta`.

## 3. Manejador de errores (`bootstrap/app.php`)

Todo error de `/api/*` sale con el mismo formato, **sin que ningún controlador lo arme**:

| Excepción | HTTP | `error_code` |
|---|:---:|---|
| `ValidationException` | 422 | `VALIDATION_FAILED` + `errors` por campo |
| `AuthenticationException` / errores del `JwtGuard` | 401 | `AUTH_*` (los de [M0](M0-identidad/README.md#7-códigos-de-error)) |
| `AuthorizationException` | 403 | `FORBIDDEN` |
| `ModelNotFoundException` / `NotFoundHttpException` | 404 | `NOT_FOUND` |
| `ThrottleRequestsException` | 429 | `TOO_MANY_REQUESTS` (con `Retry-After`) |
| Método no permitido | 405 | `METHOD_NOT_ALLOWED` |
| Otro `HttpException` | El suyo | El general de ese estado (`GeneralCode::fromStatus`) |
| `BusinessError` (de cualquier módulo) | El suyo | El suyo (`ALERTS_ALREADY_SENT`…) |
| Cualquier otra | 500 | `INTERNAL_ERROR`, "Ocurrió un error inesperado." y una línea en el *log* |

En local (`APP_DEBUG=true`), los errores 500 agregan `debug: {exception, message, file, line}`. En producción, nunca.

## 4. `BusinessError`

```php
final class AlertAlreadySent extends BusinessError
{
    public function errorCode(): string { return ErrorCode::AlreadySent->value; }   // ALERTS_ALREADY_SENT
    public function httpStatus(): int { return 409; }
    public function __construct() { parent::__construct('Esta alerta ya se envió a Tierra.'); }
}
```

## 5. `Transaction`

```php
$this->transaction->run(function () use ($data) {
    $this->batches->create($data->batchId);
    $this->measurements->insertMany($data->readings);
}, attempts: 3);
```

Los eventos se publican **después** del *commit* (`ShouldDispatchAfterCommit`).

## 6. Avance

- [x] `ModuleServiceProvider` (los *providers* de cada módulo se registran cuando se crea el módulo)
- [x] `ApiResponse` · `GeneralCode` · `ApiExceptionRenderer`
- [x] `BusinessError` y el manejador de `bootstrap/app.php`
- [x] `Transaction` · `DatabaseTransaction` (enlazados en `AppServiceProvider`)
- [x] `ForceJsonResponse` al inicio del grupo `api`
- [x] *Testsuite* `Modules` (`app/Modules/**/…Test.php`) en `phpunit.xml`
- [x] Pruebas: `tests/Feature/Shared/ApiErrorFormatTest.php` (13) y `DatabaseTransactionTest.php` (2)

## 7. Cómo quedó hecho

### Decisiones técnicas
- **`errorCode()` y no `code()`:** `Exception::getCode()` ya existe y es `final`; un nombre distinto evita la confusión.
- **El renderizador es una clase** (`ApiExceptionRenderer`) y no código en `bootstrap/app.php`: así se lee, se prueba y PHPStan lo analiza.
- **Laravel convierte antes** `AuthorizationException` → 403 y `ModelNotFoundException` → 404 (`HttpException`); el renderizador cubre los dos caminos.
- **`errors` vacío sale como `{}`** (objeto JSON), nunca `[]`, para que el cliente siempre reciba un mapa.
- **`attempts` es `positive-int`:** PHPStan impide pasar 0 o negativos, sin validación en tiempo de ejecución.

### Diferencias con este documento
- `MysqlTransaction` pasó a llamarse **`DatabaseTransaction`**, porque en las pruebas la conexión es SQLite.
- Se agregaron los códigos generales `BAD_REQUEST` (400), `METHOD_NOT_ALLOWED` (405), `PAYLOAD_TOO_LARGE` (413) y `SERVICE_UNAVAILABLE` (503).

### Cómo probarlo
```powershell
php artisan test --filter=ApiErrorFormatTest
curl -s http://localhost:8000/api/v1/no-existe
# {"data":null,"message":"El recurso no existe.","error_code":"NOT_FOUND","errors":{}}
```

> 📝 **Documentar siempre:** este documento se actualiza en el mismo *pull request* que cambia el código.
