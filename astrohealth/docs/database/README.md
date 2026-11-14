# Documentación técnica · Base de datos (MySQL)

> [← Documentación técnica](../README.md)

Las tablas que de verdad se crearon, sus índices y sus *seeders*. El diseño completo está en [Base de datos](../../../documentacion/arquitectura/02-base-de-datos.md).

| | Carpeta | Módulo | Fase | Estado |
|:---:|---|---|:---:|:---:|
| 🔐 | [`M0-identidad/`](M0-identidad/README.md) | Identidad y acceso | 1 | ⬜ |
| 🩺 | [`M1-registro/`](M1-registro/README.md) | Registro de indicadores | 2 | ⬜ |
| 🚦 | [`M2-evaluacion/`](M2-evaluacion/README.md) | Motor de evaluación | 2 | ⬜ |
| 🧍 | [`M3-cuerpo/`](M3-cuerpo/README.md) | Cuerpo humano interactivo | 3 | ⬜ |
| 📈 | [`M4-dashboard/`](M4-dashboard/README.md) | Dashboard | 3 | ⬜ |
| 🚨 | [`M5-alertas/`](M5-alertas/README.md) | Recomendaciones y alertas a Tierra | 4 | ⬜ |
| 🌍 | [`M6-entorno/`](M6-entorno/README.md) | Entorno de misión | 1 · 4 | ⬜ |
| 🏆 | [`M7-gamificacion/`](M7-gamificacion/README.md) | Gamificación | 5 | ⬜ |
| ⏰ | [`M8-recordatorios/`](M8-recordatorios/README.md) | Recordatorios | 5 | ⬜ |

**Estados:** ⬜ sin empezar · 🟨 en curso · ✅ terminado (con sus pruebas en verde).

```powershell
php artisan migrate:fresh --seed
# y el simulador enviando los lotes: ver backend/M1-registro (ingesta)
```

> 📝 **Documentar siempre:** este documento se actualiza en el mismo *pull request* que cambia el código.
