# M05 · Psicología y cognición

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M05 |
| **Grupo** | Clínico |
| **Carpeta Laravel** | `astrohealth/app/Modules/Psicologia/` |
| **Épica** | EP-05 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela · **Otros:** Miguel (simulación de HU-05.07) |
| **Sprints** | 2 (HU-05.01, HU-05.02, HU-05.07, HU-05.03, HU-05.05) · 3 (HU-05.04) · 4 (HU-05.06) |
| **Escenarios** | E4 (fatiga acumulada) |
| **Afecciones** | Estrés, ansiedad y depresión; deterioro cognitivo y fatiga; conflictos en la tripulación ([01 §3.6](../../docs/propuesta/01-analisis-enfermedades.md#36-salud-mental-cognición-y-conducta)) |

## 2. Propósito

Medir de forma periódica la **atención, la velocidad de cálculo y el estado emocional** de cada tripulante, y **detectar el deterioro cognitivo** comparando cada sesión con la línea base de esa persona. Es el primero de los dos casos de uso centrales del proyecto ([04 §5](../../docs/propuesta/04-manejo-de-datos.md#5-ejemplo-completo-detección-de-deterioro-cognitivo)).

## 3. Alcance

| Hace | No hace |
|---|---|
| Test PVT de 3 minutos, test de aritmética de 20 ítems y test de memoria de secuencias, también sin conexión | Tests cognitivos con licencia (p. ej., la batería *Cognition* de la NASA) |
| Escalas diarias (ánimo, estrés, fatiga) y semanales (carga de trabajo, cohesión) | Instrumentos psicológicos clínicos con licencia (los elegirían profesionales) |
| Cálculo de métricas en el servidor a partir de las respuestas | Confiar en métricas ni en el campo `completa` calculados por el cliente |
| Sesiones del simulador con su `tripulante_id`, para la línea base y E4 | Mostrar al CMO las escalas o la cohesión de sus compañeros |
| Regla de deterioro cognitivo, ejecutada por el motor de M12 | Crear alertas (M13) |
| Recordatorios de tests pendientes en F1 | Notificaciones *push* del sistema operativo |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Tests cognitivos | PVT, aritmética y memoria: presentación, registro de cada respuesta y métricas |
| Seguimiento emocional | Escalas diarias (0–10) y semanales (Likert 1–5) |
| Planificación de evaluaciones | Programación por tripulante y recordatorios en F1 |
| Detección de deterioro | `ReglaDeterioroCognitivo` (implementa `Analitica\Contratos\ReglaAnalitica`) |
| Reportes psicológicos | Evolución individual para el médico de vuelo; confidencial (el CMO solo ve las alertas cognitivas) |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md) (HU-05.04 no tiene pantalla: la alerta la muestra M13).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-05.01 Test PVT | 2 | Sesión de 3 min con mediana, lapsos y falsas salidas; funciona sin conexión |
| HU-05.02 Test de aritmética | 2 | 20 ítems con tiempo medio y precisión |
| HU-05.07 Test de memoria | 2 | Secuencias crecientes con regla de parada; `longitud_max` calculada en el servidor; funciona sin conexión; el simulador genera sesiones de memoria |
| HU-05.03 Ánimo, estrés y fatiga | 2 | Registro en menos de 1 minuto |
| HU-05.05 Recordatorios de tests | 2 | F1 muestra los tests pendientes del día |
| HU-05.04 Deterioro cognitivo sostenido | 3 | **E4 pasa** y E0 no genera avisos: σ mínima de la precisión de 7,5 puntos, σ mínima relativa del 10 % en la mediana del PVT y el tiempo de aritmética, y la pendiente de 7 días solo como dato de la explicación (comprobado con Monte Carlo de todas las reglas: 0,8 % de falsos en E0 y 95 % de detección de E4) |
| HU-05.06 Evolución psicológica | 4 | F3 muestra la evolución de métricas y escalas |
