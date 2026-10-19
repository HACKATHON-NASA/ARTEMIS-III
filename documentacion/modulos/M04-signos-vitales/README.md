# M04 · Signos vitales

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M04 |
| **Grupo** | Clínico |
| **Carpeta Laravel** | `astrohealth/app/Modules/SignosVitales/` |
| **Épica** | EP-04 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon |
| **Sprints** | 2 (HU-04.01, HU-04.03) · 3 (HU-04.02) · 4 (HU-04.04, HU-04.05) |
| **Escenarios** | E1 (arritmia en ejercicio), E8 (hipoxemia); E0 (sin falsas alarmas por ejercicio) |
| **Afecciones** | Redistribución de fluidos, intolerancia ortostática, arritmias, estrés térmico ([01 §3.1](../../docs/propuesta/01-analisis-enfermedades.md#31-cardiovascular-y-hematológico)) |

## 2. Propósito

Presentar el **estado cardiovascular y los signos vitales** de cada tripulante: valores actuales, tendencias por hora y por día, presión arterial con prueba ortostática y ventanas de ECG con sus métricas de ritmo. Las lecturas continuas llegan por M03; este módulo las resume, añade las mediciones puntuales y entrega a M12 las métricas que las reglas necesitan.

## 3. Alcance

| Hace | No hace |
|---|---|
| Agregados por hora y por día de FC, SpO₂, FR, temperatura, PA, VFC y actividad | Recibir lecturas de sensores (M03) |
| Registro de la prueba ortostática: de pie en gravedad (antes del vuelo o tras aterrizar) o con LBNP en vuelo | Evaluar umbrales (M12, con los de M16) |
| Recepción de ventanas cortas de ECG y cálculo de métricas a partir de los intervalos RR | Detectar picos R o el intervalo QT sobre la señal cruda (lo hace el dispositivo o el simulador con NeuroKit2, como el procesamiento en el borde de la Fase 2) |
| Vistas de vitales para F1, F2 y F3 | Mostrar el semáforo por sí solo (lo calcula M13 a partir de las alertas) |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Monitoreo continuo | Últimos valores y series de FC, SpO₂, FR y temperatura (desde M03) |
| Presión arterial | Tomas diarias (desde M03) y prueba ortostática con su modalidad (formulario) |
| ECG | Ventanas de hasta 60 s: guarda la señal como archivo y calcula FC media, pausa máxima y variabilidad RR |
| VFC | RMSSD diario (`AH-RMSSD`) como indicador de estrés y fatiga |
| Contexto de actividad | Usa el `contexto` de cada lectura (`reposo`, `ejercicio`, `sueno`) en las gráficas |
| Agregación | Resúmenes por hora y por día, con todas las lecturas y solo en reposo, para las gráficas, las tendencias y la sincronización |
| Detección de irregularidades | Métricas de ritmo que M12 evalúa (escenario E1) |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md) (HU-04.05 no tiene pantalla). HU-04.01 construye también el inicio de F1 (`/astronauta`) y HU-04.02 el de F2 (`/cmo`), que reemplazan las páginas temporales de M01.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-04.01 Ver mis vitales y su tendencia | 2 | F1 muestra valores actuales y la tendencia semanal |
| HU-04.03 Prueba ortostática | 2 | El CMO la registra para un tripulante, con su modalidad (`de_pie` o `lbnp`), y se calcula `normal` o `positiva` |
| HU-04.02 Vitales de la tripulación con semáforo | 3 | F2 muestra las tarjetas con el semáforo de M13 |
| HU-04.04 Ver el ECG de una ventana | 4 | F3 lista las ventanas y muestra la señal con sus métricas; el CMO la ve en F2 desde el caso de arritmia |
| HU-04.05 Irregularidades del ritmo | 4 | **E1 pasa:** la ventana irregular produce la alerta crítica |
