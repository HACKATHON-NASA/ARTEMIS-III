# M07 · Exámenes médicos · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

| Método | Ruta | Autenticación | Descripción |
|---|---|---|---|
| `POST` | `/api/v1/examenes/resultados` | Solo token: PWA `registros:escribir` o el del dispositivo `simulador` (con `tripulante_id` obligatorio) | `{ resultados: [{ id, tripulante_id, codigo_prueba, fecha_muestra, valor, unidad }] }`; idempotente por `id` |

Respuestas: `201` con la interpretación de cada resultado, `200` (todos existían), `422` con el error por resultado (por ejemplo, unidad distinta a la del catálogo), `401`/`403`. Con el token de la PWA, el `tripulante_id` se toma del usuario.

### Importación por CSV (web, requiere conexión)

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/astronauta/examenes/importar/previsualizar` | Recibe el archivo y devuelve las filas válidas y las erróneas con su motivo; guarda la vista previa 30 min |
| `POST` | `/astronauta/examenes/importar/confirmar` | Guarda las filas válidas de la vista previa y **borra el token** (un segundo envío no duplica nada) |

Formato ([04 §6.2](../../../docs/propuesta/04-manejo-de-datos.md#62-formato-de-carga-por-csv)): `fecha_muestra,codigo_prueba,valor,unidad`, UTF-8, separador coma y punto decimal.

### PDF del laboratorio (web, requiere conexión, HU-07.05)

| Método | Ruta | Descripción |
|---|---|---|
| `POST` | `/astronauta/examenes/documentos` | *Multipart*: `fecha_muestra` y `archivo`. Valida tipo `application/pdf` (por contenido, no por extensión) y tamaño. Si ya hay un PDF de esa fecha, el nuevo lo anula (`anula_id`); si es el mismo archivo (`sha256`), no se duplica |
| `GET` | `/examenes/documentos/{id}` | Descarga con permiso (propietario, `cmo`, `medico_vuelo`) |

## 2. Eventos

| Publica | Consume |
|---|---|
| `ResultadoExamenCargado` (uno por resultado) | — |

El PDF no publica eventos: M07 lo encola hacia la Tierra con `ColaSincronizacion::encolar('adjunto', 3, …)` (M15).

## 3. Reglas de negocio

1. **Unidad:** debe coincidir con la del catálogo; si no → error "La unidad de Hb debe ser g/dL" (HU-07.01). La `fecha_muestra` no puede ser futura.
2. **Un resultado por prueba y fecha de muestra:** si llega otro de la misma prueba y la misma `fecha_muestra` (con otro `id`), es una **corrección** (con un bloqueo con nombre por tripulante, prueba y fecha, para que dos envíos simultáneos no dejen dos vigentes): se guarda con `anula_id` apuntando al anterior, y las reglas de *delta* y tendencia usan solo los vigentes. Sin esto, cargar dos veces el mismo examen (por formulario y por CSV) contaría dos tomas.
3. **Interpretación** (rango según el `sexo` del tripulante, M02):
   1. si hay rango crítico y el valor está fuera → `critico`;
   2. si está por debajo / por encima del rango de referencia → `bajo` / `alto`;
   3. si no → `normal`. Una prueba sin rango de referencia (p. ej., CTX) es siempre `normal` y se sigue por tendencia.
4. **Rango vigente (HU-07.02):** si el médico cambia un rango, la vista muestra la interpretación con el rango vigente **y** conserva la original (`interpretacion`), indicando que el rango cambió.
5. ***Delta check*:** `delta_superado` si |valor − valor de la toma anterior de la misma prueba| > `delta_umbral` del catálogo (Hb: 1,5 g/dL).
6. **Tendencia:** `tendencia_desfavorable` si las **3 últimas tomas vigentes** (incluida la actual) empeoran una tras otra en la `direccion_desfavorable` del catálogo (Hb: `baja`; CTX: `sube`) **y** el cambio entre la primera y la última supera el `tendencia_min_pct` de M16 (**7,5 %** del valor inicial), para no confundir el ruido con una tendencia. Las pruebas con dirección `ambas` (reticulocitos, calcio) no tienen regla de tendencia: se vigilan por su rango.

   > **Por qué 7,5 % y no 5 %:** la Hb simulada baja 0,05 g/dL por semana con un ruido de 0,2 g/dL, como en la anemia espacial real. Con el 5 %, tres tomas al azar en descenso bastaban: al simular 20.000 misiones normales (E0), el **17 %** generaba un aviso falso. Con el 7,5 % baja al **0,4 %**, y el descenso de E7 (14,1 → 13,4 → 12,7 → 12,0) se detecta el 99,8 % de las veces.
7. **Hallazgos** que M12 genera al recibir `ResultadoExamenCargado` con `ReglaResultadoExamen`, que se implementa en HU-07.02 ([04 §6.3](../../../docs/propuesta/04-manejo-de-datos.md#63-reglas-de-interpretación)). La interpretación se calcula y guarda desde HU-07.01; el *delta* y la tendencia, desde HU-07.02. Detalle en las [historias de backend](HU/README.md):

| Condición | Nivel | Texto de ejemplo |
|---|---|---|
| `interpretacion = critico` | 🚨 Crítico | "Hb 6,8 g/dL, por debajo del rango crítico (7,0)" |
| `delta_superado` | ⚠️ Aviso | "Cambio brusco de Hb: −1,8 g/dL frente a la toma anterior" |
| `tendencia_desfavorable` | ⚠️ Aviso | "Descenso progresivo de Hb en 3 tomas (14,1 → 13,4 → 12,7)" |
| `bajo`/`alto` en una prueba con `alerta_fuera_rango = true` (OCT, Doppler) | ⚠️ Aviso | "Δ TRT de 24 µm (≥ 20 µm)" |
| `bajo`/`alto` en las demás pruebas | Sin hallazgo | Solo se marca en la tabla y la gráfica |

## 4. Contratos

| Ofrece (`Examenes\Contratos\…`) | Uso |
|---|---|
| `ConsultaResultados::historial(tripulanteId, pruebaCodigo)` | M12 (explicación), M14 |
| `ConsultaResultados::ultimos(tripulanteId)` | F2, F3, M14 |
| `ConsultaResultados::hayDePrueba(pruebaCodigo)` | M16 (no se cambia la unidad de una prueba con resultados) |

| Usa | De |
|---|---|
| `CatalogoPruebas::porCodigo()`, `ParametrosMision` (`tendencia_min_pct`) | M16 |
| `ConsultaTripulantes` (sexo) | M02 |
| `ColaSincronizacion`, `Importador` | M15 (PDF hacia la Tierra) |

## 5. Permisos

| Acción | Roles |
|---|---|
| Cargar y ver los exámenes propios | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver los exámenes de la tripulación | `cmo`, `medico_vuelo` |
| Adjuntar el PDF propio | `astronauta`, `cmo` |

## 6. Archivos

```
app/Modules/Examenes/
├── ExamenesServiceProvider.php
├── routes.php
├── Contratos/ConsultaResultados.php
├── Domain/
│   ├── Enums/{Interpretacion,OrigenResultado}.php
│   ├── Reglas/{Interpretador,DeltaCheck,DetectorTendencia}.php
│   └── Eventos/ResultadoExamenCargado.php
├── Application/{CargarResultados,PrevisualizarCsv,ConfirmarCsv,ProgramarExamenes,AdjuntarDocumento}.php
├── Http/
│   ├── Controllers/Web/{ExamenesController,ImportacionController,DocumentoController}.php
│   ├── Controllers/Api/ResultadoController.php
│   └── Requests/{ResultadosRequest,ImportarCsvRequest,DocumentoRequest}.php
├── Infrastructure/
│   ├── Models/{ResultadoExamen,ExamenProgramado,DocumentoExamen}.php
│   └── Repositorios/*.php
├── Database/
│   ├── Migrations/{create_resultados_examen,create_examenes_programados,create_documentos_examen}.php
│   └── Seeders/ExamenesProgramadosSeeder.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Hb 12,5 g/dL: `bajo` para un hombre y `normal` para una mujer.
- [ ] Hb 6,8 g/dL → `critico`.
- [ ] Tomas 14,1 → 13,4 → 12,7 (−9,9 %) → `tendencia_desfavorable`; 14,1 → 13,4 → 13,6 → no; 14,5 → 14,1 → 13,8 (−4,8 %) → no; 14,1 → 14,0 → 13,9 (−1,4 %) → no.
- [ ] **E0 (semilla 42, 30 días): ningún aviso de tendencia ni de *delta* en ninguna prueba.**
- [ ] Cargar dos veces la Hb del mismo día deja un solo resultado vigente y la tendencia no lo cuenta dos veces.
- [ ] Un resultado con `fecha_muestra` de mañana devuelve `422`; el token del simulador sin `tripulante_id`, también.
- [ ] Un CSV con una fila con unidad `g/L` muestra esa fila como errónea en la vista previa.
- [ ] Un resultado cargado hoy con muestra del día 10 se ordena por el día 10.
- [ ] Un archivo `.pdf` que en realidad es una imagen devuelve `422`; un PDF de 3 MB devuelve `422`.
- [ ] Otro astronauta recibe `403` al descargar el PDF de T2.
