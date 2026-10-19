# M05 · Psicología y cognición · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

Autenticación (solo token, nunca sesión; [M01 §3.3](../../M01-identidad/backend/README.md#33-tokens)):

- token PWA `registros:escribir` (también desde la cola offline): el `tripulante_id` se toma del usuario y se ignora el del cuerpo;
- token de un dispositivo **de tipo `simulador`** (`lecturas:escribir`; el token de cualquier otro dispositivo recibe `403`): el cuerpo **debe** traer `tripulante_id` (código `T1`…). Sin esto, el simulador no podría cargar las sesiones del histórico (línea base cognitiva) ni las de E4.

Idempotentes por `id`.

| Método | Ruta | Cuerpo | Descripción |
|---|---|---|---|
| `POST` | `/api/v1/psicologia/sesiones` | `{ id, tripulante_id?, tipo, inicio, duracion_s, completa, items: [{ orden, estimulo, respuesta, es_correcta, tiempo_ms, es_falsa_salida, sin_respuesta, presentado_en }] }` | Guarda la sesión y calcula las métricas |
| `POST` | `/api/v1/psicologia/escalas` | `{ tripulante_id?, respuestas: [{ id, escala, valor, respondida_en }] }` | Guarda una o varias escalas |

Respuestas: `201` (con las métricas calculadas), `200` (ya existía), `422`, `401`/`403`.

## 2. Eventos

| Publica | Consume |
|---|---|
| `TestCognitivoCompletado` (solo sesiones `completa = true`) | — |

## 3. Reglas de negocio

### 3.1 Tests

1. **PVT:** dura 180 s. El estímulo aparece tras un intervalo aleatorio de **2 a 10 s** (parámetro de diseño `pvt_intervalo_min_s`/`max_s` en M16). Tocar antes del estímulo o con TR < 100 ms es **falsa salida**; TR > 500 ms es **lapso**, y también un estímulo **sin respuesta** en `pvt_espera_max_s` (30 s †). El servidor recalcula las falsas salidas por tiempo y, en aritmética y memoria, `es_correcta`.
2. **Aritmética:** 20 operaciones de suma o resta con dos números de dos cifras y resultado no negativo.
3. **Memoria (HU-05.07):** se muestra una secuencia de dígitos, uno por segundo (`memoria_ms_por_elemento` = 1000 †), y el tripulante la escribe. Empieza con 3 dígitos (`memoria_longitud_inicial` †); cada acierto suma un dígito; **dos errores seguidos en la misma longitud** terminan el test. `longitud_max` es la secuencia más larga repetida sin error. Las secuencias se generan en el cliente sin dígitos repetidos seguidos.
4. Una sesión incompleta se guarda con `completa = false` y **no cuenta** para la línea base ni para la detección. El servidor **comprueba** el valor de `completa` que envía el cliente: PVT con `duracion_s ≥ 180`, aritmética con 20 ítems respondidos, memoria terminada por la regla de parada. Si no se cumple, la guarda como incompleta.
5. **Orden de las sesiones:** el número de sesión (1.ª, 2.ª…) sale del orden de `inicio`, no del orden de llegada, porque las sesiones hechas sin conexión pueden llegar tarde. Se calcula al consultar (`ROW_NUMBER()` por tripulante y tipo, solo las completas). Una sesión que llega después de creada la línea base y con `inicio` anterior a ella se guarda, pero no cambia la línea base ya fijada.

### 3.2 Detección de deterioro (`ReglaDeterioroCognitivo`)

Se ejecuta en el motor de M12 al recibir `TestCognitivoCompletado` y sigue **exactamente** el pseudocódigo de [04 §5.5](../../../docs/propuesta/04-manejo-de-datos.md#55-pseudocódigo):

| Paso | Regla |
|---|---|
| Familiarización | Sesiones 1 y 2 de cada tipo: sin evaluación |
| Línea base | Media y σ de las sesiones 3 a 7 de cada tipo; se guarda en M12 con `fuente = ventana_inicial`; no hay alertas hasta la sesión 8 |
| Puntuación z | `z = (valor − μ) / max(σ, σ_min, sigma_min_rel × μ)`, orientada para que **z > 0 sea peor**. σ mínimas en [base de datos §1](../database/README.md#métricas-por-sesión); `sigma_min_rel` = 10 % en `AH-PVT-MEDIANA` y `AH-ARIT-TIEMPO` |
| 🚨 Crítico | **Solo en el PVT** (es el único test con lapsos): z ≥ 3 en **2 sesiones consecutivas** en la **misma métrica** y lapsos de la sesión actual > max(2 × μ_lapsos, 2) (`cog_z_fuerte`, `cog_racha_fuerte`, `cog_lapsos_factor`, `cog_lapsos_min`). En aritmética y memoria (o en el PVT sin el aumento de lapsos), esa misma racha produce un **aviso** |
| ⚠️ Aviso | z ≥ 2 en **3 sesiones consecutivas** en la **misma métrica** (`cog_z_aviso`, `cog_racha_aviso`), o z ≥ 3 en 2 consecutivas cuando no es crítico. La **pendiente de 7 días** (`cog_pendiente_dias`) de la métrica principal (`AH-PVT-MEDIANA`, `AH-ARIT-TIEMPO`) se calcula y se muestra en la explicación, pero **no dispara avisos** |
| ℹ️ Info | z ≥ 2 en una sesión |

La explicación incluye el **contexto** de otros módulos ([04 §5.4](../../../docs/propuesta/04-manejo-de-datos.md#54-contexto-que-acompaña-la-alerta)): sueño medio de las últimas 4 noches (M06), fatiga y estrés recientes (M05), promedio horario máximo de pCO₂ de las últimas 24 h (M08) y RMSSD reciente (M04); las ventanas son `contexto_sueno_noches`, `contexto_escalas_dias`, `contexto_co2_h` y `contexto_rmssd_h`. El hallazgo pertenece al sistema `mente`.

> **Por qué la pendiente no dispara avisos y hay una σ mínima relativa:** al simular las siete métricas y todas las condiciones ([`cognitivo_todas_las_reglas.py`](../../../herramientas/montecarlo/cognitivo_todas_las_reglas.py)), la pendiente como disparador daba un aviso falso en ~97 % de las misiones normales (se evalúa cada día en 2 métricas y 4 tripulantes), y las rachas con solo σ mínimas absolutas, en el 11,7 %. Con la regla corregida y E4 a +8 % por día: **0,8 %** de falsos en E0 y **95 %** de detección de E4. Detalle en [HU-05.04](HU/HU-05.04-detectar-un-deterioro-cognitivo-sostenido.md).

> **Por qué la σ mínima de la precisión es 7,5 puntos:** con 20 ítems, cada error vale 5 puntos, y una línea base de 5 sesiones casi idénticas tiene σ ≈ 0. Con una σ mínima de 2 puntos, un solo error de más ya daba z ≥ 2. Al simular 20.000 misiones normales (E0), el 22 % generaba un aviso falso. Con 7,5 puntos baja al 0,1 % y una caída real de 15 puntos se sigue detectando el 95 % de las veces.

### 3.3 Confidencialidad

El CMO es **un compañero de tripulación**: si viera la escala de cohesión o la evolución emocional de los demás, el cuestionario dejaría de ser confidencial (y él mismo puede ser parte del conflicto). Por eso, como en las conferencias psicológicas privadas de la NASA:

| Dato | Lo ven |
|---|---|
| Escalas (ánimo, estrés, fatiga, carga de trabajo, **cohesión**) y evolución psicológica detallada | El propio tripulante y el **médico de vuelo** |
| Alertas de deterioro cognitivo (`COG_DETERIORO`) con su explicación | El CMO (es un riesgo operativo) y el médico de vuelo |

La explicación de la alerta que ve el CMO incluye la fatiga y el sueño recientes (para actuar), pero no la cohesión, el estrés ni el historial de escalas: el estrés va en un campo `confidencial` de la explicación que solo muestran las pantallas con `ver-psicologia-detallada`.

## 4. Contratos

| Ofrece (`Psicologia\Contratos\…`) | Uso |
|---|---|
| `ConsultaSesiones::ultimas(tripulanteId, tipo, n)` | M12 (rachas), M14 |
| `ConsultaEscalas::promedio(tripulanteId, escala, desde, hasta)` | M12 (contexto de las alertas), M14 |
| `ReglaDeterioroCognitivo` | Registrada en el motor de M12 por el *ServiceProvider* |

| Usa | De |
|---|---|
| `RegistroLineaBase`, `ConsultaLineaBase`, `ReglaAnalitica` | M12 |
| `ConsultaRegistrosSueno` | M06 (contexto) |
| `ConsultaObservaciones` | M03 (RMSSD para el contexto) |
| `ConsultaAmbiente::promedioHorarioMaximo()` | M08 (pCO₂ para el contexto) |
| `ConsultaTripulantes::porCodigo()` | M02 (tripulante de las sesiones del simulador) |
| `ParametrosMision` | M16 (intervalo del PVT, σ mínimas) |

## 5. Permisos

| Acción | Roles |
|---|---|
| Hacer tests y responder escalas | `ver-datos-propios` (`astronauta`, `cmo`: los propios) |
| Ver los resultados propios | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver las escalas y la evolución psicológica detallada de la tripulación | `ver-psicologia-detallada` (`medico_vuelo`) |
| Ver las alertas de deterioro cognitivo | `ver-alertas` (`cmo`, `medico_vuelo`) |

## 6. Archivos

```
app/Modules/Psicologia/
├── PsicologiaServiceProvider.php
├── routes.php
├── Contratos/{ConsultaSesiones,ConsultaEscalas}.php
├── Domain/
│   ├── Enums/{TipoTest,Escala}.php
│   ├── Reglas/{CalculadoraMetricasPvt,CalculadoraMetricasAritmetica,CalculadoraMetricasMemoria,ReglaDeterioroCognitivo}.php
│   └── Eventos/TestCognitivoCompletado.php
├── Application/{RegistrarSesion,RegistrarEscalas,ListarPendientesDelDia}.php
├── Http/
│   ├── Controllers/Web/{TestsController,EscalasController,EvolucionController}.php
│   ├── Controllers/Api/{SesionController,EscalaController}.php
│   └── Requests/{SesionRequest,EscalasRequest}.php
├── Infrastructure/
│   ├── Models/{SesionTest,RespuestaItem,RespuestaEscala,ProgramacionTest}.php
│   └── Repositorios/*.php
├── Database/
│   ├── Migrations/{create_sesiones_test,create_respuestas_item,create_respuestas_escala,create_programacion_tests}.php
│   └── Seeders/ProgramacionDemoSeeder.php   # PVT, aritmética y memoria diarios a las 08:00 UTC
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Métricas del PVT con una secuencia conocida (incluye una falsa salida y dos lapsos).
- [ ] La sesión 7 crea la línea base; la sesión 8 es la primera evaluada.
- [ ] z ≥ 3 en la sesión actual pero solo z = 2,5 en la anterior **no** es crítico.
- [ ] z ≥ 3 en dos sesiones seguidas de aritmética da **aviso**, nunca crítico (no hay lapsos).
- [ ] Con una línea base de precisión 95, 95, 100, 95, 95 %, una sesión al 90 % da z < 2 (σ mínima de 7,5).
- [ ] **E0 (semilla 42, 30 días): ningún aviso cognitivo** en ninguna métrica.
- [ ] Una sesión hecha sin conexión que llega después de otra más reciente queda en su lugar según `inicio`.
- [ ] Un PVT enviado con `completa = true` pero `duracion_s = 120` se guarda como incompleto.
- [ ] El token del simulador sin `tripulante_id` recibe `422`; un CMO no puede ver la escala de cohesión de otro tripulante (`403`).
- [ ] Con σ = 0 en la línea base se usa la σ mínima (no hay división por cero).
- [ ] La precisión de aritmética que baja produce z positiva.
- [ ] Memoria: aciertos en 3, 4, 5 y 6, y dos errores en 7 → `longitud_max = 6`; un error en 7 seguido de un acierto continúa a 8.
- [ ] Una `longitud_max` 2 por debajo de la línea base en 3 sesiones seguidas → aviso.
- [ ] Una pendiente desfavorable y significativa sin rachas **no** genera aviso (solo aparece en la explicación).
- [ ] Con una línea base de mediana 281 ± 5 ms, la σ efectiva es 28,1 ms (10 % de μ).
- [ ] Un estímulo del PVT sin respuesta en 30 s cuenta como lapso y no entra en la mediana.
- [ ] z ≥ 2 en tres sesiones seguidas pero en **métricas distintas** (mediana, luego lapsos, luego desviación) **no** genera aviso.
- [ ] Reenviar la misma sesión no la duplica ni la cuenta dos veces.
