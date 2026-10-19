# M12 · Analítica y línea base · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

M12 no expone API: trabaja con eventos y contratos.

## 2. Eventos

| Publica | Consume |
|---|---|
| `HallazgoDetectado` (solo `aviso` y `critico`; los `info` quedan en `hallazgos` para el reporte diario de M14), con `familia`, `sistema`, `modulo_nave_id` y `ocurrencia`, que M13 necesita para agrupar y para el semáforo | `LoteIngerido` (M03), `ObservacionRegistrada` (M04, M06, M10, M11), `TestCognitivoCompletado` (M05), `ResultadoExamenCargado` (M07), `UmbralModificado` (M16: invalida la caché de umbrales; **es el único evento que M12 procesa también durante una importación**, para que un umbral que llega de la Tierra se aplique de inmediato) |

## 3. Reglas de negocio

### 3.1 Política de línea base

| Variable | Fuente de la referencia | Contexto |
|---|---|---|
| Vitales (FC, SpO₂, FR, temperatura, PA, RMSSD) | `reposo`: `previa_vuelo` (M02) si existe; si no, días de misión 1 a 7. `sueno`: siempre los días de misión 1 a 7 | Una línea base **por contexto**: `reposo` y `sueno` |
| Sueño (duración, eficiencia) | Noches 1 a 7 | — |
| Masa corporal | `previa_vuelo` si existe; si no, media de la semana 1 | — |
| Métricas cognitivas | Sesiones 3 a 7 de cada tipo (lo hace `ReglaDeterioroCognitivo`) | — |

- **Por qué hay línea base de sueño:** la regla `FC_REPOSO_CAMBIO` se aplica en reposo **y** durante el sueño. Si la lectura de sueño se comparara con la línea base de reposo, la bajada normal de la FC al dormir (en personas reales, del orden del 10 al 20 %) se vería como un "cambio > 20 %" y la misión normal daría avisos falsos cada noche. Con el simulador actual no pasa (no modela la bajada nocturna), pero la regla debe ser correcta también con datos reales. `cambio_pct_mayor` usa siempre la línea base **del mismo contexto que la lectura**.
- **Cuándo se calcula:** la línea base de la ventana inicial se calcula automáticamente cuando llega la primera lectura del **día de misión 8** (o, en el sueño, la noche 8), con los datos de los días 1 a 7. En el relleno histórico pasa a mitad de la carga, porque los lotes llegan en orden de tiempo.
- Mientras no existe la línea base de una variable, **solo se evalúan las reglas absolutas** (p. ej., FC > 120); las relativas (cambio %) esperan.
- `desviacion` efectiva = max(`desviacion`, σ mínima de M16).
- **Recálculo:** solo lo autoriza el médico de vuelo desde F3, con motivo y ventana de fechas; la solicitud se guarda en `solicitudes_recalculo` (`pendiente`, `completada` o `rechazada`) en los dos nodos. Como los datos completos y las reglas están **en la nave**, la Tierra envía una **solicitud de recálculo** (paquete `configuracion`); la nave la ejecuta, crea la fila nueva con `fuente = recalculo_autorizado`, deja la anterior con `vigente = false`, lo audita y devuelve la línea base nueva a la Tierra.
- **Líneas base en la Tierra:** las que calcula la nave (ventana inicial, cognitivas y recálculos) viajan a la Tierra en el paquete `resumen` (prioridad 2), para que F3 pueda mostrar las tendencias frente a la línea base. Sin esto, el médico de vuelo solo vería las líneas base previas al vuelo.

### 3.2 Motor de reglas de umbral

Cada fila activa de `umbrales` (M16) con `evaluador = generico` tiene (las `especializada` solo guardan valores para las reglas especializadas): código de variable, familia, contextos, operador, valores, nivel y persistencia. Al llegar datos de un código, el motor evalúa sus reglas sobre la ventana `[desde − persistencia, hasta]`, **excluyendo artefactos**.

**Ventana deslizante:** un lote del relleno histórico trae una hora entera de lecturas. El motor evalúa la regla **en cada minuto** del lote (cada lectura es el final posible de una ventana de persistencia), no solo al final del lote. Si solo mirara el último minuto, una hipoxemia de 5 minutos en mitad de la hora pasaría sin alerta. Para no inundar M13, el motor genera **un hallazgo por regla, tripulante y episodio**: mientras la condición sigue cumpliéndose minuto a minuto, actualiza el hallazgo abierto (fin de la ventana y valor extremo) en lugar de crear uno nuevo cada minuto. Aun así, vuelve a publicar `HallazgoDetectado` (con el mismo `hallazgo_id`) **cada vez que se completa otra ventana de persistencia** (por ejemplo, cada 3 minutos en SpO₂): M13 lo cuenta como una ocurrencia más y así puede hacer la confirmación automática ("segundo hallazgo de la misma familia en 30 minutos", E8).

| Operador | Se cumple si… |
|---|---|
| `mayor` | valor > `valor` |
| `menor` | valor < `valor` |
| `fuera_de` | valor < `valor` **o** valor > `valor2` |
| `cambio_pct_mayor` | \|valor − μ\| / μ × 100 > `valor` (μ de la línea base vigente) |
| `perdida_pct_mayor` | (μ − valor) / μ × 100 > `valor` |

| Unidad de persistencia | Significado |
|---|---|
| `min` | En los últimos N minutos hay al menos ⌈0,8 × N⌉ lecturas válidas y **todas** cumplen (los artefactos no cuentan ni a favor ni en contra) |
| `consecutivos` | Los últimos N registros (tomas, noches, lecturas) cumplen |
| `media_7d` | La media de los últimos 7 días cumple |
| `inmediato` | Basta un valor |

**Lecturas ambientales:** si el lote tiene `ubicacion`, el motor evalúa cada módulo de la nave por separado; el hallazgo es global (`tripulante_id = null`) y la explicación nombra el módulo (reglas `AMB_*`, [M08](../../M08-ambiente/backend/README.md#3-reglas-de-negocio)). Las reglas con `activo = false` no se evalúan.

**Contexto:** una regla solo mira lecturas de sus contextos. Los **10 minutos posteriores a un bloque de ejercicio** (`recuperacion_fc_min`) no cuentan como `reposo`: los evalúa la regla de recuperación ([04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)).

### 3.3 Reglas especializadas

Implementan `Analitica\Contratos\ReglaAnalitica` y se registran en el *ServiceProvider* de su módulo.

| Código | Clase (módulo) | Se activa con | Sistema |
|---|---|---|---|
| `COG_DETERIORO` | `ReglaDeterioroCognitivo` ([M05](../../M05-psicologia/backend/README.md#32-detección-de-deterioro-regladeteriorocognitivo)) | `TestCognitivoCompletado` | `mente` |
| `CO2_CEFALEA` | `ReglaCo2Cefalea` ([M08](../../M08-ambiente/backend/README.md#reglaco2cefalea-hu-0802)) | `LoteIngerido` de `AH-PCO2`; síntoma `cefalea` | `ambiente` |
| `DOSIS_SPE` | `ReglaEventoSolar` ([M09](../../M09-radiacion/backend/README.md#3-reglas-de-negocio)) | `LoteIngerido` de `AH-DOSIS-TASA` | `radiacion` |
| `DOSIS_PROYECCION` | `ReglaProyeccionDosis` (M09) | `LoteIngerido` de `AH-DOSIS-TASA` (máximo una evaluación por hora) | `radiacion` |
| `FC_RECUPERACION` | `ReglaRecuperacionFc` (M12) | `LoteIngerido` de `8867-4` | `corazon` |
| `SUENO_MAL` | `ReglaMalSueno` ([M06](../../M06-sueno/backend/README.md#3-reglas-de-negocio)) | `ObservacionRegistrada` de `AH-SUENO-DURACION` (solo ese: cada noche publica dos y se evaluaría dos veces) | `sueno` |
| `EXAMEN_CRITICO`, `EXAMEN_DELTA`, `EXAMEN_TENDENCIA`, `EXAMEN_FUERA_RANGO` | `ReglaResultadoExamen` (M12) | `ResultadoExamenCargado` | Según la prueba (M16) |

Interfaz:

```php
interface ReglaAnalitica
{
    public function codigo(): string;                    // p. ej. 'COG_DETERIORO'
    public function familia(): string;                   // p. ej. 'COG'; ver M16 backend §3.2
    public function sistema(): Sistema;                  // enum compartido
    public function seActivaCon(): array;                // clases de evento y códigos de variable
    /** @return Hallazgo[] */
    public function evaluar(ContextoEvaluacion $ctx): array;
}
```

### 3.4 Tendencias (HU-12.04)

- **Pendiente** de 7 y 30 días: regresión lineal por mínimos cuadrados sobre los agregados diarios de M04 **con `contexto = reposo`** para los signos vitales (la línea base también es de reposo) o sobre los registros diarios de cada módulo; se informa la pendiente por semana y el valor *p* de la pendiente.
- **EWMA** con λ = 0,3 (parámetro `ewma_lambda` de M16) para suavizar las gráficas.
- La tendencia solo genera hallazgos a través de las reglas que la usan (deterioro cognitivo, exámenes, masa).

### 3.5 Explicación (HU-12.03)

Todo hallazgo guarda en `explicacion`:

```json
{
  "texto": "FC 128 lpm sostenida 5 min en reposo (límite 120). Línea base 61 ± 3 lpm.",
  "regla": "FC_REPOSO_LIMITES",
  "valor": 128, "unidad": "/min", "umbral": "fuera_de [40, 120]",
  "linea_base": { "media": 61, "desviacion": 3, "fuente": "previa_vuelo" },
  "contexto": "reposo",
  "ventana": { "desde": "2026-11-03T10:00:00Z", "hasta": "2026-11-03T10:05:00Z" },
  "datos_relacionados": { "sueno_4_noches_h": 4.9, "fatiga": 7 }
}
```

`datos_relacionados` es opcional y lo llenan las reglas que cruzan módulos.

### 3.6 Garantía para E0

Con los parámetros de simulación de [04 §3.3](../../../docs/propuesta/04-manejo-de-datos.md#33-parámetros-sugeridos-por-variable) y los umbrales de M16, la misión normal (semilla 42, 30 días) produce **0 hallazgos de aviso o críticos**. Es un criterio de cierre del Sprint 3 y una prueba automática.

La garantía no depende de la suerte de una semilla: las reglas con más riesgo se ajustaron simulando 20.000 misiones normales.

| Regla | Riesgo de aviso falso en E0 | Ajuste |
|---|:-:|---|
| Precisión de aritmética (`COG_DETERIORO`) | 22 % → **0,1 %** | σ mínima de 2 → 7,5 puntos ([M05](../../M05-psicologia/backend/README.md#32-detección-de-deterioro-regladeteriorocognitivo)) |
| `COG_DETERIORO` completa (7 métricas, todas las condiciones) | ~97 % → **0,8 %** | La pendiente ya no dispara avisos; σ mínima relativa del 10 % en la mediana del PVT y el tiempo de aritmética; E4 +8 %/día (detección 95 %) |
| Tendencia de Hb (`EXAMEN_TENDENCIA`) | 17 % → **0,4 %** | Cambio mínimo de 5 → 7,5 % ([M07](../../M07-examenes/backend/README.md#3-reglas-de-negocio)) |
| Mal sueño (`SUENO_MAL`) | **0,2 %** | Sin cambios |

Si alguien cambia los parámetros del simulador o un umbral, hay que repetir esa simulación de Monte Carlo antes de fusionar el cambio. Los scripts están en [`herramientas/montecarlo/`](../../../herramientas/README.md) y pasan a la carpeta del simulador (`simulador/tests/montecarlo/`) en el Sprint 1, junto con HU-17.01.

## 4. Contratos

| Ofrece (`Analitica\Contratos\…`) | Uso |
|---|---|
| `ReglaAnalitica` (interfaz), `ContextoEvaluacion`, `Hallazgo` (DTO), `Sistema` (enum) | M05, M08, M09 |
| `RegistroLineaBase::registrar(...)` | M02 (previa al vuelo), M05 (cognitiva) |
| `Importador` de líneas base (implementa `Sincronizacion\Contratos\Importador`) | M15 (líneas base hacia la Tierra; solicitudes de recálculo hacia la nave) |
| `ConsultaLineaBase::vigente(tripulanteId, codigo, contexto)` | M04, M05, M14 |
| `ConsultaTendencias::pendiente(tripulanteId, codigo, dias)` | M10, M14 |
| `ConsultaHallazgos::delDia(tripulanteId, fecha)` | M14 |

| Usa | De |
|---|---|
| `ConsultaObservaciones` | M03 |
| `ConsultaAgregados` | M04 |
| `ConsultaRegistrosSueno`, `ConsultaSesiones`, `ConsultaEscalas`, `ConsultaResultados`, `ConsultaSintomas` | M06, M05, M07, M11 |
| `ConsultaUmbrales`, `CatalogoPruebas`, `ParametrosMision` | M16 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Ver tendencias y líneas base | `ver-datos-tripulacion` (`cmo`, `medico_vuelo`) |
| Solicitar el recálculo de una línea base | `registrar-linea-base` (`medico_vuelo`) |

## 6. Archivos

```
app/Modules/Analitica/
├── AnaliticaServiceProvider.php
├── routes.php
├── Contratos/{ReglaAnalitica,ContextoEvaluacion,Hallazgo,RegistroLineaBase,ConsultaLineaBase,ConsultaTendencias,ConsultaHallazgos}.php
├── Domain/
│   ├── Enums/{Sistema,Nivel,Operador,UnidadPersistencia,FuenteLineaBase}.php
│   ├── Reglas/{EvaluadorUmbral,EvaluadorPersistencia,ReglaRecuperacionFc,ReglaResultadoExamen}.php
│   ├── Reglas/Estadistica/{RegresionLineal,Ewma,PuntuacionZ}.php
│   └── Eventos/HallazgoDetectado.php
├── Application/{MotorReglas,CalcularLineaBaseInicial,RecalcularLineaBase,GenerarExplicacion}.php
├── Http/
│   ├── Controllers/Web/{TendenciasController,LineasBaseController}.php
│   └── Requests/RecalcularLineaBaseRequest.php
├── Infrastructure/
│   ├── Models/{LineaBase,HallazgoModelo}.php
│   ├── Repositorios/*.php
│   └── Listeners/{AlIngerirLote,AlRegistrarObservacion,AlCompletarTest,AlCargarExamen,AlModificarUmbral}.php
├── Database/Migrations/{create_lineas_base,create_hallazgos}.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] FC de 125 lpm en **ejercicio** → ningún hallazgo; en **reposo** durante 5 min → crítico.
- [ ] FC de 125 lpm en reposo durante 4 min y 70 en el minuto 5 → nada.
- [ ] SpO₂ de 91 % durante 3 min → aviso; de 89 % → crítico.
- [ ] Sin línea base, `FC_REPOSO_CAMBIO` no se evalúa y `FC_REPOSO_LIMITES` sí.
- [ ] Un artefacto dentro de la ventana no cuenta: con N = 5, 4 válidas que cumplen + 1 artefacto → se cumple; con N = 3, 2 válidas → todavía no.
- [ ] Un episodio abierto se actualiza en vez de crear otro hallazgo, suma una ocurrencia por cada ventana completa y se cierra con una lectura que no cumple.

Detalle de cada historia en las [historias de backend](HU/README.md).
- [ ] Desactivar un umbral en M16 detiene su evaluación en la siguiente lectura.
- [ ] **E0 (semilla 42, 30 días) → 0 avisos y 0 críticos.**
- [ ] Una FC de sueño 15 % por debajo de la línea base de **reposo** pero dentro de la de **sueño** no genera `FC_REPOSO_CAMBIO`.
- [ ] Un lote histórico de una hora con SpO₂ de 89 % entre los minutos 20 y 26 genera el crítico (ventana deslizante), y un solo hallazgo para ese episodio.
- [ ] Un umbral importado desde la Tierra se aplica en la siguiente lectura (la caché se invalida aunque la importación ignore los demás eventos).
- [ ] La línea base de la ventana inicial se crea con la primera lectura del día 8 y viaja a la Tierra en el paquete `resumen`.
