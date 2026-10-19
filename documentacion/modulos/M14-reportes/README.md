# M14 · Reportes

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M14 |
| **Grupo** | Soporte |
| **Carpeta Laravel** | `astrohealth/app/Modules/Reportes/` |
| **Épica** | EP-14 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon (HU-14.01, HU-14.02) · Gabriela (HU-14.03) |
| **Sprints** | 3 (HU-14.02) · 4 (HU-14.01, HU-14.03) |
| **Escenarios** | Ninguno propio; resume los de cada día |

## 2. Propósito

Dar al médico de vuelo un **reporte diario por tripulante** que se lee sin navegar por todo el sistema, un **reporte de misión** con la evolución completa, y a los investigadores una **exportación anonimizada** de los datos. Como los reportes viajan a la Tierra por M15, el resumen diario es lo primero que se entiende tras un corte de enlace.

## 3. Alcance

| Hace | No hace |
|---|---|
| Reporte diario en PDF por tripulante (automático y a demanda) | Gráficas interactivas en el PDF (usa tablas semanales) |
| Reporte de misión en PDF por tripulante, a demanda (HU-14.03) | Enviar el reporte de misión a la Tierra (cada nodo lo genera con sus datos) |
| Exportación CSV anonimizada para investigación | Exportación FHIR (evolución) |
| Historial de reportes generados | Análisis estadístico propio (usa M12) |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Reporte diario | PDF de una página por tripulante y día |
| Reporte de misión | PDF con la evolución semanal desde el inicio de la misión (HU-14.03) |
| Exportación | CSV anonimizado (HU-14.02) |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). El backend lo hace Julieth.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-14.02 Exportar datos anonimizados | 3 | CSV sin datos identificables y con seudónimo estable |
| HU-14.01 Reporte diario por tripulante | 4 | PDF diario automático y a demanda, enviado a la Tierra |
| HU-14.03 Reporte de misión por tripulante | 4 | PDF a demanda con la evolución semanal de toda la misión |
