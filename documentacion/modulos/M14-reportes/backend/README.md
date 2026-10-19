# M14 · Reportes · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

M14 no expone API; las descargas son rutas web con permiso.

## 2. Eventos

| Publica | Consume |
|---|---|
| — | — ("desactualizado" se calcula al consultar: hay una alerta de ese día actualizada después de generar el reporte) |

## 3. Reglas de negocio

### 3.1 Reporte diario (HU-14.01)

- Se genera **automáticamente a las 06:00 UTC** (`reporte_diario_hora_utc` de M16) para el día anterior, **solo en el nodo nave** (programador de tareas), y a demanda en cualquier nodo con los datos de ese nodo. La Tierra no lo genera automáticamente: recibe el de la nave, que tiene todos los datos aunque el enlace haya estado cortado.
- **Dos versiones, según quién lo lee** (confidencialidad de [M05 §3.3](../../M05-psicologia/backend/README.md#33-confidencialidad)):
  - `medico`: completo. Es el que se genera automáticamente y viaja a la Tierra;
  - `cmo`: el mismo reporte **sin** las escalas psicológicas ni las métricas cognitivas; de la sección cognitiva solo muestra las alertas `COG_DETERIORO`. El CMO es un compañero de tripulación y no debe ver el estado emocional de los demás.
- Se genera con `barryvdh/laravel-dompdf` desde una plantilla Blade de impresión (la única Blade además de la raíz de Inertia).
- Contenido, en este orden:

| Sección | Fuente |
|---|---|
| Semáforo por sistema al cierre del día | M13 `EstadoSemaforo::alInstante(tripulanteId, 23:59:59 UTC)` (el estado actual no sirve para un día pasado) |
| Alertas del día y su estado; hallazgos `info` del día | M13 `ConsultaAlertas`, M12 `ConsultaHallazgos` |
| Signos vitales: mínimo, máximo y media del día, frente a la línea base | M04 `ConsultaAgregados`, M12 `ConsultaLineaBase` |
| Sueño de la noche | M06 |
| Tests cognitivos y escalas (solo en la versión `medico`; en la `cmo`, solo las alertas cognitivas) | M05 |
| Síntomas | M11 |
| Exámenes cargados | M07 |
| Ejercicio y dosis | M10, M09 |

- El PDF automático (versión `medico`) se encola hacia la Tierra con **prioridad 2** (M15). Los generados a demanda no se encolan.

### 3.2 Reporte de misión (HU-14.03)

- Se genera **a demanda**, por tripulante, en el nodo donde se pide, con los datos de ese nodo. Cubre desde `fecha_inicio` de la misión hasta hoy (o hasta `fecha_fin` si ya pasó). No se encola: la Tierra ya recibe los datos y puede generar el suyo.
- Plantilla Blade `reporte-mision.blade.php`; los datos van en **tablas por semana de misión** (dompdf no ejecuta gráficas de JavaScript).
- Contenido, en este orden:

| Sección | Fuente |
|---|---|
| Misión: nombre, perfil de enlace, fechas y día de misión | M02 `ConsultaMision` |
| Alertas del periodo por sistema, nivel y estado; motivos de descarte | M13 `ConsultaAlertas` |
| Signos vitales en reposo: media semanal frente a la línea base | M04 `ConsultaAgregados` (`contexto = reposo`), M12 `ConsultaLineaBase` |
| Pendientes de 30 días (`tendencia_dias_larga`; FC en reposo, masa, sueño) | M12 `ConsultaTendencias` |
| Sueño: horas y eficiencia medias por semana | M06 |
| Cognición: media semanal de cada métrica frente a su línea base; escalas (solo en la versión `medico`) | M05 |
| Exámenes: todas las tomas por prueba con su interpretación | M07 |
| Radiación: dosis acumulada y proyección frente al límite de carrera | M09 |
| Físico: minutos de ejercicio por semana, masa, fuerza e ingesta media | M10 |
| Síntomas: número por tipo e intensidad máxima | M11 |

### 3.3 Exportación anonimizada (HU-14.02)

| Regla | Detalle |
|---|---|
| Seudónimo | `P` + los 8 primeros caracteres hexadecimales de HMAC-SHA256(`tripulante_id`, `SEUDONIMO_KEY`); estable entre exportaciones. Se usa una clave propia del `.env` y no `APP_KEY`: si se rotara `APP_KEY` (por ejemplo, al redesplegar), todos los seudónimos cambiarían y las exportaciones viejas y nuevas no se podrían cruzar |
| Se excluyen | Nombre, usuario, fecha de nacimiento, notas de texto libre |
| Se transforman | Edad → grupo de 10 años (`30-39`); fecha y hora → **día de misión** y hora UTC |
| Se conservan | Código de la variable, valor, unidad y contexto (sexo y grupo de edad solo si se piden; ver "Límite") |
| Límite | Con una tripulación de 4 personas, el sexo y el grupo de edad **casi identifican** a cada tripulante (por ejemplo, la única mujer de 40–49). La seudonimización reduce el riesgo, pero no hace anónimos los datos: esas dos columnas van **desactivadas por defecto** y la página lo advierte. En una misión real haría falta una revisión de privacidad antes de compartir |
| Generación | **En cola** (trabajo `ExportarAnonimizado`), leyendo por bloques, con **como máximo 7 días** por exportación: 30 días (~1 millón de lecturas) no caben en una petición de cPanel |
| Artefactos | Excluidos |
| Columnas | `seudonimo,dia_mision,hora_utc,codigo,valor,unidad,contexto` y, solo con `incluir_sexo_edad` (desmarcado por defecto), `sexo,grupo_edad` |
| Auditoría | Cada exportación registra `exportar` en M01 |

## 4. Contratos

M14 no ofrece contratos. Usa:

| Contrato | De |
|---|---|
| `EstadoSemaforo`, `ConsultaAlertas` | M13 |
| `ConsultaHallazgos`, `ConsultaLineaBase` | M12 |
| `ConsultaAgregados`, `ConsultaVitales` | M04 |
| `ConsultaRegistrosSueno`, `ConsultaSesiones`, `ConsultaEscalas`, `ConsultaSintomas`, `ConsultaResultados`, `ConsultaEjercicio`, `ConsultaNutricion`, `ConsultaFuerza`, `ConsultaDosis` | M06, M05, M11, M07, M10, M09 |
| `ConsultaMision` | M02 |
| `ConsultaTendencias` | M12 |
| `ConsultaObservaciones` | M03 (exportación) |
| `ColaSincronizacion::encolar()`, `Importador` | M15 (reporte diario hacia la Tierra) |
| `ParametrosMision` (`reporte_diario_hora_utc`, `tendencia_dias_larga`, `sueno_programado_h`) | M16 |
| `RegistroAuditoria` | M01 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Ver y generar reportes diarios y de misión | `generar-reportes` (`cmo`: versión `cmo`; `medico_vuelo`: versión `medico`) |
| Exportar datos anonimizados (solo en la Tierra) | `exportar-anonimizados` (`investigador`) |

## 6. Archivos

```
app/Modules/Reportes/
├── ReportesServiceProvider.php
├── routes.php
├── Domain/Reglas/{Seudonimizador,GrupoEdad}.php
├── Application/{GenerarReporteDiario,GenerarReporteMision,ExportarAnonimizado}.php
├── Http/
│   ├── Controllers/Web/{ReportesController,ExportacionesController,DescargaController}.php
│   └── Requests/{ReporteRequest,ExportacionRequest}.php
├── Jobs/ExportarAnonimizado.php
├── Infrastructure/
│   ├── Models/ReporteGenerado.php
│   └── Importadores/ReporteImportador.php   # Importador de M15 (PDF diario en la Tierra)
├── Resources/views/{reporte-diario,reporte-mision}.blade.php
├── Database/Migrations/create_reportes_generados.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] El mismo tripulante tiene el mismo seudónimo en dos exportaciones.
- [ ] El CSV no contiene ningún nombre ni fecha absoluta.
- [ ] Un usuario sin rol `investigador` recibe `403` al exportar.
- [ ] El reporte de un día sin datos se genera con la indicación "Sin datos".
- [ ] El reporte de misión del día 30 tiene 5 filas semanales (semanas 1 a 5) en cada tabla.
- [ ] Generar dos veces el reporte de misión el mismo día reemplaza el archivo y conserva una sola fila.
- [ ] La versión `cmo` del reporte diario no contiene escalas, cohesión ni métricas cognitivas; la versión `medico`, sí.
- [ ] En el nodo Tierra, el programador de tareas no genera reportes diarios; el que llega de la nave se guarda con `nodo_origen = nave`.
- [ ] Cambiar `APP_KEY` no cambia los seudónimos.
- [ ] Una exportación de 8 días se rechaza; sin `incluir_sexo_edad`, el CSV no tiene esas columnas.
- [ ] El semáforo del reporte de un día pasado refleja las alertas activas al final de ese día.

Detalle de cada historia en las [historias de backend](HU/README.md).
