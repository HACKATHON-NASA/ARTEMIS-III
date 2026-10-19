# M07 · Exámenes médicos · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Sin conexión |
|---|---|---|---|:---:|
| F1 | `/astronauta/examenes` | `Astronauta/Examenes/Index` (lista, recordatorios y gráficas) | `astronauta`, `cmo` | Consulta |
| F1 | `/astronauta/examenes/cargar` | `Astronauta/Examenes/Create` | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/examenes/importar` | `Astronauta/Examenes/Importar` | `astronauta`, `cmo` | — |
| F2 | `/cmo/tripulantes/{codigo}` | Pestaña "Exámenes" de `Cmo/Tripulante/Show` | `cmo` | — |
| F3 | `/tierra/tripulantes/{codigo}/examenes` | `Tierra/Examenes/Show` | `medico_vuelo` | — |

La lista de `Astronauta/Examenes/Index` agrupa los resultados por fecha de muestra y muestra el icono 📄 cuando esa fecha tiene PDF.

Cada gráfica muestra la banda del rango de referencia, los puntos coloreados según la interpretación (con icono y texto), la línea de tendencia y las fechas clave de la misión ([04 §6.4](../../../docs/propuesta/04-manejo-de-datos.md#64-visualización)).

## 2. Archivos

```
resources/js/modules/Examenes/Pages/Astronauta/Examenes/{Index,Create,Importar}.vue
resources/js/modules/Examenes/Pages/Tierra/Examenes/Show.vue
```

## 3. Props de cada página

```ts
type Resultado = { id: string, prueba_codigo: string, prueba: string, fecha_muestra: string, valor: number,
                   unidad: string, interpretacion: 'bajo' | 'normal' | 'alto' | 'critico',
                   interpretacion_vigente: 'bajo' | 'normal' | 'alto' | 'critico', rango_cambio: boolean,
                   delta_superado: boolean, tendencia_desfavorable: boolean }

// Astronauta/Examenes/Index
{
  porFecha: { fecha_muestra: string, resultados: Resultado[], documento: { id: string, nombre: string } | null }[],
  recordatorios: { prueba: string, codigo: string, proxima_fecha: string, estado: 'vencido' | 'hoy' | 'proximo' }[],
  series: Record<string /* código */, { fecha_muestra: string, valor: number,
                                        interpretacion_vigente: 'bajo' | 'normal' | 'alto' | 'critico' }[]>,
  rangos: Record<string, { nombre: string, min: number | null, max: number | null, critico_min: number | null,
                           critico_max: number | null, unidad: string,
                           direccion_desfavorable: 'baja' | 'sube' | 'ambas' }>,   // según el sexo del tripulante
  tendencias: Record<string, { pendiente_por_semana: number, desde: string, hasta: string }>,   // ≥ 3 tomas; solo visual
  fechasMision: { inicio: string },
  pdfMaxKb: number                                      // 2048
}

// Astronauta/Examenes/Create
{ catalogo: { codigo: string, nombre: string, unidad: string, tipo: 'laboratorio' | 'imagen' }[],   // solo pruebas activas
  fechaMaxima: string }                                  // hoy (UTC); ?prueba=<código> preselecciona

// Astronauta/Examenes/Importar
{ vistaPrevia?: { token: string, validas: { fila: number, codigo: string, valor: number, corrige: { valor_anterior: number } | null }[],
                  erroneas: { fila: number, motivo: string }[] } }

// Tierra/Examenes/Show  (y pestaña "Exámenes" de Cmo/Tripulante/Show)
{ tripulante: { codigo: string, sexo: 'F' | 'M' }, porFecha: …, series: …, rangos: …, tendencias: …, fechasMision: … }
```

| Página | Acciones |
|---|---|
| `Astronauta/Examenes/Create` | Elegir la prueba fija la unidad (campo de solo lectura); fecha de la muestra obligatoria; se encola en `/api/v1/examenes/resultados` |
| `Astronauta/Examenes/Importar` | Paso 1: subir CSV → vista previa (filas válidas y erróneas con su motivo). Paso 2: "Guardar N filas válidas". Requiere conexión |
| `Astronauta/Examenes/Index` | "Adjuntar PDF" en cada fecha de muestra (*multipart* con `useForm`, barra de progreso); requiere conexión |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Examenes/GraficaPrueba.vue` | `GraficaSerie` con banda de referencia, líneas críticas, puntos coloreados **con forma distinta** por interpretación y línea de tendencia |
| `Examenes/InsigniaInterpretacion.vue` | "▼ Bajo", "● Normal", "▲ Alto", "✖ Crítico" (icono + texto + color) |
| `Examenes/TablaVistaPrevia.vue` | Filas del CSV con el error en la misma fila |

## 5. Comportamiento

- Si `rango_cambio` es `true`, la insignia muestra la interpretación vigente y un texto pequeño "Al cargar: Normal (el rango cambió)".
- Los recordatorios vencidos aparecen arriba en el inicio de F1.
- El formulario y la consulta funcionan sin red; la importación CSV y el PDF no (el botón se desactiva con el texto "Requiere conexión").

Bocetos, textos y criterios de cada pantalla: [historias de frontend](HU/README.md).

## 6. Accesibilidad

- La gráfica tiene debajo una tabla con las mismas tomas (alternativa textual).
- El selector de archivo anuncia el tamaño máximo de `pdfMaxKb` ("PDF de hasta 2 MB").
