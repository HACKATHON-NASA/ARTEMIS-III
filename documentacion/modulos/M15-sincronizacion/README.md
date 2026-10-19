# M15 · Sincronización

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M15 |
| **Grupo** | Soporte |
| **Carpeta Laravel** | `astrohealth/app/Modules/Sincronizacion/` (y `resources/js/offline/` para la PWA) |
| **Épica** | EP-15 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon (HU-15.04, HU-15.02) · Gabriela (HU-15.03) |
| **Sprints** | 2 (HU-15.04) · 3 (HU-15.01) · 4 (HU-15.02, HU-15.03) |
| **Escenarios** | E5 (conjunción solar: 14 días sin enlace) |

## 2. Propósito

Hacer realidad la regla de oro **"registrar siempre, decidir a bordo, sincronizar cuando se pueda"** en sus dos niveles ([03 §6](../../docs/propuesta/03-propuesta-tecnologica.md#6-estrategia-offline-first)):

1. **Tableta ↔ servidor de a bordo:** la PWA guarda los registros sin red y los envía al reconectar.
2. **Nave ↔ Tierra:** una cola priorizada que atraviesa un **enlace simulado** con retardo, ventanas sin enlace y ancho de banda limitado, sin pérdidas ni duplicados.

## 3. Alcance

| Hace | No hace |
|---|---|
| Cola offline en la PWA (IndexedDB) con reintentos | Usar DTN real (HDTN/ION): evolución ([03 §4](../../docs/propuesta/03-propuesta-tecnologica.md#4-evaluación-de-la-visión-de-la-fase-2)) |
| Cola de salida priorizada en cada nodo | Cifrar los paquetes por encima de HTTPS (evolución: BPSec) |
| Enlace simulado (perfil `leo`, `luna` o `marte`) y ventanas sin enlace | Resolver conflictos de edición (no hay: los registros son inmutables) |
| Recepción idempotente con acuse de recibo | |
| Recomendaciones del médico de vuelo hacia la nave | |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Cola offline (cliente) | PWA: IndexedDB, reintentos, credenciales y copias para consulta sin red |
| Cola de salida | Paquetes priorizados: 1 = crítico, 2 = resumen y registros, 3 = detalle |
| Simulador de enlace | Retardo, ventanas sin enlace y cupo por ciclo (**solo en el nodo nave**) |
| Recepción | Entrega idempotente y acuse de recibo |
| Recomendaciones | Mensajes del médico de vuelo hacia la nave |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). HU-15.01 reúne el encolado de las "partes B" de M01, M02, M07, M11, M12, M13 y M14 y conviene re-estimarla (hoy 2 puntos).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-15.04 La app funciona sin conexión | 2 | Con la red desactivada se registra un PVT, un síntoma y un examen; al volver llegan una sola vez |
| HU-15.01 Encolar por prioridad | 3 | Cada alerta crítica genera un paquete de prioridad 1 |
| HU-15.02 Sincronizar sin pérdidas tras un corte | 4 | **E5 pasa:** tras la ventana, entrega 1 → 2 → 3 sin pérdidas ni duplicados; con `marte` (12 min) nada aparece antes de 12 min |
| HU-15.03 Enviar una recomendación a la nave | 4 | La recomendación llega al CMO tras el retardo y se agrega al caso si responde a una alerta |
