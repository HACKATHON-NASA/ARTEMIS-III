# app/Shared · Lo común a todos los módulos

Piezas **técnicas** que usan todos los módulos. **Nunca** reglas de negocio ni nada de un módulo concreto. Se construyen en [HU-00.01](../../../documentacion/scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md).

| Carpeta o archivo | Qué contiene |
|---|---|
| `ModuloServiceProvider.php` | Clase base de los *providers* de los módulos: carga `routes.php` y `Database/Migrations` |
| [`Http/`](Http/README.md) | `RespuestaApi` (sobre JSON), `CodigoGeneral` (códigos comunes) y el middleware común |
| [`Excepciones/`](Excepciones/README.md) | `ErrorDeNegocio`, la clase base de los errores de negocio |
| [`Persistencia/`](Persistencia/README.md) | `Transaccion` y `TransaccionMysql` |

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
