# M13 · Alertas y casos clínicos · Frontend

> `astrohealth/resources/js/modules/Alertas/` · [Backend de este módulo](../../../../app/Modules/Alertas/README.md) · [Ficha de frontend](../../../../../documentacion/modulos/M13-alertas/frontend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)

Convertir los hallazgos en **alertas que las personas atienden**: las crea, evita duplicados, las prioriza, avisa a quien corresponde (con alarma sonora si es crítica), pide confirmación de los avisos, guía al CMO con protocolos paso a paso y aprende de los falsos positivos. También calcula el **semáforo** de cada tripulante.

| Carpeta | Qué es |
|---|---|
| [`Pages/`](Pages/README.md) | Las **páginas Inertia** de este módulo, con el nombre exacto de la ficha (`<App>/<Recurso>/<Index|Show|Create|Edit>. |
| [`Components/`](Components/README.md) | Los componentes que **solo usa este módulo**. |
| [`Composables/`](Composables/README.md) | La lógica de Vue reutilizable **de este módulo** (`use<Algo>. |
| [`Types/`](Types/README.md) | Los **tipos TypeScript de las *props*** de las páginas de este módulo, copiados de la ficha (`frontend/README. |
| [`Utils/`](Utils/README.md) | Funciones **puras** (sin Vue) que solo usa este módulo. |

Las páginas conservan el nombre de la ficha (`'Admin/Usuarios/Index'`): el resolver de Inertia las busca dentro de `modules/*/Pages/` (se configura en HU-00.01). Un módulo **no importa** nada de otro módulo: solo de las carpetas globales (`components`, `layouts`, `composables`, `lib`, `contratos`, `offline`, `types`).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
