# M02 · Misión y tripulación · Frontend

> `astrohealth/resources/js/modules/Mision/` · [Backend de este módulo](../../../../app/Modules/Mision/README.md) · [Ficha de frontend](../../../../../documentacion/modulos/M02-mision/frontend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)

Describir **la misión y a quiénes monitorea el sistema**: el perfil de la misión (que fija el comportamiento del enlace), los tripulantes con los datos que necesitan las reglas (por ejemplo, el sexo para los rangos de laboratorio), los dispositivos asignados y los módulos de la nave donde están los sensores ambientales.

| Carpeta | Qué es |
|---|---|
| [`Pages/`](Pages/README.md) | Las **páginas Inertia** de este módulo, con el nombre exacto de la ficha (`<App>/<Recurso>/<Index|Show|Create|Edit>. |
| [`Components/`](Components/README.md) | Los componentes que **solo usa este módulo**. |
| [`Composables/`](Composables/README.md) | La lógica de Vue reutilizable **de este módulo** (`use<Algo>. |
| [`Types/`](Types/README.md) | Los **tipos TypeScript de las *props*** de las páginas de este módulo, copiados de la ficha (`frontend/README. |
| [`Utils/`](Utils/README.md) | Funciones **puras** (sin Vue) que solo usa este módulo. |

Las páginas conservan el nombre de la ficha (`'Admin/Usuarios/Index'`): el resolver de Inertia las busca dentro de `modules/*/Pages/` (se configura en HU-00.01). Un módulo **no importa** nada de otro módulo: solo de las carpetas globales (`components`, `layouts`, `composables`, `lib`, `contratos`, `offline`, `types`).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
