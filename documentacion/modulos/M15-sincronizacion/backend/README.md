# M15 · Sincronización · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

Solo en el **nodo Tierra**. Autenticación: token del nodo con `sync:escribir` ([M01](../../M01-identidad/database/README.md#1-tablas)).

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/api/v1/sync/paquetes` | La nave entrega paquetes: `{ paquetes: [{ id, tipo, prioridad, comprimido, creado_en, payload }] }`. **Solo los guarda** (`recibido`) y responde con los `id` recibidos; un `id` ya recibido se confirma sin duplicarlo. La importación la hace después `sync:procesar-recibidos` (cada minuto, en los dos nodos), porque importar miles de paquetes en la petición no cabe en cPanel |
| `GET` | `/api/v1/sync/paquetes/salientes?max_bytes=N&max_paquetes=M` | La nave recoge los paquetes pendientes de la Tierra (por prioridad y antigüedad) hasta su cupo |
| `POST` | `/api/v1/sync/paquetes/acuse` | La nave confirma los `id` recogidos; la Tierra los marca `entregado` |

## 2. Eventos

| Publica | Consume (solo eventos **locales**, nunca los producidos al importar) |
|---|---|
| `RecomendacionRecibida` (en la nave, al procesar una recomendación) | `AlertaCreada`, `AlertaEscalada` (M13) → paquete `alerta` |
| | `LoteIngerido` (M03) → paquete `detalle` |
| | `ObservacionRegistrada`, `TestCognitivoCompletado`, `ResultadoExamenCargado` → paquete `registros` |
| | Además, los registros que **no publican eventos** se encolan directamente con `ColaSincronizacion::encolar('registros', 2, …)` desde su módulo: escalas psicológicas y sesiones incompletas (M05), cuestionarios de mareo (M11) y registros de sueño solo con medicación (M06). Sin esto, el médico de vuelo nunca vería las escalas ni el mareo en la Tierra |
| | `UmbralModificado` (M16) → paquete `configuracion` (Tierra → nave) o `ajuste_umbral` (nave → Tierra) |

## 3. Reglas de negocio

### 3.1 Cola offline de la PWA (HU-15.04)

| Elemento | Definición |
|---|---|
| Almacenes de IndexedDB (Dexie) | `cola_envios` `{ id, user_id, endpoint, metodo, cuerpo, multipart, creado_en, intentos, siguiente_intento_en, ultimo_error }`, `rechazados`, `instantaneas` (última copia de cada página de consulta), `credenciales` `{ user_id, token, expira_en }` (un token `registros:escribir` por usuario) |
| Dueño de cada registro | Cada elemento guarda el `user_id` de quien lo registró y **solo se envía con el token de ese usuario**. En una tableta compartida, los pendientes de otro usuario esperan a que vuelva a iniciar sesión ([M01 frontend §5](../../M01-identidad/frontend/README.md#5-comportamiento)) |
| Cuándo se reintenta | Al abrir la app, con el evento `online` y cada 60 s mientras hay red |
| Respuesta `201` o `200` | Se elimina de la cola |
| Respuesta `422`, `403` o `413` | Pasa a `rechazados` y se muestra al usuario con el motivo |
| Respuesta `401` | Se pausa la cola y se pide iniciar sesión |
| Error de red o `5xx` | Se reintenta con espera creciente (1, 2, 4… hasta 60 s) |
| Idempotencia | El `id` (UUID) del registro se genera **antes** de guardarlo en la cola |
| Archivos | `cuerpo` puede contener un `Blob` (foto del síntoma); se envía como *multipart*. Un `404` en `/sintomas/{id}/foto` se trata como reintentable |
| Service Worker | `vite-plugin-pwa` precarga JS, CSS y fuentes (la alarma de M13 se genera con Web Audio: no hay archivo de sonido) |

### 3.2 Tipos de paquete y prioridades

| Tipo | Sentido | Prioridad | Contenido |
|---|---|:---:|---|
| `alerta` | Nave → Tierra | 1 si es `critico`, 2 si es `aviso` | Estado completo de la alerta, su caso y sus acciones; uno nuevo por cada cambio, y la Tierra aplica el más reciente (`actualizada_en`) |
| `ajuste_umbral` | Nave → Tierra | 2 | Ajuste temporal del CMO (HU-16.03) |
| `registros` | Nave → Tierra | 2 | Sesiones de test (completas e incompletas), escalas, sueño, síntomas, cuestionarios de mareo, exámenes, ejercicio, nutrición, fuerza, pruebas ortostáticas |
| `resumen` | Nave → Tierra | 2 | Agregados horarios (cada hora), reporte diario PDF (M14) y líneas base calculadas en la nave (M12) |
| `detalle` | Nave → Tierra | 3 | Observaciones crudas **agrupadas por dispositivo y hora** (un paquete por hora, aunque en vivo lleguen 60 lotes de un minuto), **junto con las filas de `lotes_ingesta` de esas lecturas** (las exige la clave foránea `observaciones.lote_id` al importar), y ventanas de ECG; el `payload` va comprimido (gzip) |
| `adjunto` | Nave → Tierra | 3 | Foto del síntoma (M11) o PDF del laboratorio (M07), en base64 |
| `auditoria` | Nave → Tierra | 3 | Entradas de auditoría de la nave (M01), en lotes por hora |
| `recomendacion` | Tierra → nave | 1 | Recomendación del médico de vuelo |
| `configuracion` | Tierra → nave | 2 | Umbrales, catálogos, parámetros, protocolos (M13), líneas base previas al vuelo (HU-02.03) y **solicitudes** de recálculo de línea base (la nave lo calcula y devuelve el resultado en `resumen`) |
| `mision` | Nave → Tierra | 2 | Misión, tripulantes, dispositivos y módulos de la nave cuando cambian (M02), con sus mismos `id` |

### 3.3 Enlace simulado (solo en el nodo nave)

El comando `sync:ciclo` se ejecuta **cada minuto** por cron ([03 §5](../../../docs/propuesta/03-propuesta-tecnologica.md#5-arquitectura-de-despliegue)). En cada ciclo:

1. **Entrega lo que ya está en tránsito** (aunque haya ventana sin enlace: la señal ya viaja):
   - paquetes de salida con `entrega_programada_en ≤ ahora` → `POST /api/v1/sync/paquetes` a la Tierra en peticiones de **4 MB como máximo** (`sync_max_bytes_peticion`, por debajo del `post_max_size` habitual de cPanel) → `entregado` cuando la Tierra confirma. Si la Tierra no responde (por ejemplo, el hosting está caído), el paquete sigue `enviado`, suma un `intento` y se reintenta en el siguiente ciclo: un fallo del servidor no es lo mismo que una ventana sin enlace y no se pierde nada;
   - paquetes recibidos `en_transito` con `entrega_programada_en ≤ ahora` → se importan → `procesado`.
2. Si hay una **ventana sin enlace activa**, termina aquí.
3. **Envía:** toma paquetes `pendiente` ordenados por `prioridad` y `creado_en` **hasta llenar el cupo en bytes** del perfil (o el máximo de paquetes, lo que llegue antes), los marca `enviado` y fija `entrega_programada_en = ahora + retardo`. Un paquete más grande que lo que queda del cupo espera al siguiente ciclo, salvo que sea el primero del ciclo (así un PDF de 2 MB no se queda atascado para siempre).
4. **Recoge:** pide a la Tierra hasta `cupo` paquetes salientes, los guarda como `en_transito` con `entrega_programada_en = ahora + retardo` y envía el acuse.

| Perfil | Retardo por sentido | Cupo por ciclo (parámetros de diseño †) |
|---|---|---|
| `leo` | 0 (siguiente ciclo) | 20 MB o 2.000 paquetes |
| `luna` | 0 (≈ 1,3 s: entra en el siguiente ciclo) | 5 MB o 1.000 paquetes |
| `marte` | `retardo_marte_min` (por defecto **12 min**, configurable de 3 a 22) | 1 MB o 200 paquetes |

> **Por qué el cupo es en bytes:** antes era de 500, 200 o 50 paquetes por ciclo sin importar su tamaño. 500 paquetes `detalle` de un lote histórico de 5.000 lecturas cada uno eran cientos de MB en una sola petición, que cPanel rechaza. Y un PDF de 2 MB "costaba" lo mismo que un mensaje de recomendación.

Con un ciclo por minuto, los retardos menores de un minuto se tratan como entrega en el siguiente ciclo ([03 §5](../../../docs/propuesta/03-propuesta-tecnologica.md#5-arquitectura-de-despliegue)).

### 3.4 Ventanas sin enlace (E5)

| Comando (nodo nave) | Efecto |
|---|---|
| `php artisan sync:ventana-sin-enlace --dias=14` | Crea una ventana `conjuncion` desde ahora durante 14 días (sin `--dias`, `conjuncion_dias` de M16) |
| `php artisan sync:cerrar-ventana` | Termina la ventana activa ahora (equivale a avanzar el reloj hasta el fin de la conjunción) |

**Cuánto tarda en vaciarse la cola tras 14 días sin enlace** (4 tripulantes, datos en vivo):

| | Paquetes | Tamaño comprimido aproximado | `leo` | `marte` |
|---|---:|---:|:-:|:-:|
| Antes (un `detalle` por lote de un minuto, cupo por paquetes) | ~300.000 | — | ~10 h | ~4 días |
| Ahora (`detalle` por dispositivo y hora, gzip, cupo en bytes) | ~10.000 | ~20 MB | ~5 min (manda el tope de 2.000 paquetes por ciclo) | ~50 min (tope de 200 paquetes por ciclo) |

En los dos casos los paquetes de prioridad 1 (alertas críticas) salen en el primer ciclo.

**Cómo mostrar E5 en la demo:** no se esperan 14 días reales. Con la ventana activa (`sync:ventana-sin-enlace --dias=14`), el simulador carga un histórico de los últimos días con un escenario que genere una alerta crítica; la cola de la nave crece mientras el nodo Tierra no recibe nada. Al ejecutar `sync:cerrar-ventana`, el panel del enlace muestra la crítica llegando primero y después el resto por prioridad.

### 3.5 Importación sin bucles

- Cada módulo cuyos datos viajan implementa `Sincronizacion\Contratos\Importador` (`tipo()`, `exportar(id)`, `importar(payload)`) y lo registra en su *ServiceProvider*.
- Mientras se importa, está activo `ContextoImportacion`, y **cada evento de dominio que se construye en ese momento guarda `importado = true`**. Los *listeners* de M12, M15 y M01 miran `$evento->importado` (no el contexto, que ya no existe cuando un *listener* en cola se ejecuta en otro proceso) y lo ignoran. Así un dato importado no se vuelve a enviar ni genera alertas duplicadas. La excepción es `UmbralModificado`: M12 lo procesa siempre para invalidar su caché, porque si no, un umbral recibido de la Tierra no se aplicaría. Detalle en las [historias de backend](HU/README.md).
- En el **nodo Tierra, M12 no evalúa reglas**: las alertas llegan de la nave.

### 3.6 Hora estimada de entrega (panel del enlace)

- En tránsito: `entrega_programada_en`.
- Pendiente: (fin de la ventana activa, o ahora) + retardo + N minutos, con N el mayor de ⌈bytes en la cola hasta ese paquete / cupo en bytes⌉ y ⌈posición en la cola / cupo en paquetes⌉. Por prioridad se da el intervalo del primero al último (`estimado_desde`, `estimado_hasta`).
- **En la Tierra** no corre el ciclo ni hay ventanas (las crea la nave): el panel muestra `ultimo_contacto_en` (la última petición de la nave) y los pendientes de su propia cola **sin** hora estimada, que depende de cuándo vuelva a llamar la nave.

## 4. Contratos

| Ofrece (`Sincronizacion\Contratos\…`) | Uso |
|---|---|
| `Importador` (interfaz) | M01, M02, M03, M04, M05, M06, M07, M10, M11, M12, M13, M14, M16 |
| `ColaSincronizacion::encolar(tipo, prioridad, payload)` | M01 (auditoría), M02 (misión y tripulación), M07 y M11 (adjuntos), M13 (alertas), M14 (reporte diario), M16 |
| `ConsultaRecomendaciones::deAlerta(alertaId)` (`{ texto, autor, prioridad, recibida_en }[]`) | M13 (página de la alerta y copia al abrir el caso) |
| `ContextoImportacion::activo()` | Los eventos de dominio, **al construirse** (rellenan su `importado`). Los *listeners* de M01, M12 y M15 miran `$evento->importado` |
| `EstadoEnlace::actual()` | F2, F3 |

| Usa | De |
|---|---|
| `ConsultaMision::activa()` (perfil de enlace) | M02 |
| `ParametrosMision` (`retardo_marte_min`, `sync_cupo_*`, `sync_max_bytes_peticion`, `conjuncion_dias`) | M16 |
| `ImportacionLotes` | M03 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Ver el estado del enlace | `ver-enlace` (`cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin`) |
| Enviar recomendaciones (Tierra) | `enviar-recomendaciones` (`medico_vuelo`) |
| Leer recomendaciones (nave) | `ver-alertas` (`cmo`); el tripulante destinatario las ve en su inicio de F1 |
| Crear o cerrar ventanas sin enlace | `admin`, `ingeniero_biomedico` (comandos de consola) |

## 6. Archivos

```
app/Modules/Sincronizacion/
├── SincronizacionServiceProvider.php
├── routes.php
├── Contratos/{Importador,ColaSincronizacion,ContextoImportacion,EstadoEnlace,ConsultaRecomendaciones}.php
├── Domain/
│   ├── Enums/{TipoPaquete,EstadoPaquete,PerfilEnlace}.php
│   └── Reglas/{PoliticaPrioridad,CalculadoraRetardo,EstimadorEntrega}.php
├── Application/{CicloEnlace,RecibirPaquetes,ProcesarRecibidos,EntregarSalientes,EmpaquetarDetalle,EnviarRecomendacion,GenerarResumenHorario}.php
├── Console/{CicloCommand,ProcesarRecibidosCommand,EmpaquetarDetalleCommand,VentanaSinEnlaceCommand,CerrarVentanaCommand}.php
├── Http/
│   ├── Controllers/Web/{EnlaceController,RecomendacionesController,MisionTierraController}.php
│   ├── Controllers/Api/{PaquetesController,SalientesController,AcuseController}.php
│   └── Requests/{PaquetesRequest,RecomendacionRequest}.php
├── Infrastructure/
│   ├── Models/{PaqueteSync,PaqueteRecibido,DetalleEmpaquetado,VentanaEnlace,Recomendacion}.php
│   ├── ClienteNodoRemoto.php
│   └── Listeners/{AlCrearAlerta,AlIngerirLote,AlRegistrarDatos,AlModificarUmbral}.php
├── Database/Migrations/{create_paquetes_sync,create_paquetes_recibidos,create_ventanas_enlace,create_recomendaciones}.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Con una ventana activa, un paquete `pendiente` no sale; uno `enviado` sí se entrega al cumplirse su hora.
- [ ] Entregar dos veces el mismo `id` a la Tierra lo procesa una sola vez.
- [ ] Un dato importado en la Tierra no genera un paquete de vuelta ni un hallazgo.
- [ ] Con 120 paquetes de prioridad 3 y 1 de prioridad 1, el de prioridad 1 sale en el primer ciclo.
- [ ] Con el perfil `marte` (1 MB por ciclo), un ciclo no envía más de 1 MB salvo que el primer paquete sea mayor; ninguna petición pasa de 4 MB.
- [ ] Sesenta lotes en vivo de un minuto del mismo dispositivo forman **un** paquete `detalle`.
- [ ] Si la Tierra responde `500`, el paquete sigue `enviado` y se reintenta en el siguiente ciclo.
- [ ] Una escala psicológica y un cuestionario de mareo llegan a la Tierra en un paquete `registros`.
- [ ] La cola de la PWA reintenta tras un `5xx` y mueve a `rechazados` tras un `422`.
