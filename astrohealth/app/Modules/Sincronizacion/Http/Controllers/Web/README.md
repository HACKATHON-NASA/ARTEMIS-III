# Http/Controllers/Web · M15 Sincronización

> `astrohealth/app/Modules/Sincronizacion/Http/Controllers/Web/` · [Ficha de backend de M15](../../../../../../../documentacion/modulos/M15-sincronizacion/backend/README.md) · [Norma](../../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

Controladores de las **pantallas** (Inertia): reciben la petición, llaman a **un** caso de uso y devuelven `Inertia::render()` o una redirección con *flash*.

| Va aquí | No va aquí |
|---|---|
| `<Recurso>Controller` con métodos de pocas líneas. | Consultas o reglas. |

**Archivos previstos para M15 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../../documentacion/modulos/M15-sincronizacion/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
