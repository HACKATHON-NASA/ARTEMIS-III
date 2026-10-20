# Components · M01 Identidad y acceso (frontend)

> `astrohealth/resources/js/modules/Identidad/Components/` · [Ficha de frontend de M01](../../../../../../documentacion/modulos/M01-identidad/frontend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)

Los componentes que **solo usa este módulo**. Una responsabilidad por componente (filtros, tabla, formulario, diálogo…).

| Va aquí | No va aquí |
|---|---|
| `PascalCase.vue`, menos de 300 líneas. | Componentes que use otro módulo (se mueven a `resources/js/components/`); botones o campos propios (se usan los de `components/ui`). |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `UsuariosFiltros.vue` · `UsuariosTabla.vue` · `UsuarioPanel.vue` · `DialogoDesactivarUsuario.vue` | Piezas de la pantalla de usuarios | HU-01.02 |
| `TokenUnaVez.vue` | Caja del token nuevo con "Copiar" | HU-01.03 |
| `AuditoriaFiltros.vue` · `AuditoriaTabla.vue` | Piezas de la pantalla de auditoría | HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
