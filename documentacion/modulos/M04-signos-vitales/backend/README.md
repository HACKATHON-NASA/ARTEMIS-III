# M04 · Signos vitales · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

| Método | Ruta | Autenticación | Descripción |
|---|---|---|---|
| `POST` | `/api/v1/signos-vitales/ecg` | Solo token de dispositivo `lecturas:escribir` (`TokenDeTipo:dispositivo`, [M01](../../M01-identidad/backend/README.md#33-tokens)) | Ventana de ECG: `{ id, dispositivo_id, tripulante_id, inicio, frecuencia_muestreo_hz, contexto, muestras[], picos_r[], qtc_ms }`; `dispositivo_id` y `tripulante_id` son **códigos**, con la misma autorización que un lote de M03 (el token del simulador puede enviar en nombre de cualquier wearable); idempotente por `id` |
| `POST` | `/api/v1/signos-vitales/ortostatica` | Solo token PWA `registros:escribir` de un usuario `cmo` | Prueba ortostática de **otro** tripulante (o del propio CMO): el `tripulante_id` (código) va en el cuerpo y no se toma del usuario; idempotente por `id` |

El endpoint de ECG se construye en el Sprint 4 (HU-04.04). Hasta entonces el simulador recibe `404` y omite las ventanas con un aviso. Validaciones, fórmulas y textos de cada historia: [historias de backend](HU/README.md).

`picos_r` son los índices de las muestras donde hay un pico R. Respuestas: `201`, `200` (ya existía), `422`, `401`/`403`.

## 2. Eventos

| Publica | Consume |
|---|---|
| `ObservacionRegistrada` por cada prueba ortostática (`AH-ORTO-CAIDA-PAS`, `AH-ORTO-CAIDA-PAD`) y por cada ventana de ECG (`AH-ECG-PAUSA-MAX`, `AH-ECG-CV-RR`, `AH-QTC`); su `observacion_id` es el `id` de la prueba o de la ventana | `LoteIngerido` (M03) → recalcula los agregados de los códigos de M04 |

## 3. Reglas de negocio

1. **Agregados** de los 8 códigos de M04 (incluida la actividad): excluyen los artefactos; `p50` es la mediana (calculada en PHP). La hora se recalcula con cada lote; el **día**, con un trabajo único por tripulante y día (`ShouldBeUniqueUntilProcessing`, 120 s de retraso), para que los ~24 lotes del relleno de un día no recalculen 24 veces el día entero. Cada periodo se calcula **dos veces**: con todas las lecturas (`contexto = todos`, para las gráficas) y solo con las de `reposo` (`contexto = reposo`). Las tendencias de M12 y los reportes de M14 que hablan de "FC en reposo" usan la segunda; si se mezclaran el ejercicio y el sueño, la media diaria no diría nada del reposo.
2. **Prueba ortostática:** compara una toma **basal** (acostado) con una toma **con estímulo**, y es `positiva` si la caída sistólica supera el umbral de `PA_ORTO_SIS` o la diastólica el de `PA_ORTO_DIA` (M16; hoy **20** y **10 mmHg**, [04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)). Los umbrales se leen de M16 y se guardan con la prueba, para que el resultado coincida con el aviso de M12 aunque el médico de vuelo los cambie. En microgravedad no existe "ponerse de pie", así que la prueba tiene una `modalidad`:
   - `de_pie`: en gravedad, antes del vuelo o después del aterrizaje (la prueba clásica);
   - `lbnp`: en vuelo, con presión negativa en la parte inferior del cuerpo (LBNP), que desplaza la sangre hacia las piernas como lo haría la gravedad. Es la que permite "evaluar el riesgo antes de la reentrada" (HU-04.03).

   La modalidad se guarda con la prueba y se muestra junto al resultado; el criterio de 20/10 mmHg (filas `PA_ORTO_SIS` y `PA_ORTO_DIA` de M16) es el mismo (parámetro a validar por médicos para la modalidad `lbnp`).
3. **ECG:** RR = diferencia entre picos R consecutivos, en ms. Si `n_rr < 10`, la ventana se guarda pero no publica métricas de ritmo (sí `AH-QTC`, que lo envía el dispositivo); `AH-ECG-CV-RR` solo se publica con `n_rr ≥ 30`. La señal y los picos R se guardan juntos en un archivo JSON del disco privado (`ecg/<tripulante_id>/<id>.json`).
4. **Umbrales** (los evalúa M12 con la configuración de M16): FC, SpO₂, FR, temperatura y PA según contexto ([04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)); pausa > 3 s y QTc > 500 ms → crítico; `cv_rr` > 0,15 → crítico (irregularidad; umbral `ECG_IRREGULAR` de M16, **parámetro de diseño** a validar por médicos).
5. **Semáforo (HU-04.02):** M04 lo muestra, pero lo calcula M13 a partir de las alertas abiertas del sistema `corazon` (ver [M13](../../M13-alertas/README.md)).
6. **Valor "actual":** es la última lectura válida (no artefacto) de los últimos **10 minutos** (`vital_reciente_min`; la PA, `pa_reciente_h`, 24 h); si no hay, la tarjeta muestra "Sin datos recientes" con la hora de la última lectura.
7. **Nodo Tierra:** los agregados llegan antes en el paquete `resumen` (prioridad 2) y después se pueden recalcular con los lotes del paquete `detalle` (prioridad 3). Un recálculo **solo reemplaza** un agregado si usa al menos tantas lecturas (`n`) como el que ya existe; así un lote suelto que llega primero no deja una media hecha con pocos datos.

## 4. Contratos

| Ofrece (`SignosVitales\Contratos\…`) | Uso |
|---|---|
| `ConsultaAgregados::serie(tripulanteId, codigo, periodo, desde, hasta, contexto = 'todos')` (`'reposo'` para tendencias y reportes en reposo) | M12 (tendencias), M14 (reportes), M15 (paquetes de resumen) |
| `ConsultaVitales::resumenActual(tripulanteId)` | F1, F2, M14 |

| Usa | De |
|---|---|
| `ConsultaObservaciones` | M03 |
| `ConsultaTripulantes` | M02 |
| `EstadoSemaforo::porTripulante()` | M13 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Ver los vitales propios | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver los vitales de toda la tripulación | `ver-datos-tripulacion` (`cmo`, `medico_vuelo`) |
| Registrar una prueba ortostática | `registrar-ortostatica` (`cmo`) |
| Ver una ventana de ECG | `ver-datos-tripulacion` (`cmo`, `medico_vuelo`) |

Los *Gates* son los de [M01 backend §5](../../M01-identidad/backend/README.md#5-permisos).

## 6. Archivos

```
app/Modules/SignosVitales/
├── SignosVitalesServiceProvider.php
├── routes.php
├── Contratos/{ConsultaAgregados,ConsultaVitales}.php
├── Domain/
│   ├── Enums/{Periodo,ContextoAgregado,ResultadoOrtostatica,ModalidadOrtostatica}.php
│   └── Reglas/{EvaluadorOrtostatica,MetricasRitmo}.php
├── Application/{RecalcularAgregados,RegistrarOrtostatica,RegistrarVentanaEcg}.php
├── Jobs/RecalcularAgregadoDia.php               # Único por tripulante y día
├── Http/
│   ├── Controllers/Web/{VitalesController,TripulacionController,OrtostaticaController,EcgController}.php
│   ├── Controllers/Api/{EcgController,OrtostaticaController}.php
│   ├── Requests/{VentanaEcgRequest,OrtostaticaRequest}.php
│   └── Resources/{ResumenVitalesResource,SerieResource}.php
├── Infrastructure/
│   ├── Models/{AgregadoVital,PruebaOrtostatica,VentanaEcg}.php
│   ├── Repositorios/*.php
│   └── Listeners/AlIngerirLote.php
├── Database/Migrations/{create_agregados_vitales,create_pruebas_ortostaticas,create_ventanas_ecg}.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Los agregados ignoran los artefactos y coinciden con un cálculo manual.
- [ ] Caídas de 21/5 → `positiva`; de 20/10 → `normal`; la modalidad (`de_pie` o `lbnp`) queda guardada.
- [ ] La media diaria de FC con `contexto = reposo` no incluye las lecturas de ejercicio ni de sueño.
- [ ] En el nodo Tierra, un recálculo con menos lecturas que el agregado recibido no lo reemplaza.
- [ ] Un token de `astroskin-T2` no puede enviar una ventana de ECG de T3 (`403`).
- [ ] Una ventana con RR = [800, 800, 3.200, 800…] da `pausa_max_s = 3,2`.
- [ ] Con los datos de E0, ninguna métrica de ritmo supera los umbrales.
