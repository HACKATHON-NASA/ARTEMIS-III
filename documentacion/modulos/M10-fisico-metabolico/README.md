# M10 · Físico y metabólico

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M10 |
| **Grupo** | Clínico (ampliación del MVP) |
| **Carpeta Laravel** | `astrohealth/app/Modules/FisicoMetabolico/` |
| **Épica** | EP-10 (prioridad *Could*: primera en recortarse si hay retraso) |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon · **Otros:** Miguel (simulación de HU-10.03 a HU-10.05) |
| **Sprints** | 2 (HU-10.01, HU-10.03, HU-10.04) · 3 (HU-10.02, HU-10.05) |
| **Escenarios** | Ninguno específico; aporta el contexto de ejercicio |
| **Afecciones** | Pérdida ósea, atrofia muscular, déficit calórico y pérdida de masa ([01 §3.2](../../docs/propuesta/01-analisis-enfermedades.md#32-musculoesquelético-y-renal) y [§3.8](../../docs/propuesta/01-analisis-enfermedades.md#38-nutrición-metabolismo-e-hidratación)) |

## 2. Propósito

Registrar **las sesiones de ejercicio**, que son la principal contramedida contra la pérdida ósea y muscular; seguir la **masa corporal** y la **fuerza de prensión** semana a semana; registrar la **ingesta y la hidratación**, y vigilar la **glucosa** del monitor continuo ([Fase 1 §2.9](../../docs/investigacion/02-monitorizacion-salud-fase1.md#29-nutrición-metabolismo-e-hidratación)).

## 3. Alcance

| Hace | No hace |
|---|---|
| Registro de sesiones de ejercicio (ARED, cinta, cicloergómetro) | Estimar la densidad ósea |
| Tendencia semanal de la masa corporal | Prescribir planes de ejercicio o dietas |
| Cumplimiento de ejercicio semanal | Fijar metas de calorías o de líquidos (no hay valores con fuente) |
| Registro de comidas y líquidos con totales diarios (HU-10.03) | Alertar por la ingesta o por la fuerza |
| Fuerza de prensión con dinamómetro (HU-10.04) | |
| Serie de glucosa del CGM y su umbral (HU-10.05) | |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Ejercicio | Sesiones con equipo, duración, carga y FC |
| Composición corporal | Masa corporal semanal (`29463-7`) desde el dispositivo `medidor_masa` del tripulante (`masa-T<n>`, M02), por lote de M03, y fuerza de prensión |
| Nutrición e hidratación | Comidas (kcal y proteína) y líquidos (mL); totales del día |
| Glucosa | Serie `AH-GLUCOSA` del CGM (vía M03); regla `GLUCOSA` evaluada por M12 |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md). La masa corporal llega desde el dispositivo nuevo `medidor_masa` (`masa-T<n>`, M02).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-10.01 Registrar sesiones de ejercicio | 2 | Registro manual o del simulador; minutos semanales en F1 |
| HU-10.03 Registrar nutrición e hidratación | 2 | Comidas y líquidos sin conexión; totales del día; el simulador genera 3 comidas y 4 líquidos diarios |
| HU-10.04 Registrar la fuerza de prensión | 2 | Ambas manos sin conexión; cambio frente a la primera medición; el simulador mide los lunes |
| HU-10.02 Tendencia de la masa corporal | 3 | F3 muestra la masa semanal y su pendiente; pérdida > 5 % → aviso |
| HU-10.05 Glucosa del monitor continuo | 3 | El simulador envía la glucosa cada 5 min; F3 la grafica; < 70 o > 180 en 2 lecturas → aviso; E0 sigue sin avisos |
