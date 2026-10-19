# M06 · Sueño · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Sin conexión |
|---|---|---|---|:---:|
| F1 | `/astronauta/sueno` | `Astronauta/Sueno/Index` (anoche + tendencia de 7 y 30 noches + regularidad) | `astronauta`, `cmo` | Consulta |
| F1 | `/astronauta/sueno/registrar` | `Astronauta/Sueno/Create` (manual y medicación) | `astronauta`, `cmo` | ✅ |
| F2 | `/cmo/tripulantes/{codigo}` | Pestaña "Sueño" de `Cmo/Tripulante/Show` | `cmo` | — |

## 2. Archivos

```
resources/js/modules/Sueno/Pages/Astronauta/Sueno/{Index,Create}.vue
```

## 3. Props de cada página

```ts
// Astronauta/Sueno/Index
{
  anoche: { noche: string, duracion_h: number | null, eficiencia_pct: number | null, acostado_en: string | null,
            despertado_en: string, medicacion: boolean | null, medicacion_nombre: string | null,
            origen: 'actigrafia' | 'simulador' | 'manual' } | null,
  noches7: { noche: string, duracion_h: number, eficiencia_pct: number, mala: boolean, medicacion: boolean | null }[],
  noches30: { noche: string, duracion_h: number, eficiencia_pct: number, mala: boolean, medicacion: boolean | null }[],
  umbrales: { horas: number, eficiencia: number },   // SUENO_MAL de la misión (M16)
  horasProgramadas: number,                          // sueno_programado_h (M16), línea punteada
  regularidadMin: number | null,             // desviación de la hora de acostarse (sueno_regularidad_noches, hoy 7)
  lineaBase: { duracion: { media: number, desviacion: number } | null, eficiencia: { … } | null }
}

// Astronauta/Sueno/Create
{ nocheSugerida: string /* la noche que termina hoy: fecha UTC de (ahora − 12 h) */,
  despertadoSugerido: string /* hora de hoy en que terminó el sueño según la agenda */,
  yaExiste: { origen: string, duracion_h: number | null, eficiencia_pct: number | null, medicacion: boolean | null } | null }
  // yaExiste describe solo nocheSugerida

// Cmo/Tripulante/Show — pestaña "Sueño"
{ sueno: { noches14: { noche: string, duracion_h: number, eficiencia_pct: number, mala: boolean, medicacion: boolean | null }[],
            umbrales: { horas: number, eficiencia: number } } }
```

| Página | Acciones |
|---|---|
| `Astronauta/Sueno/Create` | Registrar a mano (horarios y horas dormidas aproximadas; la eficiencia la calcula el servidor), medicación, luz y ruido 0–10, o **solo** la medicación si el actígrafo ya envió la noche; se encola en `/api/v1/sueno/registros`. Bocetos en las [historias de frontend](HU/README.md) |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Sueno/BarrasNoches.vue` | Barras por noche con una línea en el umbral de horas (`umbrales.horas`, hoy 6 h), otra punteada en `horasProgramadas` y marca de "noche mala" (icono, no solo color) |
| `Sueno/RelojNoche.vue` | Franja horaria acostarse → despertar de las noches de la regularidad (hoy 7) para ver la regularidad |

## 5. Comportamiento

- Si ya hay un registro de actigrafía de esa noche, el formulario lo indica ("El actígrafo registró 6,4 h") y solo pide medicación, luz y ruido: el backend fusiona ambos.
- El formulario pide la hora de acostarse y la de despertar (no la fecha de la noche): la noche la calcula el servidor. Muestra "Noche del 12 al 13 de octubre" para que no haya dudas.
- La consulta funciona sin red con la última instantánea.

## 6. Accesibilidad

- Cada barra tiene `aria-label` con la noche, las horas y la eficiencia ("Noche del 12 de octubre: 5,2 horas, eficiencia 83 %, noche mala").
