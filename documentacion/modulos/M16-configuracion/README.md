# M16 · Configuración

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M16 |
| **Grupo** | Soporte |
| **Carpeta Laravel** | `astrohealth/app/Modules/Configuracion/` |
| **Épica** | EP-16 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela (HU-16.01, HU-16.03) · Jhon (HU-16.02) |
| **Sprints** | 1 (HU-16.01, HU-16.02) · 4 (HU-16.03) |
| **Escenarios** | Todos (los umbrales deciden qué es una alerta) |

## 2. Propósito

Ser la **única fuente de los valores que cambian sin tocar el código** ([02 §1](../../docs/propuesta/02-modulos-del-sistema.md#1-principios-de-diseño): "configurable, no codificado"): umbrales de alerta, catálogos de variables, pruebas y síntomas, y parámetros de la misión. Todo cambio queda versionado y auditado.

## 3. Alcance

| Hace | No hace |
|---|---|
| Umbrales versionados, editables por el médico de vuelo en la Tierra | Evaluar los umbrales (M12) |
| Ajustes temporales del CMO durante un apagón, con revisión posterior | Permitir editar umbrales a bordo fuera de un ajuste temporal |
| Catálogos de variables, pruebas y síntomas | Inventar valores clínicos: cada valor tiene su fuente o se marca como parámetro de diseño |
| Parámetros de la misión y del simulador de enlace | |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Umbrales | Reglas de umbral con versión, contexto, persistencia e historial |
| Ajustes temporales a bordo | Ajuste del CMO, sincronizado y revisado por el médico de vuelo |
| Catálogos | Variables, pruebas de laboratorio e imagen, tipos de síntoma |
| Parámetros de misión | Perfil de enlace, frecuencias, σ mínimas y parámetros de diseño |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). HU-16.01 siembra umbrales, variables y parámetros; HU-16.02, pruebas y síntomas (y edita variables y parámetros).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-16.01 Editar los umbrales | 1 | Umbrales sembrados; el médico de vuelo los edita con motivo e historial |
| HU-16.02 Catálogo de exámenes y rangos | 1 | Catálogo sembrado y editable por el admin |
| HU-16.03 Ajuste temporal durante un apagón | 4 | El ajuste del CMO rige a bordo y la Tierra lo confirma o revierte al recibirlo |
