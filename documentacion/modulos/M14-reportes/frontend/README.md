# M14 · Reportes · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles |
|---|---|---|---|
| F3 | `/tierra/reportes` | `Tierra/Reportes/Index` (lista por día y tripulante, botones "Generar ahora" y "Reporte de misión") | `medico_vuelo` |
| F2 | `/cmo/reportes` | `Cmo/Reportes/Index` (diarios y "Reporte de misión") | `cmo` |
| F4 | `/admin/exportaciones` | `Admin/Exportaciones/Index` (rango de días y variables) | `investigador` |
| — | `/reportes/{id}/descargar` | Descarga del archivo | Según el tipo |

## 2. Archivos

```
resources/js/modules/Reportes/Pages/Tierra/Reportes/Index.vue
resources/js/modules/Reportes/Pages/Cmo/Reportes/Index.vue
resources/js/modules/Reportes/Pages/Admin/Exportaciones/Index.vue
```

## 3. Props de cada página

```ts
type Reporte = { id: string, tipo: 'diario' | 'mision', tripulante: string, fecha: string,
                 nodo_origen: 'nave' | 'tierra', generado_en: string,
                 generado_por: string | null /* null = automático */, bytes: number,
                 desactualizado: boolean, url: string }

type Exportacion = { id: string, estado: 'pendiente' | 'listo' | 'error', error: string | null,
                     parametros: { dia_desde: number, dia_hasta: number, variables: string[], incluir_sexo_edad: boolean },
                     bytes: number | null, generado_en: string, url: string | null }

// Tierra/Reportes/Index  y  Cmo/Reportes/Index
{ reportes: Paginado<Reporte>, tripulantes: { codigo: string }[], filtros: { tripulante?: string, desde?: string, hasta?: string } }

// Admin/Exportaciones/Index
{ exportaciones: Exportacion[], variables: { codigo: string, nombre: string }[], diasMision: { min: number, max: number },
  maxDias: number /* 7, límite técnico del backend */ }
```

| Página | Acciones |
|---|---|
| `Tierra/Reportes/Index`, `Cmo/Reportes/Index` | "Generar ahora" (diario de un tripulante y fecha) y "Reporte de misión" (de un tripulante); ambos muestran "Generando…" y descargan al terminar |
| `Admin/Exportaciones/Index` | Rango de días de misión y variables → "Exportar CSV"; la descarga se audita |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Reportes/FilaReporte.vue` | Tipo, tripulante, fecha, insignia "Desactualizado" (si hubo alertas después de generarlo) y botón de descarga |

## 5. Comportamiento

- La generación de los PDF es síncrona en la petición (pocos segundos) y la respuesta trae `flash.descargarUrl`, que se abre para descargarlo; el botón se desactiva mientras tanto para evitar dobles envíos. Las **exportaciones** se generan en cola: la lista muestra "Generando…" y se refresca hasta que están listas. Bocetos en las [historias de frontend](HU/README.md).
- Requiere conexión (F2, F3 y F4).

## 6. Accesibilidad

- Los botones de descarga indican el formato y el tamaño ("PDF, 180 KB").
