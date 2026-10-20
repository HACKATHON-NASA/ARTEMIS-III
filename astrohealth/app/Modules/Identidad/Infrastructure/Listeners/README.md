# Infrastructure/Listeners · M01 Identidad y acceso

> `astrohealth/app/Modules/Identidad/Infrastructure/Listeners/` · [Ficha de backend de M01](../../../../../../documentacion/modulos/M01-identidad/backend/README.md) · [Norma](../../../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#23-comunicación-entre-módulos-y-eventos)

Las **reacciones a eventos de otros módulos**. Cada *listener* llama a un caso de uso de `Application`.

| Va aquí | No va aquí |
|---|---|
| *Listeners* (`ShouldQueue` si son pesados), registrados en el *ServiceProvider*. | Lógica esencial escondida: la lógica va en el servicio. |

## Archivos previstos (según la ficha)

| Archivo | Qué hace | Historia |
|---|---|---|
| `AuditarLogin.php` · `AuditarLogout.php` · `AuditarLoginFallido.php` | Eventos de autenticación de Laravel → auditoría | HU-01.04 |
| `AuditarUmbralModificado.php` | `UmbralModificado` de M16 → auditoría (ignora los importados) | HU-01.04 |

> 📝 **Documentar siempre:** si añade aquí un tipo de archivo nuevo o cambia lo que hace esta carpeta, actualice este README, la ficha del módulo y el CHANGELOG en el mismo *pull request*.
