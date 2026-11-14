# 🧱 Shared · Piezas técnicas comunes · Backend

> [← Backend](README.md) · Código: `astrohealth/app/Shared/`

| Estado | Fase | Responsable |
|---|:---:|---|
| ⬜ Sin empezar | **1** | Backend |

**Qué hace:** lo que usan **todos** los módulos y que **no** tiene reglas de negocio: el formato JSON, la base de los errores, las transacciones y la clase base de los *providers*. Si algo de aquí empieza a tener lógica de un módulo, está en el lugar equivocado.

## 1. Estructura

```
app/Shared/
├── ModuleServiceProvider.php        # Base: carga routes.php (prefijo api/v1) y Database/Migrations del módulo
├── Http/
│   ├── ApiResponse.php              # ok() · created() · noContent() · paginated()
│   ├── GeneralCode.php              # UNAUTHENTICATED · FORBIDDEN · NOT_FOUND · CONFLICT · VALIDATION_FAILED · TOO_MANY_REQUESTS · INTERNAL_ERROR
│   └── Middleware/ForceJsonResponse.php   # Accept: application/json en todo /api
├── Excepciones/
│   └── BusinessError.php            # abstract: code(), httpStatus(), field()
└── Persistencia/
    ├── Transaction.php              # interface: run(callable, int $attempts = 1)
    └── MysqlTransaction.php         # DB::transaction() — el único lugar con DB:: fuera de los repositorios
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
| `ThrottleRequestsException` | 429 | `TOO_MANY_REQUESTS` |
| `BusinessError` (de cualquier módulo) | El suyo | El suyo (`ALERTS_ALREADY_SENT`…) |
| Cualquier otra | 500 | `INTERNAL_ERROR`, "Ocurrió un error inesperado." y una línea en el *log* |

En local (`APP_DEBUG=true`) se agrega `debug: {exception, file, line}`. En producción, nunca.

## 4. `BusinessError`

```php
final class AlertAlreadySent extends BusinessError
{
    public function code(): string { return ErrorCode::AlreadySent->value; }   // ALERTS_ALREADY_SENT
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

- [ ] `ModuleServiceProvider` y el registro de los 9 *providers* en `bootstrap/providers.php`
- [ ] `ApiResponse` · `GeneralCode`
- [ ] `BusinessError` y el manejador de `bootstrap/app.php`
- [ ] `Transaction` · `MysqlTransaction`
- [ ] `ForceJsonResponse`
- [ ] `app/Modules/*/Tests` agregado a `phpunit.xml`
- [ ] Prueba: cada tipo de error responde con su formato

## 7. Cómo quedó hecho

_Pendiente._

> 📝 **Documentar siempre:** este documento se actualiza en el mismo *pull request* que cambia el código.
