# M16 · Configuración · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles |
|---|---|---|---|
| F3 | `/tierra/umbrales` | `Tierra/Umbrales/Index` (lista, historial y ajustes temporales pendientes de revisión) | `medico_vuelo` |
| F3 | `/tierra/umbrales/{regla}/editar` | `Tierra/Umbrales/Edit` (motivo obligatorio) | `medico_vuelo` |
| F2 | `/cmo/umbrales` | `Cmo/Umbrales/Index` (solo lectura + "Ajuste temporal") | `cmo` |
| F4 | `/admin/catalogos` | `Admin/Catalogos/Index` (variables, pruebas, síntomas) | `admin` |
| F4 | `/admin/parametros` | `Admin/Parametros/Index` (configuración de la misión activa) | `admin` (operativos), `medico_vuelo` (clínicos) |

## 2. Archivos

```
resources/js/modules/Configuracion/Pages/Tierra/Umbrales/{Index,Edit}.vue
resources/js/modules/Configuracion/Pages/Cmo/Umbrales/Index.vue
resources/js/modules/Configuracion/Pages/Admin/Catalogos/Index.vue
resources/js/modules/Configuracion/Pages/Admin/Parametros/Index.vue
resources/js/modules/Configuracion/Components/{FormularioUmbral,HistorialUmbral}.vue
```

## 3. Props de cada página

```ts
type Umbral = { id: string, regla_codigo: string, version: number, familia: string,
                variable_codigo: string | null, variable_nombre: string | null, unidad: string | null,
                evaluador: 'generico' | 'especializada', descripcion: string /* DescripcionUmbral */,
                parametro_diseno: boolean /* † */,
                contextos: string[] | null, operador: string, valor: number | null, valor2: number | null,
                nivel: 'aviso' | 'critico', persistencia_valor: number | null, persistencia_unidad: string,
                sistema: Sistema, activo: boolean, fuente: string | null, origen: 'tierra' | 'ajuste_temporal',
                estado_revision: 'pendiente' | 'confirmado' | 'revertido' | null, motivo: string,
                modificado_por: string | null /* null = "Valor inicial" */, modificado_en: string }

type ValoresTierra = { valor: number | null, valor2: number | null, persistencia_valor: number | null }

// Tierra/Umbrales/Index
{ vigentes: Umbral[], ajustesPendientes: (Umbral & { tierra: ValoresTierra })[] }

// Tierra/Umbrales/Edit
{ umbral: Umbral, historial: Umbral[],
  limites: { valor: { min: number, max: number }, valor2: { min: number, max: number } | null, unidad: string } }

// Cmo/Umbrales/Index
{ vigentes: (Umbral & { tierra: ValoresTierra | null })[] /* el ajuste temporal, si existe, aparece en lugar de la versión de la Tierra */,
  apagon: boolean /* el botón "Ajuste temporal" solo se activa si es true */ }

// Admin/Catalogos/Index
{ pestana: 'variables' | 'pruebas' | 'sintomas', editable: boolean /* false en la nave */,
  variables: { codigo: string, nombre: string, unidad: string, modulo: string, origen: 'sensor' | 'derivado',
               minimo_posible: number, maximo_posible: number }[],
  pruebas: { codigo: string, nombre: string, unidad: string, tipo: 'laboratorio' | 'imagen',
             ref_min_h: number | null, ref_max_h: number | null, ref_min_m: number | null, ref_max_m: number | null,
             critico_min: number | null, critico_max: number | null, delta_umbral: number | null,
             direccion_desfavorable: 'baja' | 'sube' | 'ambas', alerta_fuera_rango: boolean, sistema: Sistema,
             activo: boolean, tiene_resultados: boolean /* bloquea la unidad */ }[],
  sintomas: { codigo: string, nombre: string, activo: boolean }[] }

// Admin/Parametros/Index
{ mision: { nombre: string },
  parametros: { clave: string, categoria: 'clinico' | 'operativo', valor: unknown, descripcion: string, fuente: string,
                parametro_diseno: boolean, bloqueado: boolean /* línea base desde el día 1 */ }[],
  puedeEditar: { clinico: boolean, operativo: boolean } }
```

| Página | Acciones |
|---|---|
| `Tierra/Umbrales/Edit` | Guardar crea la versión `n + 1`; **motivo** obligatorio; **activar** exige valor y fuente (los campos se marcan si faltan) |
| `Tierra/Umbrales/Index` | Confirmar o revertir cada ajuste temporal pendiente |
| `Cmo/Umbrales/Index` | "Ajuste temporal" en un diálogo (valor y motivo; `POST /cmo/umbrales/{regla}/ajuste`); solo en el nodo nave y se encola hacia la Tierra. El botón solo está activo durante un apagón (`apagon`); si no, muestra "Hay enlace con la Tierra: pide el cambio al médico de vuelo". El diálogo solo permite cambiar valores y persistencia |
| `Admin/Catalogos/Index`, `Admin/Parametros/Index` | Edición en un diálogo con motivo, en el nodo Tierra; en la nave, solo lectura. Bocetos en las [historias de frontend](HU/README.md) |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `FormularioUmbral.vue` | Campos según el operador (`fuera_de` muestra dos valores); vista previa en texto ("Crítico si FC < 40 o > 120 lpm durante 5 min en reposo o sueño") |
| `HistorialUmbral.vue` | Versiones con quién, cuándo, motivo y fuente |

## 5. Comportamiento

- Los umbrales con † (parámetro de diseño) muestran la marca y el texto "A validar por médicos".
- En el nodo nave, las rutas de la Tierra responden `403` ("Esta información se edita en la Tierra.") por el middleware `SoloEnNodo:tierra` de M02.

## 6. Accesibilidad

- La vista previa en texto del umbral permite revisar la regla sin interpretar los campos uno a uno.
