# M12 · Analítica y línea base

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M12 |
| **Grupo** | Inteligencia |
| **Carpeta Laravel** | `astrohealth/app/Modules/Analitica/` |
| **Épica** | EP-12 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon (HU-12.01, HU-12.04) · Gabriela (HU-12.03) |
| **Sprints** | 3 (HU-12.01, HU-12.02, HU-12.03, HU-12.04); la tabla `lineas_base` se crea en el Sprint 1 (HU-02.03) |
| **Escenarios** | E0, E2, E3, E4, E7, E8 (todos los que generan o no deben generar hallazgos) |

## 2. Propósito

Ser **el cerebro explicable** del sistema: guarda la línea base de cada tripulante, evalúa las reglas (de umbral y especializadas) según el contexto de actividad, calcula tendencias y produce **hallazgos con su explicación**. No crea alertas: entrega los hallazgos de nivel aviso y crítico a M13.

## 3. Alcance

| Hace | No hace |
|---|---|
| Línea base de referencia con la política de [04 §4.4](../../docs/propuesta/04-manejo-de-datos.md#44-métodos-de-análisis-explicables) | Machine learning (evolución) |
| Motor de reglas de umbral configurables (M16) | Guardar los valores de los umbrales (M16) |
| Ejecución de las reglas especializadas que aportan M05, M08 y M09 | Contener el conocimiento clínico de cada módulo |
| Hallazgos de exámenes a partir de `ResultadoExamenCargado` | Interpretar exámenes (M07) |
| Tendencias de 7 y 30 días (regresión lineal y EWMA) | Crear, deduplicar o notificar alertas (M13) |
| Explicación legible de cada hallazgo | |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| **Motor de reglas** | Evalúa las reglas de umbral de M16 y las reglas especializadas registradas |
| **Línea base personal** | Referencia fija por tripulante, variable y contexto; recálculo solo autorizado |
| **Tendencias** | Pendiente de 7 y 30 días sobre agregados diarios; EWMA para suavizar |
| **Correlaciones** | Acceso a datos de varios módulos en una misma ventana de tiempo para las reglas que cruzan módulos |
| **Explicación** | Texto y datos estructurados del "por qué" de cada hallazgo |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md) (HU-12.02 es el motor, sin pantalla). El viaje de las líneas base y de las solicitudes de recálculo entre nodos se construye con HU-15.02 (Sprint 4).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-12.01 Línea base de referencia | 3 | Líneas base vigentes para los 4 tripulantes tras 7 días de datos |
| HU-12.02 Umbrales según el contexto | 3 | Las reglas de M16 se evalúan con persistencia y contexto; E0 sin hallazgos |
| HU-12.03 Explicación de cada hallazgo | 3 | Todo hallazgo tiene texto y datos estructurados |
| HU-12.04 Tendencias de 7 y 30 días | 3 | F3 muestra pendientes y EWMA |
