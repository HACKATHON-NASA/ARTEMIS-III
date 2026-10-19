# M13 · Alertas y casos clínicos

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M13 |
| **Grupo** | Inteligencia |
| **Carpeta Laravel** | `astrohealth/app/Modules/Alertas/` |
| **Épica** | EP-13 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela · **Otros:** Miguel (fuentes de los protocolos, HU-13.04) |
| **Sprints** | 3 (HU-13.01, HU-13.02, HU-13.03) · 4 (HU-13.04) |
| **Escenarios** | E1, E2, E3, E4, E7, E8 (todos los que producen alertas) |

## 2. Propósito

Convertir los hallazgos en **alertas que las personas atienden**: las crea, evita duplicados, las prioriza, avisa a quien corresponde (con alarma sonora si es crítica), pide confirmación de los avisos, guía al CMO con protocolos paso a paso y aprende de los falsos positivos. También calcula el **semáforo** de cada tripulante.

## 3. Alcance

| Hace | No hace |
|---|---|
| Única creadora de alertas del sistema | Detectar hallazgos (M12) |
| Deduplicación y escalamiento por familia de reglas | Enviar alertas a la Tierra (M15 las encola al recibir `AlertaCreada`) |
| Confirmación automática por segunda medición y confirmación o descarte manual | Notificaciones *push*, correo o SMS |
| Casos clínicos con protocolos guiados y registro de acciones | Validar clínicamente los protocolos (lo hace un profesional de la salud) |
| Fuente de cada paso y estado de validación de cada protocolo (HU-13.04) | Mostrar un protocolo sin validar como si lo estuviera |
| Semáforo por sistema para F1 y F2 | |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| **Gestor de alertas** | Crea, deduplica, prioriza (`info` no crea alerta; `aviso`, `critico`) y notifica |
| **Confirmación** | Los avisos piden una segunda medición antes de considerarse confirmados |
| **Casos clínicos** | Agrupan alertas, acciones del CMO y su resultado |
| **Protocolos guiados** | Listas de verificación paso a paso según el tipo de caso |
| **Retroalimentación** | Descartes con motivo (`falso_positivo`, `artefacto`, `otro`) para ajustar los umbrales |
| **Semáforo** | Estado por sistema a partir de las alertas activas |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). El envío de las alertas a la Tierra (paquete `alerta`) se construye con HU-15.01 en el Sprint 3.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-13.01 Bandeja de alertas (incluye el gestor) | 3 | Alertas ordenadas por gravedad; deduplicación y escalamiento funcionando |
| HU-13.02 Confirmar o descartar | 3 | Confirmación manual y automática; descarte con motivo |
| HU-13.03 Caso clínico con protocolo | 3 | Las críticas abren caso; el CMO sigue los pasos y registra acciones |
| HU-13.04 Protocolos con fuente y validación | 4 | Cada paso cita su fuente; los protocolos sin validar muestran el aviso; editar un paso anula la validación |
