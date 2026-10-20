# M01 · Identidad y acceso · Backend

> `astrohealth/app/Modules/Identidad/` · Espacio de nombres `App\Modules\Identidad` · [Frontend de este módulo](../../../resources/js/modules/Identidad/README.md) · [Estructura de carpetas](../../../../documentacion/modulos/00-arquitectura/10-estructura-de-carpetas.md) · [Norma](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md)

Controlar **quién entra y qué puede hacer**. Autentica a las personas (sesión web) y a las máquinas (tokens del simulador, de los dispositivos y del otro nodo), asigna un rol a cada usuario y deja un registro inmutable de los accesos a datos de salud.

**Especificación** (qué construir): [ficha](../../../../documentacion/modulos/M01-identidad/README.md) · [backend](../../../../documentacion/modulos/M01-identidad/backend/README.md) · [base de datos](../../../../documentacion/modulos/M01-identidad/database/README.md) · [frontend](../../../../documentacion/modulos/M01-identidad/frontend/README.md).

## Capas de este módulo

| Carpeta | Qué es |
|---|---|
| `IdentidadServiceProvider.php` y `routes.php` | Se crean en HU-00.01: registran rutas, migraciones, contratos y *listeners* del módulo |
| [`Contratos/`](Contratos/README.md) | Las **interfaces** (y los DTOs que devuelven) que este módulo **ofrece a los demás módulos**. |
| [`Domain/`](Domain/README.md) | Las **reglas del negocio puras**: lo que el módulo *es*, sin base de datos ni HTTP. |
| [`Application/`](Application/README.md) | Los **casos de uso**: una clase por acción (`<Verbo><Cosa>`), con un único método público `ejecutar()`. |
| [`Infrastructure/`](Infrastructure/README.md) | La **única** parte del módulo que toca la base de datos, y los adaptadores hacia afuera (*listeners*, importador de sincronización). |
| [`Http/`](Http/README.md) | La **entrada y salida HTTP** del módulo: controladores delgados, validación de entrada y forma de las respuestas. |
| [`Policies/`](Policies/README.md) | La **autorización sobre recursos concretos** (por ejemplo, "solo sus propios dispositivos"). |
| [`Console/`](Console/README.md) | Los **comandos de Artisan** del módulo (`<modulo>:<accion>`). |
| [`Database/`](Database/README.md) | Las **migraciones**, *seeders* y *factories* de las tablas de este módulo, en sus subcarpetas. |
| [`Tests/`](Tests/README.md) | Las **pruebas** del módulo: cada criterio *Dado / Cuando / Entonces* de sus historias es una prueba, escrita **antes** del código. |

Cada carpeta tiene su README con lo que va y lo que no va.

## Ejemplo: qué crea cada historia de M01

| Historia | Backend (esta carpeta) | Frontend |
|---|---|---|
| HU-01.01 Inicio de sesión | `php artisan install:features` (sin funciones extra del kit), `Application/AutenticarUsuario`, `Http/Requests/LoginRequest`, `Domain/Enums/Rol`, `Http/Middleware/{UsuarioActivo,RolPermitidoEnNodo,RedirigirSegunRol}`, migración `add_rol_activo_to_users`, *seeders* de usuarios | `Pages/Auth/Login.vue` y las páginas temporales de inicio |
| HU-01.02 Asignación de roles | `Policies/GatesIdentidad`, `Domain/Reglas/ReglasAdministrador`, `Application/{GuardarUsuario,DesactivarUsuario}`, `Http/Controllers/Web/UsuarioController`, `Http/Requests/GuardarUsuarioRequest`, repositorios de usuario | `Pages/Admin/Usuarios/Index.vue` y sus componentes |
| HU-01.03 Tokens de dispositivo | `Application/{EmitirTokenDispositivo,RevocarToken}`, `Http/Middleware/TokenDeTipo`, `Policies/TokenPolicy`, `Console/TokenNodoCommand` | `Pages/Admin/Tokens/Index.vue`, `Components/TokenUnaVez.vue` |
| HU-01.04 Auditoría (parte A) | `Contratos/RegistroAuditoria`, `Application/RegistrarAuditoria`, `Infrastructure/{Models/Auditoria,Repositorios,Listeners}`, migración `create_auditoria`, `Http/Controllers/Web/AuditoriaController` | `Pages/Admin/Auditoria/Index.vue` |

Recorrido de una petición (crear un usuario): [ARCHITECTURE §27, ejemplo 1](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#27-ejemplos-de-flujo).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
