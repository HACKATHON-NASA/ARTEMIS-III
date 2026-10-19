# M01 · Identidad y acceso

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M01 |
| **Grupo** | Núcleo |
| **Carpeta Laravel** | `astrohealth/app/Modules/Identidad/` |
| **Épica** | EP-01 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela (HU-01.01 a HU-01.03) · Jhon (HU-01.04) |
| **Sprints** | 0 (HU-01.01, HU-01.02) · 1 (HU-01.03, HU-01.04) |
| **Escenarios** | Todos (sin sesión no se accede a nada) |

## 2. Propósito

Controlar **quién entra y qué puede hacer**. Autentica a las personas (sesión web) y a las máquinas (tokens del simulador, de los dispositivos y del otro nodo), asigna un rol a cada usuario y deja un registro inmutable de los accesos a datos de salud.

## 3. Alcance

| Hace | No hace |
|---|---|
| Inicio y cierre de sesión con correo y contraseña, solo para los roles que trabajan en ese nodo | Registro libre de usuarios (los crea el administrador; la ruta de registro se borra) |
| Un rol por usuario, con permisos por *Policy* | Permisos por campo o por tripulante individual (se resuelve en cada módulo con su *Policy*) |
| Emisión y revocación de tokens de Sanctum con habilidades limitadas | Autenticación con proveedores externos (Keycloak, OAuth) — evolución |
| Registro de auditoría de lecturas, cambios y exportaciones | Análisis de la auditoría (solo consulta) |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Autenticación | Sesión web del *starter kit* (Inertia) con expiración por inactividad de 8 h |
| Roles y permisos | Columna `rol` en `users`; **lista completa de *Gates*** del sistema ([backend §5](backend/README.md#5-permisos)), que recoge la matriz de [02 §7](../../docs/propuesta/02-modulos-del-sistema.md#7-roles-y-permisos) y los permisos de cada ficha |
| Tokens | Tokens de Sanctum para dispositivos, para la cola offline de la PWA y para el nodo remoto; las escrituras en la API exigen token (no aceptan sesión) |
| Auditoría | Tabla de solo inserción (protegida con *triggers*) con cada acceso a datos de salud (sin contar el sondeo); la de la nave se envía a la Tierra por horas desde el Sprint 3 |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (especificación detallada de cada historia, lista para el *sprint planning*): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). Incluyen el trabajo de M01 dentro de HU-15.04 (token de la PWA).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-01.01 Inicio de sesión | 0 | Los 6 usuarios de prueba entran en su nodo y llegan al inicio de su rol (páginas temporales en el Sprint 0); un usuario inactivo no entra |
| HU-01.02 Asignar roles | 0 | El admin cambia el rol de un usuario y el menú cambia en la siguiente visita |
| HU-01.03 Tokens de dispositivo | 1 | Va **después** de HU-02.02, que crea los dispositivos. El simulador escribe con su token; con un token revocado recibe `401`; con una sesión de navegador no se puede escribir |
| HU-01.04 Auditoría | 1 | Va **después** de HU-02.02 (usa los tripulantes) y antes de HU-01.03. Ver los datos de otro tripulante deja una entrada `ver` (el sondeo no cuenta). El envío a la Tierra (parte B) se hace dentro de HU-15.01 en el Sprint 3, porque necesita la cola de M15 |
