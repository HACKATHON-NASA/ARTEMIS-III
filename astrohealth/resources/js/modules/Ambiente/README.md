# M08 · Ambiente · Frontend

> `astrohealth/resources/js/modules/Ambiente/` · [Backend de este módulo](../../../../app/Modules/Ambiente/README.md) · [Ficha de frontend](../../../../../documentacion/modulos/M08-ambiente/frontend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)

Vigilar **la atmósfera de cada módulo de la nave** y relacionarla con la salud de la tripulación. En microgravedad no hay convección natural y el CO₂ se acumula localmente; por encima de 3 mmHg aumentan las cefaleas y los errores en los procedimientos ([Fase 1 §2.11](../../../../../documentacion/docs/investigacion/02-monitorizacion-salud-fase1.md#a-co₂-elevado)). El módulo muestra el pCO₂ por módulo y aporta la regla que cruza el CO₂ alto con las cefaleas reportadas.

| Carpeta | Qué es |
|---|---|
| [`Pages/`](Pages/README.md) | Las **páginas Inertia** de este módulo, con el nombre exacto de la ficha (`<App>/<Recurso>/<Index|Show|Create|Edit>. |
| [`Components/`](Components/README.md) | Los componentes que **solo usa este módulo**. |
| [`Composables/`](Composables/README.md) | La lógica de Vue reutilizable **de este módulo** (`use<Algo>. |
| [`Types/`](Types/README.md) | Los **tipos TypeScript de las *props*** de las páginas de este módulo, copiados de la ficha (`frontend/README. |
| [`Utils/`](Utils/README.md) | Funciones **puras** (sin Vue) que solo usa este módulo. |

Las páginas conservan el nombre de la ficha (`'Admin/Usuarios/Index'`): el resolver de Inertia las busca dentro de `modules/*/Pages/` (se configura en HU-00.01). Un módulo **no importa** nada de otro módulo: solo de las carpetas globales (`components`, `layouts`, `composables`, `lib`, `contratos`, `offline`, `types`).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
