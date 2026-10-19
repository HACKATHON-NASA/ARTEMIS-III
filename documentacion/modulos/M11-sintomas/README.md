# M11 · Síntomas

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M11 |
| **Grupo** | Clínico (transversal) |
| **Carpeta Laravel** | `astrohealth/app/Modules/Sintomas/` |
| **Épica** | EP-11 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon |
| **Sprints** | 2 (HU-11.01, HU-11.02, HU-11.03) |
| **Escenarios** | E3 (las cefaleas que se cruzan con el CO₂) |
| **Afecciones** | Mareo espacial, cefalea, dolor de espalda, congestión, lesiones cutáneas ([01 §3](../../docs/propuesta/01-analisis-enfermedades.md#3-catálogo-de-afecciones)) |

## 2. Propósito

Que el tripulante **informe un síntoma en pocos toques**, sin interrumpir sus tareas, y que durante los primeros días responda un **cuestionario de mareo espacial**. Los síntomas son el puente entre lo que siente la persona y lo que miden los sensores: por ejemplo, la cefalea se cruza con el CO₂ (M08).

## 3. Alcance

| Hace | No hace |
|---|---|
| Registro de síntomas del catálogo con intensidad de 0 a 10, también sin conexión | Diagnosticar |
| Cuestionario de mareo de los días 1 a 3 de misión | Analizar las fotografías |
| Una fotografía opcional por síntoma, también sin conexión (HU-11.03) | Varias fotografías por síntoma |
| Historial de síntomas para el CMO y el médico de vuelo | Alertar por un síntoma aislado (las alertas salen de las reglas de M12) |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Diario de síntomas | Selección rápida e intensidad |
| Adjuntos | Una fotografía por síntoma (p. ej., de una lesión cutánea) |
| Cuestionario de mareo espacial | Se activa automáticamente los días 1 a 3 de misión |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). Incluyen la vista de síntomas del médico de vuelo (`Tierra/Sintomas/Show`), que faltaba.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-11.01 Registrar un síntoma en pocos toques | 2 | Registro en ≤ 3 toques, también sin conexión |
| HU-11.02 Cuestionario de mareo | 2 | Aparece los días 1 a 3 y deja de aparecer al responderlo |
| HU-11.03 Adjuntar una fotografía | 2 | Foto comprimida ≤ 500 KB, también sin conexión; el CMO la ve en la pestaña "Síntomas" |
