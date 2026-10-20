# app/Shared/Persistencia

| Archivo | Qué hace |
|---|---|
| `Transaccion.php` | Interfaz con `ejecutar(callable, intentos)`. Los servicios abren transacciones con ella, sin usar `DB::` ([ARCHITECTURE §22](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#22-transacciones)) |
| `TransaccionMysql.php` | La implementación con `DB::transaction()` |

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
