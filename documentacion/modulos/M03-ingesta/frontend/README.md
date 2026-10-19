# M03 · Ingesta de datos · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ubicación | Componente | Roles |
|---|---|---|---|
| F1 | Inicio del astronauta (`/astronauta`) | `Components/EstadoSensores.vue` (aviso "reacomoda el sensor") | `astronauta`, `cmo` |
| F4 | `/admin/dispositivos` | Columna de estado en `Admin/Dispositivos/Index` (M02) | `ingeniero_biomedico`, `admin` |

## 2. Archivos

```
resources/js/components/EstadoSensores.vue      # Aviso "reacomoda el sensor" en el inicio de F1
```

## 3. Props y datos

`EstadoSensores.vue` no es una página: vive en el inicio de F1. La columna de estado de `Admin/Dispositivos/Index` la pinta `EstadoDispositivo.vue` de M02 con `EstadoSensores::todos()`.

```ts
// Prop que recibe el inicio de F1 (Astronauta/Inicio) desde el backend
estadoSensores: { identificador: string, tipo: TipoDispositivo,
                  estado: 'ok' | 'revisar_colocacion' | 'sin_datos',
                  desde: string | null }[]
```

Sin conexión, el componente usa la última copia guardada en `instantaneas` (M15) y lo indica ("Estado de hace 12 min").

## 4. Comportamiento

- Refresco con `usePoll(10000, { only: ['estadoSensores'] })`.
- Con `revisar_colocacion` muestra una tarjeta de aviso suave (no roja): icono ⚠, "Reacomoda el sensor del pecho (astroskin-T2)" y "Comprueba que esté bien colocado.". Sin enlace a instrucciones (no existen). Desaparece sola cuando el backend devuelve `ok`. Textos por tipo de dispositivo en las [historias de frontend](HU/README.md).
- `sin_datos` aparece si un dispositivo **de envío continuo** no envía nada en 10 minutos (`sensor_sin_datos_min`); todos los sensores sin datos se agrupan en una sola tarjeta.
- El estado se calcula con los datos en vivo: los artefactos del relleno histórico nunca muestran el aviso.
- No es una alerta clínica: no suena ni entra en la bandeja de M13.

## 5. Accesibilidad

- La tarjeta usa `role="status"` y `aria-live="polite"`.

## 6. Pruebas mínimas (Vitest)

- [ ] Con `revisar_colocacion` se muestra el aviso con el identificador del dispositivo; con `ok` no se muestra nada.
