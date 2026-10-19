# M09 · Radiación

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M09 |
| **Grupo** | Clínico (ampliación del MVP) |
| **Carpeta Laravel** | `astrohealth/app/Modules/Radiacion/` |
| **Épica** | EP-09 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela |
| **Sprints** | 2 (HU-09.01) · 3 (HU-09.02) |
| **Escenarios** | E2 (evento solar) |
| **Afecciones** | Exposición acumulada, evento de partículas solares, efectos a largo plazo ([01 §3.4](../../docs/propuesta/01-analisis-enfermedades.md#34-radiación-espacial)) |

## 2. Propósito

Llevar la **dosis de radiación de cada tripulante** frente al límite de carrera de la NASA (600 mSv), proyectarla al final de la misión y dar la **alarma inmediata a toda la tripulación** cuando un evento de partículas solares dispara la tasa de dosis, para que vaya al refugio.

## 3. Alcance

| Hace | No hace |
|---|---|
| Dosis diaria y acumulada (misión y carrera) a partir de la tasa del dosímetro personal | Calcular la dosis por órgano o el riesgo de cáncer |
| Proyección de la dosis de carrera al final de la misión | Pronosticar el clima espacial |
| Regla de evento solar (`ReglaEventoSolar`), ejecutada por M12 | Gestionar el refugio físico |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Dosimetría personal | Dosis diaria, acumulada en la misión y acumulada en la carrera |
| Tasa en tiempo real | Serie `AH-DOSIS-TASA` (mSv/d) y detección de subidas bruscas |
| Proyección | Dosis de carrera estimada al final de la misión |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-09.01 Dosis acumulada frente al límite | 2 | F1 muestra la barra de progreso y la proyección |
| HU-09.02 Alarma de evento solar | 3 | **E2 pasa:** alarma crítica para toda la tripulación en F1 y F2 |
