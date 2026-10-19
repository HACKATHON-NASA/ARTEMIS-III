# M12 · Analítica y línea base · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles |
|---|---|---|---|
| F3 | `/tierra/tripulantes/{codigo}/tendencias` | `Tierra/Tendencias/Show` (7 y 30 días por variable, con banda de línea base) | `medico_vuelo` |
| F3 | `/tierra/tripulantes/{codigo}/lineas-base` | `Tierra/LineasBase/Index` (vigentes, historial y botón "Recalcular" con motivo) | `medico_vuelo` |

## 2. Archivos

```
resources/js/modules/Analitica/Pages/Tierra/Tendencias/Show.vue
resources/js/modules/Analitica/Pages/Tierra/LineasBase/Index.vue
```

## 3. Props de cada página

```ts
// Tierra/Tendencias/Show
{
  tripulante: { codigo: string },
  variable: string,                                          // código elegido (?variable=8867-4)
  variables: { codigo: string, nombre: string, unidad: string }[],
  diarios: { fecha: string, media: number, ewma: number }[],   // 30 días
  pendiente7: { por_semana: number, p: number } | null,
  pendiente30: { por_semana: number, p: number } | null,
  lineaBase: { media: number, desviacion: number, fuente: string } | null,
  pMax: number                                               // tendencia_p_max de M16 (0,05)
}

// Tierra/LineasBase/Index
{
  tripulante: { codigo: string },
  vigentes: { codigo: string, nombre: string, unidad: string, contexto: string | null, media: number, desviacion: number,
              n: number, fuente: 'previa_vuelo' | 'ventana_inicial' | 'recalculo_autorizado', created_at: string,
              recalculable: boolean /* false en las cognitivas */ }[],
  historial: { codigo: string, contexto: string | null, media: number, desviacion: number, fuente: string,
               autorizada_por: string | null, created_at: string }[],
  solicitudes: { id: string, codigo: string, contexto: string | null, ventana_desde: string, ventana_hasta: string,
                 motivo: string, estado: 'pendiente' | 'completada' | 'rechazada', motivo_rechazo: string | null,
                 llegada_estimada: string | null /* M15; null hasta HU-15.01 */ }[]
}
```

| Página | Acciones |
|---|---|
| `Tierra/Tendencias/Show` | Cambiar de variable (`router.get` con `preserveState`) |
| `Tierra/LineasBase/Index` | "Recalcular" abre un diálogo con el **motivo obligatorio** y la ventana de fechas; confirma con un segundo paso porque cambia la referencia de las alertas. La solicitud viaja a la nave, que hace el cálculo: la página muestra "Solicitud enviada; llegará a la nave hacia las 14:17 UTC" y la línea base nueva aparece cuando vuelve |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Analitica/GraficaTendencia.vue` | Media diaria, EWMA, banda μ ± 2σ de la línea base y recta de la pendiente |
| `Analitica/Explicacion.vue` | Muestra el JSON `explicacion` de un hallazgo (texto, valor, umbral, línea base, contexto y datos relacionados). Lo reutilizan M13 y M14 |

## 5. Comportamiento

- La pendiente se muestra como texto: "+3,2 lpm por semana (p = 0,01)"; si p ≥ `tendencia_p_max` (0,05, que el backend envía como `pMax`): "sin tendencia significativa".

Bocetos, textos y criterios (incluido el diálogo de recálculo en dos pasos y el campo `confidencial` de la explicación): [historias de frontend](HU/README.md).

## 6. Accesibilidad

- `Explicacion.vue` es texto estructurado (lista de definiciones), legible sin la gráfica.
