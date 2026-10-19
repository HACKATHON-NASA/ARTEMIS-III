# M15 · Sincronización · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Refresco |
|---|---|---|---|---|
| F1 | Cabecera de todas las páginas | `Components/IndicadorSincronizacion.vue` (pendientes locales y última sincronización) | `astronauta`, `cmo` | Local |
| F2 | `/cmo/enlace` | `Cmo/Enlace/Index` (estado, ventana activa, pendientes por prioridad, hora estimada) | `cmo`, `ingeniero_biomedico`, `admin` (`ver-enlace`) | Sondeo 10 s |
| F2 | `/cmo/recomendaciones` | `Cmo/Recomendaciones/Index` | `cmo` | Sondeo 10 s |
| F3 | `/tierra` | `Tierra/Mision/Index` (estado de la tripulación y del enlace) | `medico_vuelo` | Sondeo 10 s |
| F3 | `/tierra/recomendaciones/crear` | `Tierra/Recomendaciones/Create` | `medico_vuelo` | — |

## 2. Archivos

```
resources/js/offline/{db.ts,cola.ts,clienteApi.ts,instantaneas.ts}
resources/js/components/IndicadorSincronizacion.vue
resources/js/modules/Sincronizacion/Pages/Cmo/{Enlace,Recomendaciones}/Index.vue
resources/js/modules/Sincronizacion/Pages/Tierra/Mision/Index.vue
resources/js/modules/Sincronizacion/Pages/Tierra/Recomendaciones/Create.vue
```

## 3. Props de cada página

```ts
// Cmo/Enlace/Index   (y el panel del enlace de Tierra/Mision/Index)
{
  enlace: { nodo: 'nave' | 'tierra', perfil: 'leo' | 'luna' | 'marte', retardo_min: number,
            cupo: { bytes: number, paquetes: number },
            ventana: { inicio: string, fin: string, motivo: string } | null,   // siempre null en la Tierra
            ultimo_ciclo_en: string | null,       // null en la Tierra (no corre el ciclo)
            ultimo_contacto_en: string | null },  // solo en la Tierra: última petición de la nave
  pendientes: { prioridad: 1 | 2 | 3, total: number,
                estimado_desde: string | null, estimado_hasta: string | null /* null en la Tierra */ }[],
  enTransito: { id: string, tipo: string, prioridad: number, entrega_programada_en: string }[]
}

// Cmo/Recomendaciones/Index
{ recomendaciones: { id: string, tripulante: string | null, alerta_id: string | null, autor: string, texto: string,
                     prioridad: 'normal' | 'urgente', creada_en: string, recibida_en: string, leida_en: string | null }[] }

// Tierra/Mision/Index
{ tripulantes: { codigo: string, semaforo: Record<Sistema, Color>, ultimoDato: string | null }[],
  enlace, pendientes, enTransito /* como Cmo/Enlace/Index */, alertasRecientes: Alerta[] /* las 10 últimas */ }

// Tierra/Recomendaciones/Create
{ tripulantes: { codigo: string }[], alertas: { id: string, texto: string }[], alerta_id?: string /* si viene de una alerta */,
  enlace: { perfil: 'leo' | 'luna' | 'marte', retardo_min: number } }

// Prop compartida en F2
recomendacionUrgente: { id: string, texto: string, autor: string, tripulante: string | null, recibida_en: string } | null
```

| Página | Acciones |
|---|---|
| `Cmo/Recomendaciones/Index` | "Marcar como leída" |
| `Tierra/Recomendaciones/Create` | Texto, destinatario (tripulante o toda la tripulación), prioridad y alerta opcional; al enviar muestra "Llegará a la nave hacia las 14:17 UTC" según el retardo |

## 4. Cola offline de la PWA (`resources/js/offline/`)

| Archivo | Responsabilidad |
|---|---|
| `db.ts` | Esquema Dexie: `cola_envios` (con `user_id`), `rechazados`, `instantaneas`, `credenciales` (un token por usuario) |
| `cola.ts` | `encolar(endpoint, cuerpo)` (guarda el `user_id` del usuario actual), `procesar()` (en orden de `creado_en`, solo los elementos del usuario con token vigente), reintentos con espera creciente, manejo de `201/200/422/401/404/5xx` según [backend §3.1](../backend/README.md#31-cola-offline-de-la-pwa-hu-1504) |
| `clienteApi.ts` | `fetch` con el token `registros:escribir`; arma *multipart* si el cuerpo trae un `Blob` |
| `instantaneas.ts` | Guarda la última copia de las *props* de cada página de consulta (`router.on('success')`), las precarga tras iniciar sesión (`fetch` con `X-Inertia: true`) y, sin red, las muestra con `router.push` de Inertia 2 sin pedir nada al servidor. Detalle en las [historias de frontend](HU/README.md) |
| `comprimirImagen.ts` | Ver [M11](../../M11-sintomas/frontend/README.md) |

El *Service Worker* lo genera `vite-plugin-pwa` (precarga de JS, CSS y fuentes; navegación a F1 con la última instantánea; la alarma de M13 usa Web Audio, sin archivo).

## 5. Componentes propios

| Componente | Uso |
|---|---|
| `IndicadorSincronizacion.vue` | En la cabecera de F1: "✔ Sincronizado" / "⟳ 3 pendientes" / "⚠ 1 rechazado"; al tocarlo, lista los pendientes y los rechazados con su motivo |
| `Enlace/LineaTiempoEnlace.vue` | Ventana sin enlace, ciclo actual y entregas programadas |

## 6. Comportamiento

- `usePoll(10000)` en las páginas del enlace y de recomendaciones.
- Una recomendación `urgente` no leída aparece como banda en todas las páginas de F2.

## 7. Accesibilidad

- El indicador anuncia los cambios con `aria-live="polite"` ("3 registros pendientes de envío").

## 8. Pruebas mínimas (Vitest)

- [ ] `cola.ts` reintenta tras un `5xx`, mueve a `rechazados` tras un `422` y conserva el orden.
- [ ] Un `404` en `/sintomas/{id}/foto` se reintenta (no se rechaza).
