# Http/Controllers/Web · M03 Ingesta de datos

> `astrohealth/app/Modules/Ingesta/Http/Controllers/Web/` · [Ficha de backend de M03](../../../../../../../documentacion/modulos/M03-ingesta/backend/README.md) · [Norma](../../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#11-reglas-httpapi)

Controladores de las **pantallas** (Inertia): reciben la petición, llaman a **un** caso de uso y devuelven `Inertia::render()` o una redirección con *flash*.

| Va aquí | No va aquí |
|---|---|
| `<Recurso>Controller` con métodos de pocas líneas. | Consultas o reglas. |

**Archivos previstos para M03 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../../documentacion/modulos/M03-ingesta/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
