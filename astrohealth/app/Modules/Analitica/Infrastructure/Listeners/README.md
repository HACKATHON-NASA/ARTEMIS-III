# Infrastructure/Listeners · M12 Analítica y línea base

> `astrohealth/app/Modules/Analitica/Infrastructure/Listeners/` · [Ficha de backend de M12](../../../../../../documentacion/modulos/M12-analitica/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

Las **reacciones a eventos de otros módulos**. Cada *listener* llama a un caso de uso de `Application`.

| Va aquí | No va aquí |
|---|---|
| *Listeners* (`ShouldQueue` si son pesados), registrados en el *ServiceProvider*. | Lógica esencial escondida: la lógica va en el servicio. |

**Archivos previstos para M12 en esta carpeta:** los lista la ficha, en [§6 Archivos](../../../../../../documentacion/modulos/M12-analitica/backend/README.md#6-archivos).

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
