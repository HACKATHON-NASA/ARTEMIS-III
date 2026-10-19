# 3 · Backend con Laravel

> [← Guía](README.md) · Anterior: [Arquitectura](02-arquitectura.md) · Siguiente: [API, JSON y errores](04-api-y-errores.md)

Manual práctico de la norma [ARCHITECTURE.md](ARCHITECTURE.md): explica **para qué sirve cada capa** y **dónde se programa cada cosa**, con un ejemplo completo de la ruta a la prueba. Si este manual y la norma difieren, manda la norma. La estructura de carpetas de un módulo está en [ARCHITECTURE §5](ARCHITECTURE.md#5-estructura-de-un-módulo).

## 1. Las capas, una por una

### Ruta (`routes.php`)

Une una URL con un controlador y le pone los guardias (middleware). Cada módulo tiene un solo `routes.php` con dos grupos: el **web** (pantallas, con sesión) y el **API** (`/api/v1/<modulo>`, con token).

- Va aquí: método HTTP, URL, controlador, middleware (`auth`, `can:<gate>`, `TokenDeTipo`, `SoloEnNodo`) y nombre de la ruta.
- **No** va aquí: ninguna lógica (nada de funciones anónimas que hagan algo).

### Capa HTTP: `Http/` (la "capa de API")

Es la frontera con el exterior. Tiene tres piezas:

| Pieza | Para qué sirve | Ejemplo |
|---|---|---|
| **Request** (`Http/Requests/`) | Valida que los datos vengan bien formados y los convierte en un objeto de datos | `SintomaRequest`: `intensidad` es un entero de 0 a 10 |
| **Controller** (`Http/Controllers/Web` o `Api`) | Recibe la petición ya validada, llama a **un** servicio y devuelve la respuesta. Debe ser **corto** (unas pocas líneas por método) | `SintomaController@store` |
| **Resource** (`Http/Resources/`) | Decide la forma exacta del JSON o de las *props*: qué campos, con qué nombres y formatos | `SintomaResource` |

**Un controlador nunca** escribe consultas, calcula reglas de negocio ni llama a otro módulo. Si lo hace, ese código va en el servicio.

### Capa de servicio: `Application/` (casos de uso)

Es el corazón de cada historia. **Una clase por acción**, nombrada con un verbo (`RegistrarSintoma`, `EmitirTokenDispositivo`, `GuardarUsuario`), con un método público `ejecutar(...)`.

- Va aquí: los pasos de la acción, en orden (comprobar, guardar, publicar el evento, encolar hacia la Tierra), la transacción y las llamadas a los contratos de otros módulos.
- **No** va aquí: leer `$request`, devolver JSON o páginas, ni escribir SQL. Recibe datos ya validados y devuelve objetos.
- Así el mismo servicio sirve desde una pantalla, desde la API y desde un comando de consola.

### Dominio: `Domain/`

Lo que el negocio **es**, sin base de datos ni HTTP: se puede probar con pruebas unitarias puras.

| Carpeta | Qué va | Ejemplo |
|---|---|---|
| `Domain/Enums/` | Valores cerrados (*backed enums* de tipo `string`) | `Rol`, `NivelAlerta` |
| `Domain/Reglas/` | Cálculos y reglas puras | Mediana y lapsos del PVT |
| `Domain/Eventos/` | Eventos que publica el módulo | `ObservacionRegistrada` |
| `Domain/ValueObjects/` | Objetos de datos inmutables | `DatosSintoma` |
| `Domain/Excepciones/` | Errores de negocio del módulo ([formato](04-api-y-errores.md#errores-de-negocio)) | `SintomaNoEncontrado` |

### Repositorio: `Infrastructure/`

La **única** parte del módulo que sabe de MySQL.

| Carpeta | Qué va |
|---|---|
| `Infrastructure/Models/` | Modelos Eloquent, **solo de las tablas propias** |
| `Infrastructure/Repositorios/` | Repositorios de **lectura** (`<Agregado>LecturaRepositorio`: `porId`, `enVentana`…, implementan los `Contratos/` que el módulo ofrece) y de **escritura** (`<Agregado>EscrituraRepositorio`: `guardar`, `marcarConfirmada`…) |
| `Infrastructure/Listeners/` | Reacciones a eventos de **otros** módulos |
| `Infrastructure/Sincronizacion/` | El `Importador` para M15, si los datos viajan entre nodos |

Un repositorio **no** decide reglas: solo guarda y consulta.

### Contratos: `Contratos/`

Las interfaces que el módulo **ofrece** a los demás (`ConsultaSintomas`). Es lo único de un módulo que otro módulo puede usar. El *ServiceProvider* las une con su implementación (`$this->app->bind(ConsultaSintomas::class, SintomaLecturaRepositorio::class)`).

### Otras piezas

| Pieza | Dónde | Para qué |
|---|---|---|
| *ServiceProvider* | `<Modulo>ServiceProvider.php` | Registra rutas, migraciones, contratos, *listeners*, *Gates* y tareas programadas del módulo |
| Permisos | *Gates* de [M01 §5](../M01-identidad/backend/README.md#5-permisos) y `Policies/` del módulo | Quién puede hacer qué. Los nombres de los *Gates* los define solo M01 |
| Comandos | `Console/` | Tareas de consola (`identidad:token-nodo`); las programadas se registran en el *provider* |
| Migraciones y *seeders* | `Database/` | Ver [Base de datos](06-base-de-datos.md) |
| Pruebas | `Tests/Unit` y `Tests/Feature` | Ver el punto 4 |

## 2. ¿Dónde programo esto?

| Necesito… | Va en |
|---|---|
| Validar que un campo es obligatorio, un UUID o un número en un rango | `Http/Requests/…Request` |
| Comprobar que el usuario tiene permiso | Middleware `can:<gate>` en la ruta, o `Gate::authorize()` / *Policy* |
| Decidir qué pasos sigue una acción | `Application/<Verbo><Cosa>` |
| Un cálculo o regla del negocio | `Domain/Reglas/` (y lo llama el servicio) |
| Una consulta a la base | `Infrastructure/Repositorios/` |
| Datos de **otro** módulo | Su contrato en `Contratos/`, inyectado en el servicio |
| Avisar a otros módulos de que algo pasó | Un evento en `Domain/Eventos/`, publicado desde el servicio |
| Reaccionar a algo que pasó en otro módulo | Un *listener* en `Infrastructure/Listeners/` |
| Un umbral, un límite o un tiempo clínico u operativo | **Nunca en el código:** `ParametrosMision::get()` o `ConsultaUmbrales::vigentes()` de M16 |
| Un límite técnico (tamaño de lote, reintentos, caché) | Constante con nombre en la clase que lo usa |
| La forma del JSON o de las *props* | `Http/Resources/` |
| Un texto que ve el usuario | En la clase que lo produce, **exacto** como lo dice la historia |

## 3. Ejemplo completo: registrar un síntoma (HU-11.01)

**Ruta** (`app/Modules/Sintomas/routes.php`):

```php
use App\Modules\Identidad\Http\Middleware\TokenDeTipo;
use App\Modules\Sintomas\Http\Controllers\Api\SintomaController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/sintomas')
    ->middleware(['api', 'auth:sanctum', TokenDeTipo::class.':pwa,dispositivo'])
    ->group(function () {
        Route::post('/', [SintomaController::class, 'store'])->name('api.sintomas.store');
    });
```

**Request** (`Http/Requests/SintomaRequest.php`): valida y entrega un objeto de datos.

```php
final class SintomaRequest extends FormRequest
{
    public function rules(CatalogoSintomas $catalogo): array
    {
        return [
            'id'         => ['required', 'uuid'],
            'tipo'       => ['required', 'string', Rule::in($catalogo->activos())],
            'intensidad' => ['required', 'integer', 'between:0,10'],
            'inicio_en'  => ['required', 'date'],
            'notas'      => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return ['tipo.in' => 'Ese síntoma no está en el catálogo.'];
    }

    public function datos(string $tripulanteId): DatosSintoma
    {
        return new DatosSintoma(
            id: $this->string('id'),
            tripulanteId: $tripulanteId,
            tipo: $this->string('tipo'),
            intensidad: $this->integer('intensidad'),
            inicioEn: $this->date('inicio_en'),
            notas: $this->input('notas'),
        );
    }
}
```

**Controller** (`Http/Controllers/Api/SintomaController.php`): corto.

```php
final class SintomaController
{
    public function store(SintomaRequest $request, TripulanteDelToken $tripulante, RegistrarSintoma $registrar): JsonResponse
    {
        $resultado = $registrar->ejecutar($request->datos($tripulante->de($request)));

        return $resultado->creado
            ? RespuestaApi::creado(CodigoSintomas::Registrado, 'Síntoma registrado.', SintomaResource::make($resultado->sintoma))
            : RespuestaApi::ok(CodigoSintomas::YaRegistrado, 'El síntoma ya estaba registrado.', SintomaResource::make($resultado->sintoma));
    }
}
```

**Servicio** (`Application/RegistrarSintoma.php`): los pasos.

```php
final class RegistrarSintoma
{
    public function __construct(
        private SintomaLecturaRepositorio $lectura,
        private SintomaEscrituraRepositorio $escritura,
        private Transaccion $transaccion,
    ) {}

    public function ejecutar(DatosSintoma $datos): ResultadoRegistro
    {
        if ($existente = $this->lectura->porId($datos->id)) {
            return ResultadoRegistro::existente($existente);   // idempotencia: 200
        }

        return $this->transaccion->ejecutar(function () use ($datos) {
            $sintoma = $this->escritura->guardar($datos);
            event(ObservacionRegistrada::deSintoma($sintoma));   // sale después del commit
            return ResultadoRegistro::creado($sintoma);
        });
    }
}
```

**Repositorios** (`Infrastructure/Repositorios/`): la única capa que toca MySQL, separada en lectura y escritura. El de lectura implementa el contrato `ConsultaSintomas` que usan M08, M12 y M14.

```php
final class SintomaLecturaRepositorio implements ConsultaSintomas
{
    public function porId(string $id): ?SintomaRegistrado
    {
        return SintomaRegistrado::find($id);
    }

    public function enVentana(?string $tripulanteId, string $tipo, CarbonInterface $desde, CarbonInterface $hasta): Collection
    {
        // … devuelve DTOs: los contratos nunca exponen modelos Eloquent
    }
}

final class SintomaEscrituraRepositorio
{
    public function guardar(DatosSintoma $d): SintomaRegistrado
    {
        return SintomaRegistrado::create([
            'id' => $d->id, 'tripulante_id' => $d->tripulanteId, 'tipo' => $d->tipo,
            'intensidad' => $d->intensidad, 'inicio_en' => $d->inicioEn, 'notas' => $d->notas,
        ]);
    }
}
```

**Resource** (`Http/Resources/SintomaResource.php`): la forma del JSON.

```php
final class SintomaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'tipo'       => $this->tipo,
            'intensidad' => $this->intensidad,
            'inicio_en'  => $this->inicio_en->toIso8601ZuluString('millisecond'),
        ];
    }
}
```

> `TripulanteDelToken`, `DatosSintoma` y `ResultadoRegistro` son nombres de ejemplo. Lo que cuenta es **qué hace cada capa**, no el nombre de esas clases auxiliares.

## 4. Pruebas (Pest)

**Primero la prueba, después el código:** cada criterio *Dado / Cuando / Entonces* de la historia es una prueba ([plan §10](../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).

```php
it('registra una cefalea y publica la observación', function () {
    Event::fake([ObservacionRegistrada::class]);
    $token = tokenPwaDe(astronauta('T2'));

    $this->withToken($token)->postJson('/api/v1/sintomas', [
        'id' => $id = (string) Str::uuid(), 'tipo' => 'cefalea', 'intensidad' => 6,
        'inicio_en' => now()->toIso8601ZuluString(),
    ])->assertCreated()
      ->assertJsonPath('success', true)
      ->assertJsonPath('code', 'SINTOMAS_REGISTRADO')
      ->assertJsonPath('data.intensidad', 6);

    Event::assertDispatched(ObservacionRegistrada::class, fn ($e) => $e->codigo === 'AH-SINTOMA-CEFALEA');
});

it('no duplica si el mismo id llega dos veces', function () { /* …assertOk() la segunda vez */ });
```

| Tipo | Carpeta | Qué prueba |
|---|---|---|
| *Feature* | `Tests/Feature` | La ruta completa: permisos, validación, respuesta y lo que queda en la base |
| *Unit* | `Tests/Unit` | Reglas de `Domain/` sin base de datos |

## 5. Convenciones de nombres

La tabla oficial está en [ARCHITECTURE §17](ARCHITECTURE.md#17-convenciones-de-nombres), y las prácticas prohibidas, en [§25](ARCHITECTURE.md#25-prácticas-prohibidas).
