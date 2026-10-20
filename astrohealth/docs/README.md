# Documentación técnica del código

Aquí se documenta **cómo quedó construida** la aplicación, capa por capa y módulo por módulo. Es el complemento de las fichas:

| | Fichas (`documentacion/modulos/`) | Documentación técnica (`astrohealth/docs/`) |
|---|---|---|
| Responde | **Qué** hay que construir | **Cómo quedó hecho** |
| Se escribe | Antes de programar | Mientras se programa, en el mismo *pull request* |
| Contiene | Reglas, tablas, rutas, pantallas, criterios | Lo implementado de verdad, decisiones técnicas, diferencias con la ficha, cómo probarlo |

## Organización

```
docs/
├── backend/        # Laravel, por módulo: M01-identidad/ … M16-configuracion/
├── frontend/       # Vue + Inertia, por módulo
├── database/       # MySQL, por módulo: tablas, migraciones, seeders
└── server/         # Común a toda la app: despliegue en cPanel, .env por nodo, cron, colas, CI, logs
```

| Carpeta | Índice |
|---|---|
| [`backend/`](backend/README.md) | Un `README.md` por módulo con: historias implementadas, rutas y endpoints reales, casos de uso, códigos de respuesta, decisiones, diferencias con la ficha y cómo probarlo |
| [`frontend/`](frontend/README.md) | Páginas y sus *props* reales, componentes, decisiones, diferencias con la ficha |
| [`database/`](database/README.md) | Tablas creadas, índices, *seeders*, decisiones |
| [`server/`](server/README.md) | Despliegue y operación del servidor |

## Reglas

1. **Se documenta lo real.** No se copia la ficha: se enlaza y se cuenta lo que se construyó y por qué.
2. **En el mismo *pull request*** que el código. Al terminar una historia, su módulo en `docs/` dice que está implementada y cómo.
3. **Un tema por archivo** cuando un `README.md` crece: `docs/backend/M03-ingesta/idempotencia-de-lotes.md`, con su enlace en el README del módulo.
4. **Si el código se aparta de la ficha**, se anota en "Diferencias con la ficha" y se corrige la ficha (con su línea en el [CHANGELOG](../../documentacion/CHANGELOG.md)).
5. Nombres de archivo en minúscula y con guiones.

> 📝 **Documentar siempre:** un *pull request* que cambia el código sin actualizar su documento aquí no se aprueba.
