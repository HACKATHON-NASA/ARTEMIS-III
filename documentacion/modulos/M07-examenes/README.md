# M07 · Exámenes médicos

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M07 |
| **Grupo** | Clínico |
| **Carpeta Laravel** | `astrohealth/app/Modules/Examenes/` |
| **Épica** | EP-07 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela |
| **Sprints** | 2 (HU-07.01, HU-07.03, HU-07.04, HU-07.05) · 3 (HU-07.02) |
| **Escenarios** | E7 (anemia progresiva) |
| **Afecciones** | Anemia espacial, pérdida ósea, cálculos renales, déficit de vitaminas, SANS, trombosis yugular ([01 §3](../../docs/propuesta/01-analisis-enfermedades.md#3-catálogo-de-afecciones)) |

## 2. Propósito

Que el astronauta **cargue sus resultados de laboratorio e imagen**, y que el sistema los registre con la fecha de la muestra, los interprete frente al rango de referencia según el sexo, detecte cambios bruscos y tendencias desfavorables, y recuerde los exámenes de las afecciones que no dan síntomas. Es el segundo caso de uso central ([04 §6](../../docs/propuesta/04-manejo-de-datos.md#6-ejemplo-completo-exámenes-médicos)).

## 3. Alcance

| Hace | No hace |
|---|---|
| Carga por formulario (también sin conexión) y por CSV con vista previa (requiere conexión) | Analizar imágenes (OCT, Doppler): se carga el **resultado estructurado** |
| Interpretación `bajo` / `normal` / `alto` / `critico` según el sexo | Leer los valores del PDF (el PDF se adjunta como documento; los valores se cargan por formulario o CSV) |
| *Delta check* y tendencia | Crear alertas (M12 crea hallazgos y M13 alertas) |
| Gráfica por prueba con su banda de referencia | Editar el catálogo de pruebas (M16) |
| Recordatorios de exámenes programados | |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Carga de resultados | Formulario y CSV con vista previa |
| Catálogo de pruebas | Se **consulta** en M16 (código, unidad, rangos, *delta*, dirección desfavorable) |
| Interpretación | Marca cada valor al cargarlo y calcula la interpretación con el rango vigente al mostrarlo |
| Evolución | Gráfica histórica y comparación con la toma anterior |
| Exámenes programados | Próxima fecha de cada examen y recordatorio en F1 |
| Documentos | PDF original del laboratorio, uno por fecha de muestra (HU-07.05) |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). HU-07.01 ya guarda la interpretación (`bajo`/`normal`/`alto`/`critico`); HU-07.02 añade el *delta*, la tendencia y la regla de M12 que genera los hallazgos.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-07.01 Cargar resultados | 2 | Formulario con unidad automática; CSV con vista previa (de un solo uso); resultado ligado a la fecha de la muestra e interpretado según el sexo |
| HU-07.03 Evolución con su rango | 2 | Gráfica con banda de referencia y tendencia |
| HU-07.04 Recordatorios | 2 | F1 muestra los exámenes vencidos o próximos (7 días) |
| HU-07.05 Adjuntar el PDF del laboratorio | 2 | PDF ≤ 2 MB por fecha de muestra, descargable por el CMO y el médico de vuelo; viaja a la Tierra con prioridad 3 |
| HU-07.02 Marcar valores fuera de rango | 3 | Marca con color, icono y texto; crítico → alerta crítica; **E7 pasa** y E0 no genera avisos de tendencia (umbral del 7,5 %) |
