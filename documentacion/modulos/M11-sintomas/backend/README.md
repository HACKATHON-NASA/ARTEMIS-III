# M11 · Síntomas · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

Autenticación: solo token ([M01 §3.3](../../M01-identidad/backend/README.md#33-tokens)): PWA `registros:escribir` o el del dispositivo `simulador` (para E3). Idempotentes por `id`.

| Método | Ruta | Cuerpo |
|---|---|---|
| `POST` | `/api/v1/sintomas` | `{ id, tripulante_id, tipo, intensidad, inicio_en, notas }` |
| `POST` | `/api/v1/sintomas/mareo` | `{ id, tripulante_id, nausea, vomito, mareo, cefalea, malestar, respondida_en }` (el `dia_mision` lo calcula el servidor) |
| `GET` (web) | `/sintomas/fotos/{id}` | Descarga de la foto: el dueño o `ver-datos-tripulacion` (`403` si no); auditoría `ver` si la abre otra persona |
| `POST` | `/api/v1/sintomas/{id}/foto` | *Multipart*: `foto_id` (UUID) y `archivo` (JPEG). Idempotente por `foto_id`; `404` si el síntoma aún no existe (la cola lo reintenta); `422` si el síntoma ya tiene otra foto |

Con el token de la PWA, el `tripulante_id` se toma del usuario y se ignora el del cuerpo; con el del simulador, el `tripulante_id` (código) es obligatorio. La foto solo la puede subir el dueño del síntoma (`403` en otro caso).

## 2. Eventos

| Publica | Consume |
|---|---|
| `ObservacionRegistrada` por cada síntoma (`AH-SINTOMA-<TIPO>`, valor = intensidad, unidad `{score}`) | — |

La foto no publica eventos: M11 la encola hacia la Tierra con `ColaSincronizacion::encolar('adjunto', 3, …)` (M15).

## 3. Reglas de negocio

1. El cuestionario de mareo aparece en F1 los **días de misión 1, 2 y 3** (día de misión de M02), una vez por día; deja de aparecer al responderlo. El `dia_mision` de la respuesta lo calcula el servidor a partir de `respondida_en` (`ConsultaMision::diaMision`), así que un cuestionario del día 3 respondido sin conexión y enviado el día 4 se guarda en el día 3. Una respuesta con `respondida_en` fuera de los días 1 a 3 da `422`.
   > **Para la demo:** con la misión de demostración (empieza 29 días antes de hoy) el cuestionario ya no aparece. Para mostrarlo, se usa una misión que empiece hoy (`MISION_INICIO`) en un entorno aparte, o se muestran sus pruebas y la gráfica con datos cargados.
2. `total` = suma de los cinco ítems; la evolución se muestra como gráfica de los tres días. Un segundo cuestionario del mismo día con otro `id` → `422` (con el mismo `id`, `200`). Su ítem "cefalea" **no** cuenta para `CO2_CEFALEA`: esa regla solo mira los síntomas registrados.
3. Un síntoma de intensidad 0 se acepta (registra que el síntoma desapareció). `inicio_en` no puede estar más de 5 minutos en el futuro.
4. **Foto (HU-11.03):** se captura con `<input type="file" accept="image/*" capture="environment">`, se **comprime en el cliente** a JPEG (lado mayor 1.280 px, calidad 0,8) y, si sigue pasando de `adjunto_foto_max_kb` (hoy 500 KB), se reduce la calidad hasta cumplir. Se guarda como `Blob` en la cola offline **después** del síntoma, para que llegue en orden. El servidor verifica que el contenido sea JPEG y lo **vuelve a codificar sin metadatos EXIF** (fecha, modelo del dispositivo, ubicación): la foto es un dato de salud y no debe llevar información que no hace falta. Como eso borra también la orientación, el cliente endereza la foto al comprimirla. Se guarda en `sintomas/<tripulante_id>/<foto_id>.jpg` del disco privado.
5. M11 no evalúa umbrales. Las cefaleas alimentan `ReglaCo2Cefalea` (M08) y aparecen en el contexto de las alertas. Los códigos `AH-SINTOMA-<TIPO>` solo viajan en los eventos: **no** se siembran en `catalogo_variables` (algunos superan sus 20 caracteres).
6. **Historial para la tripulación:** pestaña "Síntomas" del CMO y página `Tierra/Sintomas/Show` del médico de vuelo (nueva). Detalle en las [historias de backend](HU/README.md).

## 4. Contratos

| Ofrece (`Sintomas\Contratos\…`) | Uso |
|---|---|
| `ConsultaSintomas::enVentana(tripulanteId, tipo, desde, hasta)` | M08 (correlación), M12 (contexto), M14 |

| Usa | De |
|---|---|
| `CatalogoSintomas` | M16 |
| `ConsultaMision::activa()` (día de misión) | M02 |
| `ColaSincronizacion`, `Importador` | M15 (foto hacia la Tierra) |
| `ParametrosMision` (`adjunto_foto_max_kb`) | M16 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Registrar y ver los síntomas propios | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver los síntomas de la tripulación (con sus fotos) | `ver-datos-tripulacion` (`cmo`, `medico_vuelo`) |

## 6. Archivos

```
app/Modules/Sintomas/
├── SintomasServiceProvider.php
├── routes.php
├── Contratos/ConsultaSintomas.php
├── Application/{RegistrarSintoma,RegistrarCuestionarioMareo,DebeMostrarseMareo,AdjuntarFoto}.php
├── Http/
│   ├── Controllers/Web/SintomasController.php
│   ├── Controllers/Web/FotoController.php           # Descarga con permiso
│   ├── Controllers/Api/{SintomaController,MareoController,FotoController}.php
│   └── Requests/{SintomaRequest,MareoRequest,FotoRequest}.php
├── Infrastructure/
│   ├── Models/{SintomaRegistrado,RespuestaMareo,FotoSintoma}.php
│   └── Repositorios/SintomasEloquent.php
├── Database/Migrations/{create_sintomas_registrados,create_respuestas_mareo,create_fotos_sintoma}.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Un tipo de síntoma que no está en el catálogo devuelve `422`.
- [ ] Responder dos veces el cuestionario del mismo día no crea dos registros.
- [ ] El día 4 de misión el cuestionario ya no aparece.
- [ ] Reenviar la misma foto devuelve `200` sin duplicarla; un PNG renombrado a `.jpg` devuelve `422`.
- [ ] La foto guardada no tiene metadatos EXIF.
- [ ] T3 no puede subir una foto al síntoma de T2 (`403`).
- [ ] Un cuestionario respondido el día 3 y enviado el día 4 queda en el día 3; uno respondido el día 4 devuelve `422`.
- [ ] El token del simulador sin `tripulante_id` recibe `422`.
