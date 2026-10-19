# M02 · Misión y tripulación

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M02 |
| **Grupo** | Núcleo |
| **Carpeta Laravel** | `astrohealth/app/Modules/Mision/` |
| **Épica** | EP-02 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela (HU-02.01, HU-02.02) · Jhon (HU-02.03) |
| **Sprints** | 1 (HU-02.01, HU-02.02, HU-02.03) |
| **Escenarios** | Todos (definen el contexto de la misión) |

## 2. Propósito

Describir **la misión y a quiénes monitorea el sistema**: el perfil de la misión (que fija el comportamiento del enlace), los tripulantes con los datos que necesitan las reglas (por ejemplo, el sexo para los rangos de laboratorio), los dispositivos asignados y los módulos de la nave donde están los sensores ambientales.

## 3. Alcance

| Hace | No hace |
|---|---|
| Una misión activa con perfil de enlace (`leo`, `luna`, `marte`) y fechas | Varias misiones activas a la vez |
| Alta de tripulantes y vínculo con su usuario | Historia clínica completa (fuera de alcance) |
| Alta de dispositivos y asignación a un tripulante o a un módulo de la nave | Gestión de firmware o calibración |
| Registro de la línea base previa al vuelo (se guarda en M12) | Cálculo de la línea base de referencia (M12) |
| Cálculo del **día de misión** de cualquier fecha | Planificación de actividades de la tripulación |
| Los mismos `id` en los dos nodos y el envío de los cambios a la Tierra (paquete `mision` de M15, desde el Sprint 3) | Editar la misión o la tripulación desde la Tierra |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Misiones | Nombre, perfil de enlace, fechas, día de misión actual |
| Tripulantes | Código (`T1`…`T6`), nombre, sexo, fecha de nacimiento, rol a bordo, usuario vinculado |
| Dispositivos | Tipo, identificador, asignación, batería y último contacto |
| Módulos de la nave | Ubicaciones de los sensores ambientales (para M08) |
| Línea base previa al vuelo | Formulario del nodo Tierra que envía los valores a `Analitica\Contratos\RegistroLineaBase`; viajan a la nave en el paquete `configuracion` |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo, lista para el *sprint planning*): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). El envío a la Tierra (parte B de HU-02.01, HU-02.02 y HU-02.03) se construye dentro de HU-15.01, en el Sprint 3.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-02.01 Crear misión con perfil de enlace | 1 | La misión de demostración existe con perfil `leo`, empieza 29 días antes de hoy (o en `MISION_INICIO`) y se puede cambiar a `marte` |
| HU-02.02 Registrar tripulantes y dispositivos | 1 | 4 tripulantes vinculados a sus usuarios, con sus dispositivos (incluido el dispositivo `simulador`) y con los mismos `id` en los dos nodos; el token del simulador se emite después, en HU-01.03 |
| HU-02.03 Línea base previa al vuelo | 1 | El médico de vuelo registra en la Tierra la FC en reposo de T2 (61 ± 3 lpm) y queda en `lineas_base` con `fuente = previa_vuelo` y `contexto = reposo`; la llegada a la nave se prueba en el Sprint 4, con la sincronización |
