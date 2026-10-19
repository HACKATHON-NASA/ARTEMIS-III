# M03 · Ingesta de datos · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

### `POST /api/v1/ingesta/lotes`

Autenticación: **solo token** de dispositivo con habilidad `lecturas:escribir` (middleware `TokenDeTipo:dispositivo` de [M01](../../M01-identidad/backend/README.md#33-tokens); una sesión de navegador recibe `401`). Máximo **5.000 lecturas por lote**.

```json
{
  "lote_id": "7f3c2a10-5b1e-4f7e-9a2d-0c8e1b4d6f21",
  "dispositivo_id": "astroskin-T2",
  "tripulante_id": "T2",
  "ubicacion": null,
  "modo": "vivo",
  "enviado_en": "2026-10-12T14:05:00Z",
  "bateria_pct": 82,
  "lecturas": [
    { "id": "0d6b2f4e-1c3a-4e8b-9f21-6a7c5d3e2b10", "codigo": "8867-4", "valor": 72, "unidad": "/min",
      "medido_en": "2026-10-12T14:04:00Z", "calidad": 0.98, "contexto": "reposo" }
  ]
}
```

- `dispositivo_id` es el **identificador** del dispositivo (M02), `tripulante_id` es el **código** del tripulante (`T1`…) y `ubicacion` es el **código** del módulo de la nave (lecturas ambientales). Un lote es personal **o** ambiental, no ambos. `bateria_pct` (0–100) es opcional: si llega, M02 actualiza la batería del dispositivo.
- `contexto` es **obligatorio** en las lecturas personales y debe ser `null` en las ambientales.
- `calidad` es opcional; si falta, se guarda `null` y la lectura **no** se considera artefacto.
- `modo` es `vivo` (sensor o simulador en tiempo real) o `historico` (relleno del simulador). Cambia dos cosas: el aviso de colocación solo mira lotes `vivo` (regla 7) y los lotes `historico` se procesan con la cola de baja prioridad ([§3.2](#32-rendimiento-en-cpanel)).
- Es el contrato de [04 §4.2](../../../docs/propuesta/04-manejo-de-datos.md#42-contrato-de-recepción-ejemplo), con cuatro campos añadidos: `ubicacion`, `modo`, `bateria_pct` y la regla del contexto.

| Respuesta | Cuándo | Cuerpo |
|---|---|---|
| `201 Created` | Lote nuevo procesado | `{ lote_id, guardadas, artefactos, rechazadas, duplicadas }` |
| `200 OK` | El `lote_id` ya existía (también si llegó a la vez por dos conexiones) | El mismo resumen del primer procesamiento; no se guarda nada |
| `422 Unprocessable Entity` | Esquema inválido, tripulante o módulo de la nave desconocido | Errores por campo (`tripulante_desconocido`, `ubicacion_desconocida`…) |
| `401` / `403` | Token inválido o de sesión (`401`); el dispositivo no puede escribir para ese tripulante o ubicación (`403`) | — |

### `GET /api/v1/ingesta/periodo?desde=&hasta=`

Autenticación: token de un dispositivo de tipo `simulador` (otro token → `403`). Devuelve `{ hay_lecturas, lotes }` según `lotes_ingesta.medido_desde` y `medido_hasta`. Lo usa el simulador para no repetir un relleno histórico sobre datos existentes.

> La antigua `GET /api/v1/ingesta/estado-sensores` **no se construye**: F1 recibe el estado de los sensores como *prop* (con `usePoll`), y el simulador, que la usaba con su token de dispositivo, no estaba autorizado. Detalle en las [historias de backend](HU/README.md).

## 2. Eventos

| Publica | Consume |
|---|---|
| `LoteIngerido` (un evento por lote, tras confirmar la transacción) | — |

## 3. Reglas de negocio

### 3.1 Procesamiento de un lote

Se aplican en este orden ([04 §4.1](../../../docs/propuesta/04-manejo-de-datos.md#41-flujo-completo)):

1. **Esquema:** si falla, `422` y no se guarda nada. También es `422` un `tripulante_id` o una `ubicacion` que no existen en la misión activa.
2. **Autorización del dispositivo** (antes que la idempotencia, para que un `lote_id` ajeno no devuelva el resumen de otro dispositivo): el `dispositivo_id` del lote debe ser el dispositivo dueño del token. El token del dispositivo `simulador` es la excepción: puede enviar lotes **en nombre de** cualquier dispositivo de la misión (`astroskin-T2`, `cgm-T3`…), para que cada dispositivo simulado tenga su último contacto, su batería y su estado de colocación. **En todos los casos**, el dispositivo nombrado debe estar asignado a ese tripulante o ubicación (`403`), y no puede ser de tipo `tableta` ni `simulador` (`422`). El `origen` de las lecturas es `simulador` si el **dueño del token** es el simulador, y `sensor` en otro caso.
3. **Idempotencia de lote:** si `lote_id` existe, `200` con el resumen original. La fila de `lotes_ingesta` se inserta **al empezar** la transacción: si dos envíos iguales llegan a la vez, el segundo choca con la clave primaria, espera a que el primero confirme y responde `200` con su resumen, leído fuera de la transacción (nunca un `500`).
4. **Por cada lectura:**
   1. si su `id` ya existe → se cuenta como `duplicada` y se omite (se inserta con `insertOrIgnore`, así una carrera entre dos lotes distintos tampoco produce un error);
   2. si el `codigo` no está en `catalogo_variables` → `rechazada` (`codigo_desconocido`); si es un código `derivado` → `codigo_no_admitido`; si es de M08 en un lote personal, u otro código en un lote ambiental → `ambito_invalido`;
   3. **normalización de unidad:** se aceptan la unidad del catálogo y estas conversiones: `[degF]` → `Cel`, `[lb_av]` → `kg`; otra unidad → `rechazada` (`unidad_invalida`);
   4. si el valor normalizado está fuera del **rango físicamente posible** del catálogo → `rechazada` (`fuera_de_rango`); ej.: FC de 400 lpm;
   5. si `medido_en` está más de 5 minutos en el futuro respecto al servidor → `rechazada` (`marca_de_tiempo`);
   6. si `calidad < 0,5` → se guarda con `es_artefacto = true` (no dispara reglas).
5. Se guarda todo en **una transacción** y se actualizan el último contacto y, si llega, la batería del dispositivo con `ConsultaDispositivos::marcarContacto(identificador, bateria_pct)` (M02).
6. Se publica `LoteIngerido`, solo si se guardó al menos una lectura.
7. **Sensor mal colocado (HU-03.03):** se calcula al consultar, sin tabla propia, y solo con lotes `vivo` (la consulta une `observaciones` con `lotes_ingesta.modo`, porque `observaciones` no tiene `modo`). Los dispositivos `simulador` y `tableta` no tienen estado:
   - `revisar_colocacion` si el dispositivo tiene **3 o más artefactos** con `medido_en` en los **últimos 5 minutos**;
   - `ok` en cuanto pasan 5 minutos sin artefactos (`colocacion_ventana_min`);
   - `sin_datos` si `dispositivos.ultimo_contacto_en` (M02) tiene más de **10 minutos** (`sensor_sin_datos_min`), **solo** en los dispositivos de envío continuo (`wearable`, `oximetro`, `dosimetro`, `cgm` y `monitor_ambiental`). El tensiómetro (una vez al día), el medidor de masa (una vez por semana) y el actígrafo (el sueño entra por M06, no por lote) nunca están `sin_datos`.
   - En el nodo Tierra no hay estado (`[]`): el último contacto no se sincroniza.

   Los artefactos antiguos del relleno histórico no generan avisos. **No** genera una alerta clínica.

En el MVP **todas** las observaciones entran por lote: `lote_id` y `dispositivo_id` siempre tienen valor. El origen `manual` del contrato queda reservado para una carga manual futura. Los registros de formulario (sueño, tests, síntomas, ejercicio…) viven en las tablas de su módulo, no en `observaciones`.

### 3.2 Rendimiento en cPanel

El relleno histórico de 30 días son unos **14.000 lotes** (un lote por dispositivo y hora: wearable, oxímetro, dosímetro y CGM de cada tripulante, más el monitor de cada módulo de la nave), cada uno con su `LoteIngerido` y varios *listeners* (M04, M09, M12, M15). En cPanel la cola corre por cron una vez por minuto, así que hay que dimensionarla:

| Elemento | Decisión |
|---|---|
| Recepción | Síncrona en la petición: valida, inserta en bloques y confirma. Objetivo: < 1 s por lote de 5.000 lecturas |
| *Listeners* | En cola. Los de lotes `vivo` van a la cola `default`; los de `historico`, a la cola `historico`, para que el relleno no retrase la demo en vivo |
| Trabajador | El *scheduler* ejecuta cada minuto `queue:work --queue=default,historico --stop-when-empty --max-time=55` (sin solaparse: `withoutOverlapping`) |
| Prueba de carga | Al cierre del **Sprint 1**, con HU-03.01 (en el Sprint 0 el endpoint aún no existe): `php artisan ingesta:prueba-carga` envía 200 lotes de 5.000 lecturas al nodo nave en cPanel y mide el tiempo de recepción; `ingesta:estado-cola` mide el vaciado de la cola. Si el relleno completo tarda más de 1 hora en procesarse, se cargan los 30 días en local, se exporta la base y se importa en cPanel. En el Sprint 0 solo se verifica que el cron y la cola funcionan |

## 4. Contratos

| Ofrece (`Ingesta\Contratos\…`) | Uso |
|---|---|
| `ConsultaObservaciones::serie(tripulanteId, codigo, desde, hasta, contexto = null)` | M04, M09, M10, M12 |
| `ConsultaObservaciones::serieUbicacion(moduloNaveId, codigo, desde, hasta)` | M08, M12 |
| `ConsultaObservaciones::ultima(tripulanteId, codigo)` | F1 y F2 a través de M04 |
| `ImportacionLotes::importar(array $lotes, array $observaciones)` | M15 en el nodo Tierra (el paquete `detalle` lleva también las filas de `lotes_ingesta`, por la clave foránea de `observaciones.lote_id`) |
| `EstadoSensores::porTripulante(tripulanteId)` | F1 |
| `EstadoSensores::todos()` (identificador → `ok`, `revisar_colocacion` o `sin_datos`) | M02 (columna de estado de `Admin/Dispositivos/Index`), desde HU-03.03 |

Las consultas **excluyen los artefactos** salvo que se pida `incluirArtefactos: true`.

**Importación en el nodo Tierra:** `ImportacionLotes` guarda las lecturas **tal como las validó la nave** (con su `es_artefacto` y su `origen`) y conserva la idempotencia por `lote_id` y por `id`. **No** repite la autorización del dispositivo ni las validaciones de catálogo, rango y marca de tiempo: en la Tierra no hay tokens de dispositivo, y el paquete `detalle` puede llegar antes que el paquete `mision` que crea los dispositivos.

| Usa | De |
|---|---|
| `ConsultaDispositivos`, `ConsultaTripulantes::porCodigo()`, `ConsultaModulosNave::porCodigo()` | M02 |
| `CatalogoVariables::porCodigo()` | M16 |

## 5. Permisos

| Acción | Quién |
|---|---|
| Enviar lotes | Tokens de dispositivo con `lecturas:escribir` (dispositivos y simulador); nunca una sesión |
| Ver el estado de los sensores | El propio astronauta; *Gate* `ver-estado-dispositivos` (`cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin`) |

## 6. Archivos

```
app/Modules/Ingesta/
├── IngestaServiceProvider.php
├── routes.php
├── Contratos/{ConsultaObservaciones,ImportacionLotes,EstadoSensores}.php
├── Domain/
│   ├── Enums/{Contexto,Origen,ModoLote,MotivoRechazo}.php
│   ├── Reglas/{ValidadorLectura,NormalizadorUnidades,DetectorColocacion}.php
│   └── Eventos/LoteIngerido.php
├── Application/{ProcesarLote,ImportarLote,ConsultarEstadoSensores}.php
├── Console/{PruebaCargaCommand,EstadoColaCommand}.php   # ingesta:prueba-carga, ingesta:estado-cola
├── Http/
│   ├── Controllers/Api/{LoteController,PeriodoController}.php
│   ├── Requests/LoteRequest.php
│   └── Resources/ResumenLoteResource.php
├── Infrastructure/
│   ├── Models/{LoteIngesta,Observacion}.php
│   └── Repositorios/ObservacionesEloquent.php
├── Database/Migrations/{create_lotes_ingesta,create_observaciones}.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Reenviar el mismo lote devuelve `200` con el mismo resumen y no inserta filas.
- [ ] Dos envíos simultáneos del mismo lote devuelven `201` y `200`, nunca `500`.
- [ ] Una lectura repetida en otro lote se cuenta como `duplicada`.
- [ ] 98,6 `[degF]` se guarda como 37,0 `Cel`.
- [ ] Un lote mixto (personal y ambiental) devuelve `422`; un lote personal con una lectura sin `contexto`, también.
- [ ] Un lote para `T9` (no existe) devuelve `422` con `tripulante_desconocido`.
- [ ] Un token de `astroskin-T2` no puede escribir lecturas de T3 (`403`); una sesión de navegador recibe `401`.
- [ ] Un lote del simulador en nombre de `cgm-T3` guarda las lecturas con `origen = simulador`; en nombre de `cgm-T3` para T2, `403`.
- [ ] `AH-PCO2` en un lote personal se rechaza con `ambito_invalido`; un código derivado, con `codigo_no_admitido`.
- [ ] Un lote de 5.001 lecturas devuelve `422`.
- [ ] El token de `astroskin-T2` no puede enviar un lote con `dispositivo_id = cgm-T2`; el token del simulador sí.
- [ ] Una lectura sin `calidad` no se marca como artefacto.
- [ ] Tres artefactos en 5 minutos en un lote `vivo` → `revisar_colocacion`; los mismos artefactos en un lote `historico` → `ok`.
- [ ] En el nodo Tierra, importar un lote cuyo dispositivo aún no existe guarda las lecturas igual.
