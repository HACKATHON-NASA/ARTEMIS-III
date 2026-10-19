# M10 · Físico y metabólico · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Sin conexión |
|---|---|---|---|:---:|
| F1 | `/astronauta/ejercicio` | `Astronauta/Ejercicio/Index` (sesiones y minutos semanales) | `astronauta`, `cmo` | Consulta |
| F1 | `/astronauta/ejercicio/registrar` | `Astronauta/Ejercicio/Create` | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/nutricion` | `Astronauta/Nutricion/Index` (totales del día y últimos registros) | `astronauta`, `cmo` | Consulta |
| F1 | `/astronauta/nutricion/registrar` | `Astronauta/Nutricion/Create` (comida o líquido en ≤ 3 toques) | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/fuerza/registrar` | `Astronauta/Fuerza/Create` (ambas manos) | `astronauta`, `cmo` | ✅ |
| F3 | `/tierra/tripulantes/{codigo}/fisico` | `Tierra/Fisico/Show` (masa, fuerza, ejercicio, ingesta y glucosa de 24 h y 7 días) | `medico_vuelo` | — |

## 2. Archivos

```
resources/js/modules/FisicoMetabolico/Pages/Astronauta/Ejercicio/{Index,Create}.vue
resources/js/modules/FisicoMetabolico/Pages/Astronauta/Nutricion/{Index,Create}.vue
resources/js/modules/FisicoMetabolico/Pages/Astronauta/Fuerza/Create.vue
resources/js/modules/FisicoMetabolico/Pages/Tierra/Fisico/Show.vue
```

## 3. Props de cada página

```ts
// Astronauta/Ejercicio/Index
{ sesiones: { id: string, equipo: 'ared' | 'cinta' | 'cicloergometro', inicio: string, duracion_min: number,
              carga_kg: number | null, fc_media: number | null }[],
  minutosSemana: number, metaSemanal: number /* ejercicio_meta_semanal_min de M16, hoy 840 */ }

// Astronauta/Ejercicio/Create
{ equipos: { valor: string, etiqueta: string }[] }

// Astronauta/Nutricion/Index
{ hoy: { kcal: number, proteina_g: number, liquidos_ml: number },
  registros: { id: string, tipo: 'comida' | 'liquido', descripcion: string | null, kcal: number | null,
               volumen_ml: number | null, consumido_en: string }[] }

// Astronauta/Nutricion/Create
{ atajos: { tipo: 'liquido', volumen_ml: number, etiqueta: string }[] }   // "Vaso 250 mL", "Bolsa 500 mL"

// Astronauta/Fuerza/Create
{ ultima: { izquierda: number | null, derecha: number | null, medido_en: string | null } }

// Tierra/Fisico/Show
{
  tripulante: { codigo: string },
  masaSemanal: { semana: number, media_kg: number }[], lineaBaseMasa: number | null, pendienteMasa: number | null,
  fuerza: { medido_en: string, izquierda: number, derecha: number, cambio_pct_izq: number, cambio_pct_der: number }[],
  umbralMasaPct: number,                             // MASA_PERDIDA (5): línea del −5 %
  ejercicioSemanal: { semana: number, minutos: number }[], metaSemanal: number,
  ingestaDiaria: { fecha: string, kcal: number, liquidos_ml: number }[],
  glucosa24h: { t: string, valor: number }[] | null, glucosa7d: { fecha: string, media: number, minimo: number, maximo: number }[] | null,
  limitesGlucosa: { min: number, max: number }       // fila GLUCOSA de M16 (70 y 180)
}
```

| Página | Acciones |
|---|---|
| `Astronauta/Ejercicio/Create` | Equipo, inicio y duración; carga solo si el equipo es ARED; se encola |
| `Astronauta/Nutricion/Create` | Toque 1: "Comida" o "Líquido"; toque 2: atajo o cantidad; toque 3: guardar. Se encola |
| `Astronauta/Fuerza/Create` | Dos campos (mano izquierda y derecha) con la última medición como referencia; se encolan dos registros |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Fisico/AnilloSemanal.vue` | Minutos de ejercicio de la semana frente a `metaSemanal` (hoy 840) |
| `Fisico/TotalesDia.vue` | kcal, proteína y mL del día (sin meta: no hay valores con fuente) |
| `Fisico/GraficaGlucosa.vue` | `GraficaSerie` con las líneas de `limitesGlucosa` (hoy 70 y 180 mg/dL) |

## 5. Comportamiento

- Las capturas funcionan sin red; los totales del día suman también lo que está en la cola local (marcado "pendiente de envío").
- La glucosa no se registra a mano: llega del CGM (simulado) por M03.

Bocetos, textos y criterios de cada pantalla: [historias de frontend](HU/README.md).

## 6. Accesibilidad

- Los atajos de líquidos son botones de 44 px con texto ("Vaso 250 mL").
