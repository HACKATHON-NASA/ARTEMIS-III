# M13 · Alertas y casos clínicos · Base de datos

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · [Frontend](../frontend/README.md) · **Base de datos**

Tablas **MySQL** que este módulo posee: es el único que las migra y las escribe ([reglas de comunicación](../../README.md#4-reglas-de-comunicación-entre-módulos)). Siguen las [convenciones de base de datos](../../README.md#7-convenciones-de-base-de-datos).

---

## 1. Tablas

### `alertas`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` nulo | `null` = toda la tripulación |
| `familia` | `varchar(30)` | Clave de deduplicación junto con el tripulante |
| `regla_codigo` | `varchar(30)` | Regla del último hallazgo |
| `sistema` | `varchar(10)` | `corazon`, `sueno`, `mente`, `radiacion`, `cuerpo`, `ambiente` |
| `nivel` | `varchar(8)` | `aviso` o `critico` |
| `explicacion` | `json` | La del último hallazgo (formato de [M12 backend §3.5](../../M12-analitica/backend/README.md#35-explicación-hu-1203)) |
| `estado` | `varchar(12)` | `abierta`, `confirmada`, `descartada`, `cerrada` |
| `ocurrencias` | `int` | Hallazgos agrupados |
| `primera_en`, `ultima_en` | `datetime(3)` | |
| `confirmada_por` | `bigint` nulo | `null` si se confirmó automáticamente |
| `confirmada_en` | `datetime(3)` nulo | |
| `motivo_descarte` | `varchar(15)` nulo | `falso_positivo`, `artefacto`, `otro` |
| `confirmacion` | `varchar(12)` | `automatica`, `manual` o `no_aplica` |
| `nota_cierre` | `varchar(500)` nulo | Al descartar o al cerrar un aviso sin caso |
| `cerrada_por` | `bigint` nulo | |
| `caso_clinico_id` | `uuid` nulo | Clave foránea añadida en HU-13.03 |
| `nodo_origen` | `varchar(6)` | Siempre `nave` en el MVP (la Tierra no evalúa reglas); se guarda para una evolución con reglas propias de la Tierra |
| `actualizada_en` | `datetime(3)` | Último cambio; la Tierra aplica un paquete `alerta` solo si es más reciente |
| `cerrada_en` | `datetime(3)` nulo | |

Índice: `(tripulante_id, familia, estado)`.

### `casos_clinicos`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | |
| `tripulante_id` | `uuid` nulo | |
| `protocolo_codigo` | `varchar(30)` | |
| `protocolo_pasos` | `json` | Copia de los pasos al abrir el caso |
| `protocolo_validado_en` | `date` nulo | Copia del estado de validación al abrir |
| `estado` | `varchar(8)` | `abierto`, `cerrado` |
| `abierto_por` | `bigint` nulo | `null` si se abrió automáticamente |
| `abierto_en` | `datetime(3)` | |
| `cerrado_en` | `datetime(3)` nulo | |
| `resultado` | `text` nulo | Resumen al cerrar |
| `actualizado_en` | `datetime(3)` | Último cambio (paquete `alerta`) |

### `acciones`

| Columna | Tipo | Notas |
|---|---|---|
| `id` | `uuid` | Generado en el cliente |
| `caso_clinico_id` | `uuid` | |
| `tipo` | `varchar(14)` | `paso`, `nota` o `recomendacion` |
| `paso_numero` | `tinyint` nulo | Paso del protocolo, si corresponde |
| `descripcion` | `varchar(500)` | |
| `realizada_por` | `bigint` nulo | `null` en una recomendación de la Tierra |
| `autor` | `varchar(150)` | Copia legible del autor |
| `realizada_en` | `datetime(3)` | |
| `created_at` | `datetime(3)` | |

### `protocolos` (sembrados)

| Columna | Tipo | Notas |
|---|---|---|
| `codigo` | `varchar(30)` | Clave primaria |
| `nombre` | `varchar(100)` | |
| `familias` | `json` | Familias de reglas que lo activan |
| `pasos` | `json` | `[{ "numero": 1, "texto": "…", "fuente": "…" }]`; `fuente` es `null` en los pasos genéricos de HU-13.03 y obligatoria desde HU-13.04 |
| `validado_por` | `varchar(100)` nulo | Nombre y profesión de quien lo validó |
| `validado_en` | `date` nulo | `null` = pendiente de validación clínica |
| `created_at`, `updated_at` | `datetime(3)` | Se editan en la Tierra y llegan a la nave en el paquete `configuracion` (M15) |

Los pasos de la tabla son los genéricos con los que nace HU-13.03. En HU-13.04 se reescriben a partir de documentos públicos (p. ej., NASA-STD-3001 y guías clínicas publicadas) y cada paso cita su fuente; **la IA no redacta pasos clínicos** ([plan §10, regla 6](../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)).

| Protocolo | Familias | Pasos sembrados (genéricos, **a validar por profesionales**) |
|---|---|---|
| `evento_solar` | `DOSIS_SPE` | 1. Ordenar a la tripulación ir al refugio · 2. Confirmar que cada tripulante está en el refugio · 3. Vigilar la tasa de dosis hasta que se normalice · 4. Registrar la dosis del evento |
| `hipoxemia` | `SPO2` | 1. Verificar la colocación del oxímetro y repetir la medición · 2. Registrar síntomas (disnea, confusión) · 3. Revisar el pCO₂ del módulo · 4. Aplicar el procedimiento de a bordo · 5. Informar a la Tierra |
| `arritmia` | `ECG` | 1. Detener el ejercicio · 2. Repetir el ECG en reposo · 3. Registrar síntomas · 4. Aplicar el procedimiento de a bordo · 5. Informar a la Tierra |
| `generico` | Cualquier otra | 1. Revisar la explicación de la alerta · 2. Evaluar al tripulante · 3. Registrar las acciones · 4. Informar a la Tierra |

## 2. Migraciones y seeders

```
├── Database/
│   ├── Migrations/{create_alertas,create_alerta_hallazgos,create_protocolos,create_casos_clinicos,add_caso_fk_to_alertas,create_acciones}.php
│   └── Seeders/ProtocolosSeeder.php
```

## 3. Relaciones

| Desde | Hacia | Tipo |
|---|---|---|
| `alertas.caso_clinico_id` | `casos_clinicos.id` | `FOREIGN KEY` (nula) |
| `acciones.caso_clinico_id` | `casos_clinicos.id` | `FOREIGN KEY` |
| `casos_clinicos.protocolo_codigo` | `protocolos.codigo` | `FOREIGN KEY` |
| `alertas.tripulante_id`, `casos_clinicos.tripulante_id` | `tripulantes.id` (M02) | Lógica |
| `alertas.confirmada_por`, `casos_clinicos.abierto_por`, `acciones.realizada_por` | `users.id` (M01) | Lógica |

Orden de migración: `alertas` y `alerta_hallazgos` en HU-13.01; `protocolos` → `casos_clinicos` → clave foránea de `alertas.caso_clinico_id` → `acciones` en HU-13.03. La tabla `alerta_hallazgos` (`alerta_id`, `hallazgo_id`, `ocurrencia`, `nivel`, `recibido_en`; única `(hallazgo_id, ocurrencia)`) evita contar dos veces un evento y que un episodio descartado vuelva a avisar. Columnas exactas en las [historias de base de datos](HU/README.md).

## 4. Consultas principales

| Consulta | Índice |
|---|---|
| ¿Hay una alerta activa de esta familia? (deduplicación) | `alertas (tripulante_id, familia, estado)` |
| Bandeja por gravedad y antigüedad | `alertas (estado, nivel, primera_en)` |
| Alertas de un día (reportes) | `alertas (tripulante_id, primera_en)` |

## 5. Volumen

En E0, cero alertas. Con los 9 escenarios, unas decenas. `acciones` crece con los pasos de los casos.

## 6. Seeders

`ProtocolosSeeder`: los 4 protocolos (`evento_solar`, `hipoxemia`, `arritmia`, `generico`). Desde HU-13.04 **falla** si un paso no tiene `fuente`; siembra `validado_en = null`.
