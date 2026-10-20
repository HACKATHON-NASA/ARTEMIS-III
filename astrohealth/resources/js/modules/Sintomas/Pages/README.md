# Pages · M11 Síntomas (frontend)

> `astrohealth/resources/js/modules/Sintomas/Pages/` · [Ficha de frontend de M11](../../../../../../documentacion/modulos/M11-sintomas/frontend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)

Las **páginas Inertia** de este módulo, con el nombre exacto de la ficha (`<App>/<Recurso>/<Index|Show|Create|Edit>.vue`). El resolver busca cada nombre de página aquí.

| Va aquí | No va aquí |
|---|---|
| Páginas que solo coordinan componentes y reciben *props* tipadas. | Lógica de negocio, `fetch`/`axios` para datos de pantalla. |

**Archivos previstos para M11 en esta carpeta:** los lista la ficha, en [§2 Archivos](../../../../../../documentacion/modulos/M11-sintomas/frontend/README.md#2-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
