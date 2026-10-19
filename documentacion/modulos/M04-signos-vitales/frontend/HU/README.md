# M04 · Signos vitales · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de M04: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hace **Jhon**. HU-04.05 no tiene pantalla propia (la alerta y el caso son de M13). Las *props* vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se siguen la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones) de M01. Textos para el astronauta **de tú**.
- **Componentes compartidos de HU-00.03:** `TarjetaSigno`, `Semaforo`, `GraficaSerie` (ECharts con banda de línea base y líneas de umbral), `EstadoVacio` y `Cargando`. M04 no crea otros equivalentes.
- **Las tarjetas no dicen "normal" ni "anormal".** El estado clínico lo da el semáforo de M13, no M04.
- **Fechas:** `DiaMision` (M02) + hora UTC ("D12 · 14:05 UTC").

**Nombre, unidad y decimales de cada código** (`resources/js/contratos/vitales.ts`). La unidad se muestra **según el código**, no según el UCUM: `/min` es "lpm" en la FC pero "rpm" en la FR. La [convención del índice de módulos](../../../README.md#convenciones-del-frontend) se corrige en el mismo sentido.

| Código | Nombre en pantalla | Unidad | Decimales |
|---|---|---|:---:|
| `8867-4` | Frecuencia cardiaca | lpm | 0 |
| `59408-5` | Saturación de oxígeno (SpO₂) | % | 0 |
| `9279-1` | Frecuencia respiratoria | rpm | 0 |
| `8310-5` | Temperatura | °C | 1 |
| `8480-6` · `8462-4` | Presión arterial (se muestran juntas: "118/76") | mmHg | 0 |
| `AH-RMSSD` | Variabilidad cardiaca (RMSSD) | ms | 0 |
| `AH-ACTIVIDAD` | Actividad | cuentas/min | 0 |

**Nombre de los sistemas del semáforo:** `corazon` Corazón · `sueno` Sueño · `mente` Mente · `radiacion` Radiación · `cuerpo` Cuerpo · `ambiente` Ambiente. Colores e iconos según [M13 §3.6](../../../M13-alertas/backend/README.md#36-semáforo-lo-usan-f1-f2-y-hu-0402): ✖ "Crítico", ⚠ "Atención", ✔ "Normal".


## Historias

| Historia | Archivo |
|---|---|
| HU-04.01 · Ver mis signos vitales actuales y su tendencia | [HU-04.01-ver-mis-signos-vitales-actuales-y-su-tendencia.md](HU-04.01-ver-mis-signos-vitales-actuales-y-su-tendencia.md) |
| HU-04.03 · Registrar una prueba ortostática | [HU-04.03-registrar-una-prueba-ortostatica.md](HU-04.03-registrar-una-prueba-ortostatica.md) |
| HU-04.02 · Signos vitales de toda la tripulación con semáforo | [HU-04.02-signos-vitales-de-toda-la-tripulacion-con-semaforo.md](HU-04.02-signos-vitales-de-toda-la-tripulacion-con-semaforo.md) |
| HU-04.04 · Ver el ECG de una ventana de tiempo | [HU-04.04-ver-el-ecg-de-una-ventana-de-tiempo.md](HU-04.04-ver-el-ecg-de-una-ventana-de-tiempo.md) |
