# Application (= Service: casos de uso) · M15 Sincronización

> `astrohealth/app/Modules/Sincronizacion/Application/` · [Ficha de backend de M15](../../../../../documentacion/modulos/M15-sincronizacion/backend/README.md) · [Norma](../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#9-reglas-de-service)

Los **casos de uso**: una clase por acción (`<Verbo><Cosa>`), con un único método público `ejecutar()`. Coordina repositorios, reglas del dominio, contratos de otros módulos, la transacción y los eventos.

| Va aquí | No va aquí |
|---|---|
| Servicios como `RegistrarSintoma`; la subcarpeta `DTOs`. | SQL, `DB::`, Query Builder, `Request`, respuestas HTTP o páginas. |

**Archivos previstos para M15 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../documentacion/modulos/M15-sincronizacion/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
