# M02 · Misión y tripulación · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Nodo | Ruta web | Componente | Roles |
|---|---|---|---|---|
| F4 | Nave | `/admin/mision` | `Admin/Mision/Edit` | `admin` |
| F4 | Nave | `/admin/tripulantes` | `Admin/Tripulantes/Index` | `admin` |
| F4 | Nave | `/admin/dispositivos` | `Admin/Dispositivos/Index` | `admin`, `ingeniero_biomedico` |
| F3 | Tierra | `/tierra/tripulantes/{codigo}/linea-base` | `Tierra/LineaBase/Edit` | `medico_vuelo` |

La misión, los tripulantes y los dispositivos se editan **en la nave** y viajan a la Tierra; en el nodo Tierra las páginas de F4 de M02 son de solo lectura (*prop* `soloLectura`). Los módulos de la nave se gestionan dentro de `Admin/Mision/Edit`. Cómo debe verse cada pantalla: [historias de frontend](HU/README.md). La línea base previa al vuelo se edita **en la Tierra** y viaja a la nave ([backend §3.4](../backend/README.md#34-línea-base-previa-al-vuelo-hu-0203)).

## 2. Archivos

```
resources/js/modules/Mision/Pages/Admin/Mision/Edit.vue
resources/js/modules/Mision/Pages/Admin/Tripulantes/Index.vue
resources/js/modules/Mision/Pages/Admin/Dispositivos/Index.vue
resources/js/modules/Mision/Pages/Tierra/LineaBase/Edit.vue
resources/js/components/DiaMision.vue            # "D12" en las cabeceras de F1, F2 y F3
```

## 3. Props de cada página

```ts
// Admin/Mision/Edit
{
  mision: { id: string, nombre: string, perfil_enlace: 'leo' | 'luna' | 'marte',
            fecha_inicio: string, fecha_fin: string | null, dia_mision: number } | null,
  modulosNave: { id: string, codigo: string, nombre: string }[],
  soloLectura: boolean             // los textos de los perfiles están en resources/js/contratos/mision.ts
}

// Admin/Tripulantes/Index
{
  tripulantes: { id: string, codigo: string, nombre: string, sexo: 'F' | 'M', fecha_nacimiento: string,
                 rol_a_bordo: string, usuario: { id: number, email: string } | null }[],
  usuariosDisponibles: { id: number, email: string, rol: 'astronauta' | 'cmo' }[],   // activos y sin tripulante en la misión activa
  soloLectura: boolean
}

// Admin/Dispositivos/Index
{
  dispositivos: { id: string, identificador: string, tipo: TipoDispositivo,
                  asignado_a: { tipo: 'tripulante' | 'modulo', codigo: string, de_mision_activa: boolean } | null,
                  bateria_pct: number | null, ultimo_contacto_en: string | null,
                  estado_sensor: 'ok' | 'revisar_colocacion' | 'sin_datos' | null }[],   // columna de M03; null hasta HU-03.03
  tripulantes: { id: string, codigo: string }[],
  modulosNave: { id: string, codigo: string, nombre: string }[],
  tipos: TipoDispositivo[],
  soloLectura: boolean
}

// Tierra/LineaBase/Edit
{
  tripulante: { id: string, codigo: string, nombre: string },
  tripulantes: { codigo: string }[],
  variables: { codigo: string, nombre: string, unidad: string, contexto: 'reposo' | null, min: number, max: number }[],  // las 8 de VariablePrevuelo (M02)
  vigentes: { codigo: string, contexto: string | null, media: number, desviacion: number, n: number, fuente: string, created_at: string }[],
  sincronizada: boolean | null     // null hasta HU-15.01; false mientras no haya llegado a la nave
}
```

| Página | Acciones |
|---|---|
| `Admin/Mision/Edit` | Guardar la misión; cambiar el perfil de enlace (pide confirmación: cambia el retardo de M15); empezar otra misión (cierra la actual, con confirmación) |
| `Admin/Tripulantes/Index` | Alta y edición en un panel lateral; vincular usuario (solo usuarios `astronauta` o `cmo` sin tripulante); la tabla marca con ⚠ y texto los usuarios de esos roles que aún no tienen tripulante |
| `Admin/Dispositivos/Index` | Alta, asignación a tripulante **o** módulo (el formulario muestra un solo selector según el tipo) |
| `Tierra/LineaBase/Edit` | Una fila por variable con media y σ; guardar envía todas con `fuente = previa_vuelo` y el contexto de cada variable; muestra "Pendiente de llegar a la nave" hasta que se entrega el paquete `configuracion` |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `DiaMision.vue` | Muestra "D12" (`formato="corto"`) o "Día 12 de misión" (`formato="largo"`), y "Previo al vuelo" si el día es ≤ 0, a partir de la *prop* compartida `mision`; con la *prop* `fecha` muestra el día de misión de ese dato |
| `Admin/Dispositivos/EstadoDispositivo.vue` | Batería, último contacto ("hace 3 min") y estado del sensor con icono y texto |

## 5. Comportamiento

- Todas las páginas requieren conexión (F3 y F4 no trabajan sin red).
- El menú muestra cada página solo si el usuario cumple su *Gate* (`gestionar-mision`, `registrar-dispositivos`, `registrar-linea-base`) en `auth.permisos`.
- La tabla de dispositivos se refresca con `usePoll(30000, { only: ['dispositivos'] })`.

## 6. Accesibilidad

- El sexo se elige con botones de opción etiquetados ("Femenino", "Masculino"), no con un desplegable sin etiqueta.
- La batería se muestra con número y barra (no solo color).
