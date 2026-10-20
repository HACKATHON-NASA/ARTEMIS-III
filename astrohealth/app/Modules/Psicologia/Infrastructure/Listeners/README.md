# Infrastructure/Listeners · M05 Psicología y cognición

> `astrohealth/app/Modules/Psicologia/Infrastructure/Listeners/` · [Ficha de backend de M05](../../../../../../documentacion/modulos/M05-psicologia/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

Las **reacciones a eventos de otros módulos**. Cada *listener* llama a un caso de uso de `Application`.

| Va aquí | No va aquí |
|---|---|
| *Listeners* (`ShouldQueue` si son pesados), registrados en el *ServiceProvider*. | Lógica esencial escondida: la lógica va en el servicio. |

**Archivos previstos para M05 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M05-psicologia/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
