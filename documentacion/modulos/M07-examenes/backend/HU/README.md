# M07 · Exámenes médicos · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** de cada historia de M07. Lo hace **Julieth**. Las tablas están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **Reparto entre historias.** HU-07.01 calcula y guarda la **interpretación** (`bajo`, `normal`, `alto`, `critico`), porque la columna es obligatoria y la gráfica de HU-07.03 (mismo sprint) la usa. HU-07.02 añade el *delta check*, la tendencia y la regla de M12 que convierte todo esto en hallazgos (la ficha anterior no asignaba esa regla a ninguna historia).
- **El catálogo manda** (M16, `CatalogoPruebas::porCodigo()`): unidad, rangos por sexo, rango crítico, `delta_umbral`, `direccion_desfavorable` y `alerta_fuera_rango`. Ningún valor clínico se escribe en el código.
- **Sexo del tripulante** para los rangos: `ConsultaTripulantes::porId()` de M02.
- **Escritura por API solo con token** (`TokenDeTipo:pwa,dispositivo`): PWA de un `astronauta` o `cmo` (el tripulante es el del usuario; `origen = formulario`), o dispositivo de tipo `simulador` (`tripulante_id` obligatorio; `origen = simulador`). Otro dispositivo → `403`. El CSV y el PDF usan rutas **web** con sesión.
- **Valores configurables por misión** ([M16 §3.6](../../../M16-configuracion/backend/README.md#36-configuración-por-misión)): `tendencia_min_pct` (7,5), `examen_aviso_dias` (7) y `adjunto_pdf_max_kb` (2048). Se leen con `ParametrosMision::get()`; los números de estas historias son el valor por defecto.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).


## Historias

| Historia | Archivo |
|---|---|
| HU-07.01 · Cargar los resultados de mis exámenes | [HU-07.01-cargar-los-resultados-de-mis-examenes.md](HU-07.01-cargar-los-resultados-de-mis-examenes.md) |
| HU-07.03 · Evolución de cada prueba en una gráfica con su rango | [HU-07.03-evolucion-de-cada-prueba-en-una-grafica-con-su-rango.md](HU-07.03-evolucion-de-cada-prueba-en-una-grafica-con-su-rango.md) |
| HU-07.04 · Recordatorios de exámenes programados | [HU-07.04-recordatorios-de-examenes-programados.md](HU-07.04-recordatorios-de-examenes-programados.md) |
| HU-07.05 · Adjuntar el PDF del laboratorio | [HU-07.05-adjuntar-el-pdf-del-laboratorio.md](HU-07.05-adjuntar-el-pdf-del-laboratorio.md) |
| HU-07.02 · Marcar valores fuera de rango | [HU-07.02-marcar-valores-fuera-de-rango.md](HU-07.02-marcar-valores-fuera-de-rango.md) |
