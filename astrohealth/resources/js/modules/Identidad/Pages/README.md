# Pages · M01 Identidad y acceso (frontend)

> `astrohealth/resources/js/modules/Identidad/Pages/` · [Ficha de frontend de M01](../../../../../../documentacion/modulos/M01-identidad/frontend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)

Las **páginas Inertia** de este módulo, con el nombre exacto de la ficha (`<App>/<Recurso>/<Index|Show|Create|Edit>.vue`). El resolver busca cada nombre de página aquí.

| Va aquí | No va aquí |
|---|---|
| Páginas que solo coordinan componentes y reciben *props* tipadas. | Lógica de negocio, `fetch`/`axios` para datos de pantalla. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `Auth/Login.vue` | Inicio de sesión (la del kit, adaptada; pasa aquí desde `resources/js/pages/auth`) | HU-01.01 |
| `Auth/ForgotPassword.vue` · `Auth/ResetPassword.vue` | Del kit; solo con correo configurado | HU-01.01 |
| `Settings/Security.vue` · `Settings/Appearance.vue` | Del kit: cambio de contraseña y apariencia | HU-01.01 |
| `Admin/Usuarios/Index.vue` | Usuarios y roles | HU-01.02 |
| `Admin/Tokens/Index.vue` | Tokens de dispositivo | HU-01.03 |
| `Admin/Auditoria/Index.vue` | Auditoría de accesos | HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
