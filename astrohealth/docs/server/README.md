# Documentación técnica · Servidor

> [← Documentación técnica](../README.md) · [Checklist de entrega del plan](../../../documentacion/PLAN_ASTROHEALTH.md#8-checklist-final-de-entrega)

Todo lo del **servidor**, común a toda la aplicación. No va por módulo. El plan pide la app **desplegada con una URL pública** (fase 7).

| Documento | Qué tendrá | Fase | Estado |
|---|---|:---:|:---:|
| `despliegue.md` | Paso a paso para publicar Laravel + el *build* de Vite, y el servicio de Python, con una URL pública | 7 | ⬜ |
| `variables-de-entorno.md` | El `.env` de producción de la app y del simulador (sin valores secretos) | 1 · 7 | ⬜ |
| `cron-y-colas.md` | `schedule:run` cada minuto (`reminders:mark-due` de M8) y la cola `database` | 5 | ⬜ |
| `servicio-python.md` | Cómo se arranca y se mantiene vivo el simulador (`uvicorn`, puerto 8001) y qué pasa si se cae (respaldo local de M2) | 2 · 7 | ⬜ |
| `logs-y-errores.md` | Dónde están los *logs*, qué es `evaluation.fallback` y cómo leer un error de la API | 2 | ⬜ |

## Variables que la app necesita (resumen)

| Variable | Ejemplo en local | Para qué |
|---|---|---|
| `APP_URL` | `http://localhost:8000` | |
| `DB_*` | `astrohealth`, `root` | MySQL |
| `SESSION_LIFETIME` | `480` | |
| `QUEUE_CONNECTION` | `database` | |
| `SIMULADOR_URL` | `http://localhost:8001/api/v1` | Python (M2) |
| `SIMULADOR_TIMEOUT` | `3` | Segundos antes de pasar al respaldo local |
| `JWT_SECRET` | `php artisan identidad:jwt-secret` | Firma de los tokens (M0). **Distinto** en cada entorno |
| `JWT_TTL` · `JWT_REFRESH_TTL` | `15` · `10080` | Minutos de vida del *access* y del *refresh* |
| `SIMULADOR_CLIENT_ID` · `SIMULADOR_CLIENT_SECRET` | `sim-ares-1` · … | Credenciales del dispositivo del simulador (`DeviceSeeder`) |

> El simulador tiene su propio `.env` con la `NASA_API_KEY` (`simulador/.env`). **Nunca** se sube al repositorio.

## Antes de presentar

- [ ] La URL pública abre y permite iniciar sesión con un astronauta de prueba
- [ ] `php artisan migrate:fresh --seed` en producción y el simulador enviando lotes con su dispositivo (`identidad:device-create`)
- [ ] `JWT_SECRET` propio de producción (`php artisan identidad:jwt-secret`) y la cookie del *refresh* con `Secure`
- [ ] Python en marcha. Si no, la app funciona igual con el respaldo local
- [ ] `APP_DEBUG=false` y `APP_ENV=production`
- [ ] El aviso *"Herramienta de apoyo, no reemplaza el criterio médico"* se ve en la app

> 📝 **Documentar siempre:** este documento se actualiza en el mismo *pull request* que cambia el código.
