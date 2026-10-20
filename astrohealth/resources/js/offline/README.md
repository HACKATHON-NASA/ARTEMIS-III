# resources/js/offline · Cola sin conexión (M15)

Infraestructura **global**: la usan todas las capturas de F1 (síntomas, tests, sueño…). La especifica [M15](../../../../documentacion/modulos/M15-sincronizacion/frontend/README.md) y se construye en HU-15.04.

| Archivo | Qué hace |
|---|---|
| `db.ts` | Base IndexedDB (Dexie) |
| `cola.ts` | `encolar(endpoint, cuerpo)` y `procesar()` |
| `clienteApi.ts` | Envío a `/api/v1` con el token de la PWA |
| `instantaneas.ts` | Copia local de los datos para ver sin conexión |
| `comprimirImagen.ts` | Compresión de fotos antes de encolarlas (M11) |

Es la **única** parte del frontend que llama a la API.

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
