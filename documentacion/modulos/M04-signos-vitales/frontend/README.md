# M04 · Signos vitales · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Refresco |
|---|---|---|---|---|
| F1 | `/astronauta` | `Astronauta/Inicio` (reemplaza la temporal de M01: aviso de sensores, semáforo desde el Sprint 3 y 4 tarjetas) | `astronauta`, `cmo` (propios) | Sondeo 10 s |
| F1 | `/astronauta/vitales` | `Astronauta/Vitales/Index` | `astronauta`, `cmo` (propios) | Sondeo 10 s |
| F2 | `/cmo` | `Cmo/Tripulacion/Index` (tarjetas con semáforo) | `cmo` | Sondeo 10 s |
| F2 | `/cmo/tripulantes/{codigo}` | `Cmo/Tripulante/Show` | `cmo` | Sondeo 10 s |
| F2 | `/cmo/ortostatica/crear` | `Cmo/Ortostatica/Create` | `cmo` | — |
| F2 | `/cmo/tripulantes/{codigo}/ecg/{ventana}` | `Cmo/Ecg/Show` (mismo componente que F3; lo abre el protocolo `arritmia` de E1) | `cmo` | — |
| F3 | `/tierra/tripulantes/{codigo}/ecg` | `Tierra/Ecg/Index` (ventanas del tripulante con sus métricas) | `medico_vuelo` | — |
| F3 | `/tierra/tripulantes/{codigo}/ecg/{ventana}` | `Tierra/Ecg/Show` | `medico_vuelo` | — |

Las gráficas usan el componente compartido "serie de tiempo con banda de línea base" (ECharts) y marcan los bloques de ejercicio.

## 2. Archivos

```
resources/js/modules/SignosVitales/Pages/Astronauta/Vitales/Index.vue
resources/js/modules/SignosVitales/Pages/Cmo/Tripulacion/Index.vue
resources/js/modules/SignosVitales/Pages/Cmo/Tripulante/Show.vue      # Pestañas: Vitales (M04), Sueño (M06), Exámenes (M07), Síntomas (M11)
resources/js/modules/SignosVitales/Pages/Cmo/Ortostatica/Create.vue
resources/js/modules/SignosVitales/Pages/Cmo/Ecg/Show.vue           # Envoltorio de GraficaEcg para F2
resources/js/modules/SignosVitales/Pages/Tierra/Ecg/{Index,Show}.vue
resources/js/modules/SignosVitales/Components/{GraficaEcg,TarjetaTripulante}.vue
```

## 3. Props de cada página

```ts
type Ultimo = { codigo: string, valor: number, unidad: string, medido_en: string, contexto: string,
                reciente: boolean /* vital_reciente_min (10 min); la PA, pa_reciente_h (24 h) */ } | null

// Astronauta/Inicio
{ estadoSensores: …[] /* M03 */, resumen: Record<'fc' | 'spo2' | 'temp' | 'pas' | 'pad', Ultimo>,
  flechas: Record<string, 'sube' | 'baja' | 'estable' | null>, semaforo: Record<Sistema, Color> | null,
  recordatoriosExamenes: { prueba: string, codigo: string, proxima_fecha: string, estado: 'vencido' | 'hoy' }[] /* M07, HU-07.04 */,
  eventoSolar: { activo: boolean, desde: string, hasta: string | null, dosis_evento_msv: number,
                 dosis_30d_msv: number, referencia_30d: number } | null /* M09, HU-09.02 */,
  mareoPendiente: { diaMision: number, diasTotal: number } | null /* M11, HU-11.02 */,
  mensajesMedico: { id: string, texto: string, autor: string, prioridad: 'normal' | 'urgente',
                    recibida_en: string }[] /* M15, HU-15.03 */ }

// Astronauta/Vitales/Index
{
  ultimos: Record<'fc' | 'spo2' | 'fr' | 'temp' | 'pas' | 'pad' | 'rmssd' | 'actividad', Ultimo>,
  flechas: Record<string, 'sube' | 'baja' | 'estable' | null>,
  series24h: Record<string, { inicio: string, media: number, minimo: number, maximo: number }[]>,  // agregados por hora
  series7d: Record<string, { inicio: string, media: number, minimo: number, maximo: number }[]>,   // agregados por día
  lineaBase: Record<string, { media: number, desviacion: number, fuente: string } | null>,
  bloquesEjercicio: { inicio: string, fin: string }[]
}   // el semáforo está en Astronauta/Inicio (null hasta el Sprint 3)

// Cmo/Tripulacion/Index
{ tripulantes: { codigo: string, nombre: string, semaforo: Record<Sistema, Color>,
                 ultimos: Record<'fc' | 'spo2' | 'temp', Ultimo>, alertasAbiertas: number,
                 nivelMaximo: 'critico' | 'aviso' | null }[] }

// Cmo/Tripulante/Show   (pestaña Vitales; las otras pestañas las definen M06, M07 y M11)
{ tripulante: { codigo: string, nombre: string }, pestana: 'vitales' | 'sueno' | 'examenes' | 'sintomas',
  vitales: { ultimos: …, series7d: …, ortostaticas: { realizada_en: string, modalidad: 'de_pie' | 'lbnp', caida_sistolica: number,
             caida_diastolica: number, resultado: 'normal' | 'positiva' }[],
             ecg: { id: string, inicio: string, contexto: string, fc_media: number | null, cv_rr: number | null,
                    qtc_ms: number | null }[] } }

// Cmo/Ortostatica/Create
{ tripulantes: { id: string, codigo: string }[], modalidades: ['de_pie', 'lbnp'],
  umbrales: { sistolica: number, diastolica: number } /* PA_ORTO_SIS y PA_ORTO_DIA vigentes (M16) */ }

// Tierra/Ecg/Index
{ tripulante: { codigo: string }, ventanas: Paginado<{ id: string, inicio: string, contexto: string,
                 fc_media: number | null, pausa_max_s: number | null, cv_rr: number | null, qtc_ms: number | null }>,
  umbrales: { pausa_s: number, cv_rr: number, qtc_ms: number } }

// Tierra/Ecg/Show  y  Cmo/Ecg/Show
{ ventana: { id: string, inicio: string, contexto: string, duracion_s: number, frecuencia_muestreo_hz: number,
             muestras: number[], picos_r: number[], fc_media: number | null, pausa_max_s: number | null,
             cv_rr: number | null, qtc_ms: number | null }, tripulante: { codigo: string },
  umbrales: { pausa_s: number, cv_rr: number, qtc_ms: number } }
```

| Página | Acciones |
|---|---|
| `Cmo/Ortostatica/Create` | Elegir la modalidad (de pie en gravedad, o LBNP en vuelo), el tripulante, la toma basal y la toma con estímulo; muestra la caída calculada **antes** de enviar; se envía con la cola offline (`/api/v1/signos-vitales/ortostatica`) |
| `Cmo/Tripulante/Show` | Cambiar de pestaña con `router.get(..., { only: [pestaña] })` (carga perezosa) |
| `Tierra/Ecg/Show` | Zoom y desplazamiento en la señal; marca los picos R |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `TarjetaTripulante.vue` | Tarjeta de F2 con código, semáforo por sistema y FC/SpO₂/T actuales |
| `GraficaEcg.vue` | ECharts con la señal (≤ 15.000 muestras) y marcas de picos R; cuadrícula de papel de ECG |
| `TarjetaSigno.vue` (compartido) | Valor actual, unidad, flecha de tendencia y banda de la línea base |

## 5. Comportamiento

- `Astronauta/Vitales/Index` y `Cmo/Tripulacion/Index` se refrescan con `usePoll(10000)` solo con `ultimos`, `semaforo` y `series24h`; `Cmo/Tripulante/Show`, solo con la pestaña abierta (`vitales`, `sueno`…).
- Una tarjeta con `reciente = false` (sin lecturas válidas en los últimos `vital_reciente_min`; en la PA, en `pa_reciente_h`; lo calcula el backend) muestra "Sin datos recientes · última 13:42 UTC" en vez de un valor viejo.
- Sin conexión, F1 muestra la última instantánea con la marca "Datos de hace N min".
- Un valor marcado como artefacto no se muestra como "actual".

## 6. Accesibilidad

- Cada tarjeta lee "Frecuencia cardiaca, 72 latidos por minuto, subiendo, medida hace 1 minuto" (`aria-label`), sin juicio clínico: el estado lo da el semáforo de M13. Bocetos y textos de cada página en las [historias de frontend](HU/README.md).
- La gráfica de ECG tiene un resumen textual (FC media, pausa máxima, QTc) para lectores de pantalla.
