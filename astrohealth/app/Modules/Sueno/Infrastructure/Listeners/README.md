# Infrastructure/Listeners · M06 Sueño

> `astrohealth/app/Modules/Sueno/Infrastructure/Listeners/` · [Ficha de backend de M06](../../../../../../documentacion/modulos/M06-sueno/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

Las **reacciones a eventos de otros módulos**. Cada *listener* llama a un caso de uso de `Application`.

| Va aquí | No va aquí |
|---|---|
| *Listeners* (`ShouldQueue` si son pesados), registrados en el *ServiceProvider*. | Lógica esencial escondida: la lógica va en el servicio. |

**Archivos previstos para M06 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M06-sueno/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
