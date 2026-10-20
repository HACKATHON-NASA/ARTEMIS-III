# app/Shared/Excepciones

| Archivo | Qué hace |
|---|---|
| `ErrorDeNegocio.php` | Clase base de los errores de negocio, con código funcional y HTTP. El manejador común de `bootstrap/app.php` la convierte en el sobre de error ([ARCHITECTURE §13](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#13-manejo-de-errores-y-códigos-funcionales)) |

Las excepciones concretas viven en `Domain/Excepciones` de cada módulo.

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
