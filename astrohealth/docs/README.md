# 📘 Documentación técnica del código

> [← AstroHealth](../README.md) · [Plan](../../documentacion/PLAN_ASTROHEALTH.md) · [Fichas de los módulos](../../documentacion/modulos/README.md) · [ARCHITECTURE.md](../../documentacion/arquitectura/ARCHITECTURE.md)

Aquí se documenta **cómo quedó construida** la aplicación, módulo por módulo y capa por capa. Es el complemento de las fichas:

|            | Fichas (`documentacion/modulos/`)                       | Documentación técnica (`astrohealth/docs/`)                                                             |
| ---------- | ------------------------------------------------------- | ------------------------------------------------------------------------------------------------------- |
| Responde   | **Qué** hay que construir                               | **Cómo quedó hecho**                                                                                    |
| Se escribe | Antes de programar                                      | Mientras se programa, en el mismo _pull request_                                                        |
| Contiene   | Datos, rutas, archivos por capa, componentes, criterios | El avance real, las rutas reales, las decisiones técnicas, las diferencias con la ficha y cómo probarlo |

## Organización

```
docs/
├── backend/     # Laravel: la guía archivo por archivo, M0-identidad/ … M8-recordatorios/ y shared.md
├── database/    # MySQL, por módulo: tablas creadas y seeders
├── frontend/    # Vue 3, por módulo: componentes, stores y capturas
└── server/      # Común a toda la app: despliegue, .env, cron, Python y logs
```

## Avance por módulo

|     | Módulo                                  | Fase  | Backend                                  | Base de datos                                   | Frontend                                  |
| :-: | --------------------------------------- | :---: | ---------------------------------------- | ----------------------------------------------- | ----------------------------------------- |
| 🧱  | Shared · piezas comunes                 |   1   | [✅](backend/shared.md)                  | —                                               | —                                         |
| 🔐  | M0 · Identidad y acceso (JWT y roles)   |   1   | [✅](backend/M0-identidad/README.md)     | [✅](database/M0-identidad/README.md)           | [⬜](frontend/M0-identidad/README.md)     |
| 🩺  | M1 · Registro de indicadores            |   2   | [⬜](backend/M1-registro/README.md)      | [⬜](database/M1-registro/README.md)            | [⬜](frontend/M1-registro/README.md)      |
| 🚦  | M2 · Motor de evaluación                |   2   | [⬜](backend/M2-evaluacion/README.md)    | [⬜](database/M2-evaluacion/README.md)          | [⬜](frontend/M2-evaluacion/README.md)    |
| 🧍  | M3 · Cuerpo humano interactivo          |   3   | [⬜](backend/M3-cuerpo/README.md)        | [⬜](database/M3-cuerpo/README.md)              | [⬜](frontend/M3-cuerpo/README.md)        |
| 📈  | M4 · Dashboard                          |   3   | [⬜](backend/M4-dashboard/README.md)     | [— sin tablas](database/M4-dashboard/README.md) | [⬜](frontend/M4-dashboard/README.md)     |
| 🚨  | M5 · Recomendaciones y alertas a Tierra |   4   | [⬜](backend/M5-alertas/README.md)       | [⬜](database/M5-alertas/README.md)             | [⬜](frontend/M5-alertas/README.md)       |
| 🌍  | M6 · Entorno de misión                  | 1 · 4 | [🟨](backend/M6-entorno/README.md)       | [🟨](database/M6-entorno/README.md)             | [⬜](frontend/M6-entorno/README.md)       |
| 🏆  | M7 · Gamificación                       |   5   | [⬜](backend/M7-gamificacion/README.md)  | [⬜](database/M7-gamificacion/README.md)        | [⬜](frontend/M7-gamificacion/README.md)  |
| ⏰  | M8 · Recordatorios                      |   5   | [⬜](backend/M8-recordatorios/README.md) | [⬜](database/M8-recordatorios/README.md)       | [⬜](frontend/M8-recordatorios/README.md) |

**Estados:** ⬜ sin empezar · 🟨 en curso · ✅ terminado (con sus pruebas en verde). Quien termina una parte cambia su casilla aquí y en el índice de su capa.

## Reglas

1. **Se documenta lo real.** No se copia la ficha: se enlaza y se cuenta lo que se construyó y por qué.
2. **En el mismo _pull request_** que el código. Al terminar algo, se marca su casilla en "Avance" y se llenan sus tablas.
3. **Un tema por archivo** cuando un README crece. Por ejemplo, `docs/backend/M2-evaluacion/respaldo-local.md`, con su enlace en el README del módulo.
4. **Si el código se aparta de la ficha**, se anota en "Diferencias con la ficha" y se corrige la ficha en el mismo PR.
5. Los nombres de archivo van en minúscula y con guiones.

> 📝 **Documentar siempre:** un _pull request_ que cambia el código sin actualizar su documento aquí no se aprueba.
