# M16 · Configuración · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

M16 no expone API pública. La configuración viaja a la nave por M15 (paquete `configuracion`).

## 2. Eventos

| Publica | Consume |
|---|---|
| `UmbralModificado` (cambio en la Tierra, ajuste temporal, confirmación o reversión) | — |

## 3. Reglas de negocio

### 3.1 Edición y ajustes temporales

1. **En la Tierra:** el médico de vuelo edita un umbral → nueva versión `origen = tierra` → paquete `configuracion` (prioridad 2) hacia la nave.
2. **En la nave**, los umbrales `tierra` son de **solo lectura**.
3. **Ajuste temporal (HU-16.03):** el CMO crea una fila `origen = ajuste_temporal`, `estado_revision = pendiente`, que **tiene prioridad** sobre la de la Tierra a bordo. Viaja a la Tierra (paquete `ajuste_umbral`, prioridad 2). Límites del ajuste:
   - solo se puede hacer **durante un apagón**: con una ventana sin enlace activa o con un retardo de 3 minutos o más (perfil `marte`). Con enlace inmediato, el CMO le pide el cambio al médico de vuelo (la página lo explica);
   - solo cambia **valores** (`valor`, `valor2`, persistencia). No puede desactivar una regla ni cambiar su operador, sus contextos o su nivel: así un ajuste rápido a bordo nunca apaga una alarma crítica.
4. **Revisión:** el médico de vuelo lo **confirma** (se convierte en la nueva versión `tierra`) o lo **revierte** (deja de estar vigente). La decisión vuelve a la nave como `configuracion`.
   - **Cambios cruzados:** si la Tierra editó la misma regla antes de recibir el ajuste, su versión nueva llega a la nave pero el ajuste **sigue teniendo prioridad** hasta que el médico de vuelo lo revise. Ningún cambio se pierde: la Tierra ve el ajuste pendiente junto con su propia versión y decide.
5. **Dónde se edita cada cosa:** umbrales, catálogos y parámetros se editan **en la Tierra** (médico de vuelo y admin) y viajan a la nave en el paquete `configuracion`; en la nave son de solo lectura, salvo el ajuste temporal del CMO. Las filas se identifican por `(mision_id, regla_codigo, version, origen)` (umbrales) o por su clave (catálogos y parámetros), así los dos nodos tienen las mismas.
6. Todo cambio exige `motivo` y se audita: los umbrales, M01 al recibir `UmbralModificado`; los catálogos y parámetros, M16 con `RegistroAuditoria`.
7. **Un umbral solo puede activarse con `valor` y `fuente`** (`422` en otro caso). Así nacen las reglas `AMB_*` de M08 (HU-08.03).

### 3.2 Umbrales sembrados

Valores de [04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales). Los marcados con † son **parámetros de diseño** a validar por médicos.

| Regla | Familia | Variable | Contextos | Operador | Valor(es) | Nivel | Persistencia | Sistema |
|---|---|---|---|---|---|---|---|---|
| `FC_REPOSO_CAMBIO` | `FC` | `8867-4` | reposo, sueño | `cambio_pct_mayor` | 20 | aviso | 5 min | corazon |
| `FC_REPOSO_LIMITES` | `FC` | `8867-4` | reposo, sueño | `fuera_de` | 40 – 120 | critico | 5 min | corazon |
| `FC_EJERCICIO_MAX` | `FC` | `8867-4` | ejercicio | `mayor` | 185 | critico | 2 min | corazon |
| `FC_RECUPERACION` | `FC` | `8867-4` | 10 min tras el ejercicio | `mayor` | 100 (en el minuto 10) | aviso | inmediato | corazon |
| `ECG_PAUSA` | `ECG` | `AH-ECG-PAUSA-MAX` | cualquiera | `mayor` | 3 s | critico | inmediato | corazon |
| `ECG_QTC` | `ECG` | `AH-QTC` | cualquiera | `mayor` | 500 ms | critico | inmediato | corazon |
| `ECG_IRREGULAR` † | `ECG` | `AH-ECG-CV-RR` | cualquiera | `mayor` | 0,15 | critico | inmediato | corazon |
| `PA_SIS_LIMITES` | `PA` | `8480-6` | reposo | `fuera_de` | 90 – 140 | aviso | 2 consecutivos | corazon |
| `PA_DIA_LIMITES` | `PA` | `8462-4` | reposo | `fuera_de` | 60 – 90 | aviso | 2 consecutivos | corazon |
| `PA_ORTO_SIS` | `PA_ORTO` | `AH-ORTO-CAIDA-PAS` | — | `mayor` | 20 | aviso | inmediato | corazon |
| `PA_ORTO_DIA` | `PA_ORTO` | `AH-ORTO-CAIDA-PAD` | — | `mayor` | 10 | aviso | inmediato | corazon |
| `SPO2_AVISO` | `SPO2` | `59408-5` | cualquiera | `menor` | 92 | aviso | 3 min | cuerpo |
| `SPO2_CRITICO` | `SPO2` | `59408-5` | cualquiera | `menor` | 90 | critico | 3 min | cuerpo |
| `FR_REPOSO` | `FR` | `9279-1` | reposo, sueño | `fuera_de` | 8 – 25 | aviso | 5 min | cuerpo |
| `TEMP_REPOSO_ALTA` | `TEMP` | `8310-5` | reposo, sueño | `mayor` | 38 | aviso | 10 min | cuerpo |
| `TEMP_REPOSO_BAJA` | `TEMP` | `8310-5` | reposo, sueño | `menor` | 35 | critico | 10 min | cuerpo |
| `TEMP_EJERCICIO` | `TEMP` | `8310-5` | ejercicio | `mayor` | 39 | aviso | 10 min | cuerpo |
| `SUENO_MAL` | `SUENO` | `AH-SUENO-DURACION` y `AH-SUENO-EFICIENCIA` | — | `menor` (cualquiera de las dos) | 6 h · 85 % | aviso | 3 consecutivos | sueno |
| `MASA_PERDIDA` | `MASA` | `29463-7` | — | `perdida_pct_mayor` | 5 | aviso | `media_7d` | cuerpo |
| `GLUCOSA` | `GLUCOSA` | `AH-GLUCOSA` | cualquiera | `fuera_de` | 70 – 180 | aviso | 2 consecutivos | cuerpo |
| `DOSIS_PROYECCION` | `DOSIS` | proyección (M09) | — | `mayor` | 80 % del límite de carrera | aviso | — | radiacion |
| `CO2_CEFALEA` | `CO2` | `AH-PCO2` | — | `mayor` | 3,0 mmHg (promedio horario) + cefalea | aviso | 60 min | ambiente |
| `CO2_ALTO` | `CO2_MODULO` | `AH-PCO2` | — | `mayor` | 3,0 mmHg (por módulo de la nave; hallazgo global) | aviso | 60 min | ambiente |
| `AMB_VOC` (**inactiva**) | `AMB_VOC` | `AH-VOC` | — | `mayor` | Sin valor (límite NASA a cargar con su fuente) | aviso | 12 consecutivos (60 min a una lectura cada 5 min) | ambiente |
| `AMB_PM` (**inactiva**) | `AMB_PM` | `AH-PM` | — | `mayor` | Sin valor (límite NASA a cargar con su fuente) | aviso | 12 consecutivos | ambiente |
| `AMB_RUIDO` (**inactiva**) | `AMB_RUIDO` | `AH-RUIDO` | — | `mayor` | Sin valor (límite NASA a cargar con su fuente) | aviso | 12 consecutivos | ambiente |

`FC_RECUPERACION`, `SUENO_MAL`, `DOSIS_PROYECCION` y `CO2_CEFALEA` toman aquí sus **valores**, pero su lógica la ejecutan reglas especializadas (M12, M06, M09 y M08); por eso se siembran con `evaluador = especializada` y el motor genérico de M12 no las evalúa. Todas las filas se siembran con su `fuente` (04 §8, salvo `GLUCOSA`: [Fase 1 §3](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#3-qué-monitorear-parámetros-rangos-y-alertas)); las `AMB_*` se siembran sin valor ni fuente. `COG_DETERIORO`, `DOSIS_SPE` y `EXAMEN_*` no tienen fila de umbral: usan los parámetros de la sección 3.5 y el catálogo de pruebas. La familia de las reglas de examen es `EXAMEN_<código de la prueba>`.

### 3.3 Catálogo de variables sembrado

Los **códigos de sensor** están en el [índice de módulos §8](../../README.md#8-catálogo-de-códigos-de-variables). Los **códigos derivados** son:

| Código | Variable | Unidad | Módulo | Rango posible |
|---|---|---|:---:|---|
| `AH-ECG-PAUSA-MAX` | Pausa RR máxima | `s` | M04 | 0,2 – 10 |
| `AH-ECG-CV-RR` | Coeficiente de variación RR | `1` | M04 | 0 – 1 |
| `AH-QTC` | QT corregido | `ms` | M04 | 250 – 700 |
| `AH-ORTO-CAIDA-PAS`, `AH-ORTO-CAIDA-PAD` | Caída ortostática | `mm[Hg]` | M04 | −50 – 100 |
| `AH-PVT-MEDIANA`, `AH-PVT-DE` | Mediana y desviación del TR | `ms` | M05 | 100 – 2000 |
| `AH-PVT-LAPSOS`, `AH-PVT-FALSAS` | Lapsos y falsas salidas | `{#}` | M05 | 0 – 200 |
| `AH-ARIT-TIEMPO` | Tiempo medio por ítem | `s` | M05 | 0,3 – 60 |
| `AH-ARIT-PRECISION` | Precisión | `%` | M05 | 0 – 100 |
| `AH-MEM-SPAN` | Longitud máxima recordada | `{#}` | M05 | 0 – 20 |
| `AH-SUENO-DURACION` | Duración del sueño | `h` | M06 | 0 – 16 |
| `AH-SUENO-EFICIENCIA` | Eficiencia del sueño | `%` | M06 | 0 – 100 |
| `AH-EJERCICIO-MIN` | Minutos de ejercicio | `min` | M10 | 1 – 240 |
| `AH-INGESTA-KCAL` | Energía de una comida | `kcal` | M10 | 0 – 3000 |
| `AH-INGESTA-LIQUIDO` | Volumen de un líquido | `mL` | M10 | 1 – 3000 |
| `AH-FUERZA-PRENSION` | Fuerza de prensión | `kg` | M10 | 1 – 100 |
| `AH-SINTOMA-<TIPO>` | Intensidad del síntoma (**patrón: no se siembra** fila por síntoma; algunos códigos superan los 20 caracteres de `codigo`) | `{score}` | M11 | 0 – 10 |

### 3.4 Catálogo de pruebas sembrado

Rangos de [04 §3.3](../../../docs/propuesta/04-manejo-de-datos.md#exámenes-de-laboratorio-m07). La interpretación es `alto` si el valor es **mayor** que el máximo y `bajo` si es **menor** que el mínimo.

| Código | Prueba | Unidad | Referencia H | Referencia M | Crítico | Delta | Desfavorable | Alerta fuera de rango | Sistema |
|---|---|---|---|---|---|---|---|:---:|---|
| `718-7` | Hemoglobina | `g/dL` | 13,5 – 17,5 | 12,0 – 15,5 | < 7,0 † | 1,5 | baja | — | cuerpo |
| `4544-3` | Hematocrito | `%` | 41 – 53 | 36 – 46 | — | — | baja | — | cuerpo |
| `AH-RETIC` | Reticulocitos | `%` | 0,5 – 2,5 | 0,5 – 2,5 | — | — | ambas | — | cuerpo |
| `AH-BILI` | Bilirrubina total | `mg/dL` | 0,1 – 1,2 | 0,1 – 1,2 | — | — | sube | — | cuerpo |
| `62292-8` | 25-OH vitamina D total | `ng/mL` | 30 – 100 | 30 – 100 | — | — | baja | — | cuerpo |
| `AH-B12` | Vitamina B12 | `pg/mL` | 200 – 900 | 200 – 900 | — | — | baja | — | cuerpo |
| `AH-CA` | Calcio sérico | `mg/dL` | 8,5 – 10,5 | 8,5 – 10,5 | — | — | ambas | — | cuerpo |
| `AH-CTX` | CTX (resorción ósea) | `ng/mL` | Sin rango (se sigue frente a la línea base) | — | — | — | sube | — | cuerpo |
| `AH-OCT-DTRT` | Δ TRT por OCT | `um` | máximo 19,9 (≥ 20 µm es `alto`) | igual | — | — | sube | ✅ | cuerpo |
| `AH-DOPPLER-IJV` | Grado de flujo yugular (1–4) | `{grado}` | 1 – 2 (3 = estancado, 4 = retrógrado) | igual | — | — | sube | ✅ | corazon |

Los códigos `AH-*` se sustituirán por su LOINC cuando se verifique.

### 3.5 Parámetros de misión sembrados

| Clave | Valor | Fuente |
|---|---|---|
| `limite_carrera_msv` | 600 | NASA-STD-3001 ([Fase 1 §2.5](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#25-radiación-espacial)) |
| `limite_evento_msv` | 250 | NASA (OCHMO). **A verificar:** en NASA-STD-3001 parece ser un límite de 30 días (250 mGy-Eq, órganos hematopoyéticos), no "por evento"; ver [M09 backend §3](../../M09-radiacion/backend/README.md#3-reglas-de-negocio) |
| `spe_factor` | 10 | Parámetro de diseño † |
| `retardo_marte_min` | 12 (rango 3 – 22) | Fase 1 §5.3; 12 min para la demo |
| `sync_cupo_bytes_leo`, `sync_cupo_bytes_luna`, `sync_cupo_bytes_marte` | 20 MB, 5 MB, 1 MB por ciclo | Parámetro de diseño † |
| `sync_cupo_paquetes_leo`, `sync_cupo_paquetes_luna`, `sync_cupo_paquetes_marte` | 2.000, 1.000, 200 por ciclo | Parámetro de diseño † |
| `sync_max_bytes_peticion` | 4 MB | Por debajo del `post_max_size` habitual de cPanel |
| `pvt_duracion_s` | 180 | [04 §5.1](../../../docs/propuesta/04-manejo-de-datos.md#51-captura) |
| `pvt_intervalo_min_s`, `pvt_intervalo_max_s` | 2, 10 | Parámetro de diseño † |
| `aritmetica_items` | 20 | 04 §5.1 |
| `cog_sesiones_familiarizacion`, `cog_sesiones_linea_base` | 2, 5 | [04 §5.2](../../../docs/propuesta/04-manejo-de-datos.md#52-línea-base) |
| `sigma_min` | `{ "AH-PVT-MEDIANA": 10, "AH-PVT-DE": 10, "AH-PVT-LAPSOS": 1, "AH-PVT-FALSAS": 1, "AH-ARIT-TIEMPO": 0.2, "AH-ARIT-PRECISION": 7.5, "AH-MEM-SPAN": 1 }` | 04 §5.2 (las de `AH-PVT-DE`, `AH-PVT-FALSAS` y `AH-MEM-SPAN` son parámetros de diseño †) |
| `memoria_longitud_inicial`, `memoria_ms_por_elemento` | 3, 1000 | Parámetro de diseño † |
| `memoria_longitud_tope` | 12 | Parámetro de diseño † (sin tope, el test podría no terminar) |
| `co2_cefalea_ventana_h` | 24 | Parámetro de diseño † (ventana de `CO2_CEFALEA`, M08) |
| `pvt_espera_max_s` | 30 | Parámetro de diseño † (estímulo sin respuesta = lapso) |
| `sigma_min_rel` | `{ "AH-PVT-MEDIANA": 0.10, "AH-ARIT-TIEMPO": 0.10 }` | Monte Carlo de todas las reglas ([`cognitivo_todas_las_reglas.py`](../../../herramientas/montecarlo/cognitivo_todas_las_reglas.py)) † |
| `adjunto_foto_max_kb`, `adjunto_pdf_max_kb` | 500, 2048 | Parámetro de diseño † |
| `ventana_linea_base_dias` | 7 | 04 §4.4 |
| `ewma_lambda` | 0,3 | Parámetro de diseño † |
| `recuperacion_fc_min` | 10 | 04 §8 |
| `tendencia_min_pct` | 7,5 | Parámetro de diseño †: con 5 %, el 17 % de las misiones normales simuladas daba un aviso falso de descenso de Hb (ver [M07 backend §3](../../M07-examenes/backend/README.md#3-reglas-de-negocio)) |
| `ejercicio_meta_semanal_min` | 840 | ~2 h diarias (resumen de enfermedades) |

**Parámetros añadidos para que nada clínico quede fijo en el código** (columna Fuente: fuente · categoría · quién lo usa; los anteriores son todos `clinico` salvo `sync_*`, `adjunto_*` y `retardo_marte_min`):

| Clave | Valor por defecto | Fuente |
|---|---|---|
| `sueno_programado_h` | 8,5 | Horario programado en la ISS ([Fase 1 §2.8](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#28-sueño-y-ritmos-circadianos)) · clínico · M06 (meta en F1 y F2), M14 |
| `sueno_despertar_utc` | "07:00" | Agenda de la misión (simulador §6.4) · clínico · M06 (hora de despertar que prellena el registro manual) |
| `sueno_regularidad_noches`, `sueno_regularidad_min_noches` | 7, 3 | Parámetro de diseño † · clínico · M06 |
| `cog_z_aviso`, `cog_racha_aviso` | 2, 3 | [04 §5.3](../../../docs/propuesta/04-manejo-de-datos.md#53-reglas-de-detección) · clínico · M05 |
| `cog_z_fuerte`, `cog_racha_fuerte` | 3, 2 | 04 §5.3 · clínico · M05 |
| `cog_lapsos_factor`, `cog_lapsos_min` | 2, 2 | 04 §5.3 (el mínimo de 2 lapsos es parámetro de diseño †) · clínico · M05 |
| `cog_pendiente_dias` | 7 | 04 §5.3 · clínico · M05 (solo explicación) |
| `contexto_escalas_dias`, `contexto_rmssd_h` | 3, 24 | 04 §5.4 ("fatiga reciente", "RMSSD reciente"; la duración es parámetro de diseño †) · clínico · M05 (explicación) |
| `reporte_diario_hora_utc` | "06:00" | Parámetro de diseño † · operativo · M14 (hora del reporte diario automático en la nave) |
| `examen_aviso_dias` | 7 | Parámetro de diseño † · clínico · M07 (recordatorio "próximo") |
| `test_atraso_min` | 60 | Parámetro de diseño † · clínico · M05 (estado "Atrasado" de los tests) |
| `contexto_sueno_noches`, `contexto_co2_h` | 4, 24 | [04 §5.4](../../../docs/propuesta/04-manejo-de-datos.md#54-contexto-que-acompaña-la-alerta) · clínico · M05 (explicación) |
| `co2_objetivo_7d_mmhg` | 2,5 | [Fase 1 §3.2](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#32-ambiente-y-radiación) (objetivo del promedio de 7 días) · clínico · M08 (línea de referencia) |
| `co2_cefalea_intensidad_min` | 1 | Parámetro de diseño † · clínico · M08 |
| `dosis_hueco_max_min` | 5 | Parámetro de diseño † · clínico · M09 |
| `dosis_proyeccion_dias` | 7 | Parámetro de diseño † (días de la tasa media) · clínico · M09 |
| `spe_ventana_h`, `spe_min_lecturas`, `spe_lecturas_consecutivas` | 24, 60, 3 | Parámetro de diseño † · clínico · M09 |
| `dosis_corto_plazo_dias` | 30 | NASA-STD-3001 (a verificar, como `limite_evento_msv`) · clínico · M09 |
| `mareo_dias` | 3 | Parámetro de diseño † · clínico · M11 |
| `linea_base_min_lecturas`, `linea_base_min_noches` | 10, 5 | Parámetro de diseño † · clínico · M12 (mínimo para crear una línea base) |
| `tendencia_min_puntos`, `tendencia_p_max` | 4, 0,05 | Parámetro de diseño † (0,05 es el nivel de significación habitual) · clínico · M12 |
| `tendencia_dias_corta`, `tendencia_dias_larga` | 7, 30 | [04 §4.4](../../../docs/propuesta/04-manejo-de-datos.md#44-métodos-de-análisis-explicables) · clínico · M12, M14 |
| `alerta_confirmacion_min` | 30 | Parámetro de diseño † · clínico · M13 |
| `falsos_positivos_dias` | 30 | Parámetro de diseño † · operativo · M13 |
| `vital_reciente_min`, `pa_reciente_h` | 10, 24 | Parámetro de diseño † · clínico · M04 (valor "actual" de una tarjeta; la PA se toma una vez al día) |
| `flecha_tendencia_pct` | 5 | Parámetro de diseño † · clínico · M04 (flecha de tendencia, solo visual) |
| `sensor_sin_datos_min` | 10 | Parámetro de diseño † · operativo · M03 (solo dispositivos de envío continuo) |
| `colocacion_artefactos_min`, `colocacion_ventana_min` | 3, 5 | Parámetro de diseño † · operativo · M03 (aviso de colocación) |
| `conjuncion_dias` | 14 | Conjunción solar de unas dos semanas ([Fase 1 §5.3](../../../docs/investigacion/02-monitorizacion-salud-fase1.md)) · operativo · M15 (comando de demo) |

### 3.6 Configuración por misión

Cada misión tiene **su propia configuración**: en una el sueño programado puede ser de 8 h y en otra de 9 h, o el umbral de SpO₂ puede ser distinto.

1. **Qué es por misión:** los umbrales (§3.2) y los parámetros (§3.5). Las dos tablas llevan `mision_id`. Los **catálogos** (variables, pruebas y síntomas, §3.3 y §3.4) son **comunes** a todas las misiones: son referencias de laboratorio y validaciones técnicas, no decisiones de una misión.
2. **Valores por defecto en un solo sitio:** `Domain/ValoresPorDefecto.php` contiene las tablas de §3.2 y §3.5 (con su fuente). Los usan el *seeder* y la inicialización; nadie más.
3. **Inicialización:** la primera vez que se pide la configuración de una misión que no tiene filas, `InicializarConfiguracion` las crea con los valores por defecto, con bloqueo (`Cache::lock("config:<mision>")`) y `id` UUID v5 de `umbral:<mision_id>:<regla>:1:tierra` y `parametro:<mision_id>:<clave>`. Como los valores y los `id` salen del código, la nave y la Tierra crean las mismas filas sin enviarse nada; M02 no tiene que llamar a M16.
4. **Qué misión se usa:** `ConsultaUmbrales` y `ParametrosMision` usan por defecto la **misión activa** (M02 `ConsultaMision::activa()`) y aceptan un `misionId` opcional para leer la de otra misión (reportes de M14 de misiones pasadas). La caché lleva la misión en la clave (`umbrales:<mision>:<codigo>`, `parametros:<mision>`).
5. **Dónde se edita:** en la Tierra, siempre la **misión activa**; las de misiones pasadas son de solo lectura. Los parámetros tienen `categoria`: `clinico` (los edita el médico de vuelo, `editar-umbrales`) u `operativo` (`sync_*`, `adjunto_*`, `retardo_marte_min`, `falsos_positivos_dias`, `conjuncion_dias`, `sensor_sin_datos_min`, `colocacion_*` y `reporte_diario_hora_utc`; los edita el admin, `editar-catalogos`).
6. **Parámetros que solo se cambian antes del día 1:** `ventana_linea_base_dias`, `cog_sesiones_familiarizacion` y `cog_sesiones_linea_base` (cambiarlos después invalidaría las líneas base ya calculadas): `422` "Este parámetro solo se puede cambiar antes de empezar la misión."
7. **Ningún valor clínico u operativo se escribe en el código** de los módulos: se lee de M16. Los números que aparecen en las fichas y las historias son los valores por defecto. Quedan fijos en el código solo los límites técnicos (tamaños de lote, reintentos, cachés, tolerancia de relojes).


## 4. Contratos

| Ofrece (`Configuracion\Contratos\…`) | Uso |
|---|---|
| `ConsultaUmbrales::vigentes(variableCodigo, evaluador = 'generico', ?misionId)` (aplica el ajuste temporal si existe) y `::porRegla(reglaCodigo, ?misionId)` | M12 y las reglas especializadas |
| `CatalogoVariables::porCodigo()` | M03, M12 |
| `CatalogoPruebas::porCodigo()`, `::activas()` | M07, M12 |
| `CatalogoSintomas::activos()` | M11 |
| `ParametrosMision::get(clave, ?misionId)` (por defecto, la misión activa; §3.6) | Casi todos los módulos clínicos, M14 y M15 |

| Usa | De |
|---|---|
| `RegistroAuditoria` | M01 (cambios de catálogos y parámetros; los cambios de umbrales los audita M01 al recibir `UmbralModificado`, para no duplicarlos) |
| `ColaSincronizacion`, `Importador`, `EstadoEnlace` (¿hay apagón?) | M15 |
| `ConsultaMision::activa()` | M02 |
| `ConsultaResultados::hayDePrueba(pruebaCodigo)` (unidad bloqueada) | M07 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Editar umbrales y revisar ajustes temporales (Tierra) | `editar-umbrales` (`medico_vuelo`) |
| Aplicar un ajuste temporal a bordo durante un apagón | `ajustar-umbral-temporal` (`cmo`) |
| Editar catálogos y parámetros operativos (Tierra) | `editar-catalogos` (`admin`) |
| Editar parámetros clínicos de la misión activa (Tierra) | `editar-umbrales` (`medico_vuelo`) |
| Ver la configuración | `ver-configuracion` (`cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin`) |

## 6. Archivos

```
app/Modules/Configuracion/
├── ConfiguracionServiceProvider.php
├── routes.php
├── Contratos/{ConsultaUmbrales,CatalogoVariables,CatalogoPruebas,CatalogoSintomas,ParametrosMision}.php
├── Domain/
│   ├── Enums/{Operador,UnidadPersistencia,OrigenUmbral,EstadoRevision,DireccionDesfavorable}.php
│   ├── ValoresPorDefecto.php
│   ├── Reglas/{ResolutorUmbralVigente,DescripcionUmbral}.php
│   └── Eventos/UmbralModificado.php
├── Application/{InicializarConfiguracion,EditarUmbral,AplicarAjusteTemporal,RevisarAjuste,EditarCatalogo,EditarParametro}.php
├── Http/
│   ├── Controllers/Web/{UmbralesController,AjustesController,CatalogosController,ParametrosController}.php
│   └── Requests/{EditarUmbralRequest,AjusteTemporalRequest,RevisarAjusteRequest,CatalogoPruebaRequest,CatalogoSintomaRequest,CatalogoVariableRequest,ParametroRequest}.php
├── Infrastructure/
│   ├── Models/{Umbral,VariableCatalogo,PruebaCatalogo,SintomaCatalogo,ParametroMision}.php
│   ├── Repositorios/*.php
│   └── Importadores/ConfiguracionImportador.php   # Importador de M15 (configuracion y ajuste_umbral)
├── Database/
│   ├── Migrations/{create_umbrales,create_catalogo_variables,create_catalogo_pruebas,create_catalogo_sintomas,create_parametros_mision}.php
│   └── Seeders/{UmbralesSeeder,CatalogoVariablesSeeder,CatalogoPruebasSeeder,CatalogoSintomasSeeder,ParametrosMisionSeeder}.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Los **valores** de los umbrales sembrados coinciden con los de [04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales): la prueba recorre las filas de la sección 3.2 y compara variable, contexto, valor y persistencia con una tabla esperada escrita a mano a partir de 04 §8 (no se puede comparar "tabla contra tabla": 04 §8 también lista reglas que viven en otros módulos, como la cognición o el SANS).
- [ ] Con enlace inmediato (`leo` sin ventana), el CMO no puede crear un ajuste temporal (`422` con el motivo); con una ventana activa, sí.
- [ ] Un ajuste temporal que intenta desactivar una regla o cambiar su nivel devuelve `422`.
- [ ] Si llega una versión nueva de la Tierra mientras hay un ajuste pendiente, el ajuste sigue vigente a bordo.
- [ ] Activar `AMB_VOC` sin `valor` o sin `fuente` devuelve `422`.
- [ ] Editar un umbral crea la versión `n + 1` y deja la anterior `vigente = false`.
- [ ] Con un ajuste temporal `pendiente`, `ConsultaUmbrales` devuelve el ajuste en la nave.
- [ ] Revertir un ajuste vuelve a aplicar la versión de la Tierra.
- [ ] Un cambio sin motivo es rechazado.
