# M09 · Radiación · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Refresco |
|---|---|---|---|---|
| F1 | `/astronauta/radiacion` | `Astronauta/Radiacion/Index` (barra de progreso frente a `limite_msv`, hoy 600 mSv; proyección y tasa actual) | `astronauta`, `cmo` | Sondeo 10 s |
| F3 | `/tierra/radiacion` | `Tierra/Radiacion/Index` (tripulación completa) | `medico_vuelo` | — |

La alarma de evento solar no es una página de M09: es la alerta crítica global que M13 muestra como **ventana modal con sonido** en F1 y F2.

## 2. Archivos

```
resources/js/modules/Radiacion/Pages/Astronauta/Radiacion/Index.vue
resources/js/modules/Radiacion/Pages/Tierra/Radiacion/Index.vue
```

## 3. Props de cada página

```ts
// Astronauta/Radiacion/Index
{
  carrera: { previa_msv: number, mision_msv: number, total_msv: number, limite_msv: number /* 600, M16 */, pct: number },
  umbralProyeccionPct: number,       // valor de DOSIS_PROYECCION (80): marca de la barra
  proyeccion: { total_msv: number, pct: number, fecha_fin: string, tasa_media_7d: number } | null,   // null sin fecha de fin
  tasaActual: { valor: number, unidad: 'mSv/d', medido_en: string } | null,
  diarias30: { fecha: string, dosis_msv: number, minutos_sin_dato: number }[],
  eventoSolar: { activo: boolean, desde: string, hasta: string | null, dosis_evento_msv: number, dosis_30d_msv: number,
                 referencia_30d: number /* limite_evento_msv, 250 a verificar */ } | null,
  minutosSinDatoHoy: number          // si es > 0, la página avisa "Dosis de hoy incompleta: faltan N min de lecturas"
}

// Tierra/Radiacion/Index
{ tripulantes: { codigo: string, carrera: …, proyeccion: …, tasaActual: … }[] }
```

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Radiacion/BarraCarrera.vue` | Barra de 0 a `limite_msv` (hoy 600 mSv) con la dosis actual, la proyección (tramo rayado) y la marca de `umbralProyeccionPct` (hoy 80 %, 480 mSv) |
| `Radiacion/PanelEventoSolar.vue` | Durante un evento: "IR AL REFUGIO", dosis acumulada del evento, dosis de los últimos 30 días frente a `referencia_30d` (hoy 250, pendiente de verificar en NASA-STD-3001) y hora de inicio |

## 5. Comportamiento

- `usePoll(10000, { only: ['tasaActual', 'eventoSolar', 'carrera'] })` en F1.
- La alarma de evento solar la dispara `AlarmaCritica` (M13) en todas las páginas; esta página añade el detalle de la dosis del evento.
- Sin fecha de fin de misión: "Sin proyección: la misión no tiene fecha de fin".

Bocetos, textos y criterios (incluido el panel del evento activo y terminado): [historias de frontend](HU/README.md).

## 6. Accesibilidad

- La barra tiene `role="progressbar"` con `aria-valuenow`, `aria-valuemax` (= `limite_msv`) y texto "312 de 600 mSv (52 %)".
