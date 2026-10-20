# Components · M15 Sincronización (frontend)

> `astrohealth/resources/js/modules/Sincronizacion/Components/` · [Ficha de frontend de M15](../../../../../../documentacion/modulos/M15-sincronizacion/frontend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)

Los componentes que **solo usa este módulo**. Una responsabilidad por componente (filtros, tabla, formulario, diálogo…).

| Va aquí | No va aquí |
|---|---|
| `PascalCase.vue`, menos de 300 líneas. | Componentes que use otro módulo (se mueven a `resources/js/components/`); botones o campos propios (se usan los de `components/ui`). |

**Archivos previstos para M15 en esta carpeta:** los lista la ficha, en [§2 Archivos](../../../../../../documentacion/modulos/M15-sincronizacion/frontend/README.md#2-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
