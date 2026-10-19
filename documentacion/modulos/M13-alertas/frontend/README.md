# M13 · Alertas y casos clínicos · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Refresco |
|---|---|---|---|---|
| F2 | `/cmo/alertas` | `Cmo/Alertas/Index` (bandeja por gravedad y antigüedad) | `cmo` | Sondeo 5 s |
| F2 | `/cmo/alertas/{id}` | `Cmo/Alertas/Show` (explicación, contexto, confirmar o descartar) | `cmo` | — |
| F2 | `/cmo/casos/{id}` | `Cmo/Casos/Show` (protocolo paso a paso y acciones) | `cmo` | — |
| F3 | `/tierra/alertas` | `Tierra/Alertas/Index` (historial y decisiones tomadas a bordo) | `medico_vuelo` | Sondeo 10 s |
| F4 | `/admin/protocolos` | `Admin/Protocolos/Index` (pasos, fuentes y registro de la validación; se edita en la Tierra, en la nave es de solo lectura) | `admin` | — |
| F1, F2 | Todas | `Components/AlarmaCritica.vue` en el *layout* (ventana modal + sonido) | `astronauta`, `cmo` | Sondeo 5 s |

> **Sonido:** los navegadores bloquean el audio hasta que el usuario interactúa. Al iniciar sesión, F1 y F2 piden tocar "Activar alarmas sonoras" una vez.

## 2. Archivos

```
resources/js/modules/Alertas/Pages/Cmo/Alertas/{Index,Show}.vue
resources/js/modules/Alertas/Pages/Cmo/Casos/Show.vue
resources/js/modules/Alertas/Pages/Tierra/Alertas/Index.vue
resources/js/modules/Alertas/Pages/Admin/Protocolos/Index.vue
resources/js/components/AlarmaCritica.vue
```

## 3. Props de cada página

```ts
type Alerta = { id: string, tripulante: string | null /* null = toda la tripulación */, familia: string,
                regla_codigo: string, sistema: Sistema, nivel: 'aviso' | 'critico',
                estado: 'abierta' | 'confirmada' | 'descartada' | 'cerrada', ocurrencias: number,
                primera_en: string, ultima_en: string, texto: string }

// Cmo/Alertas/Index
{ alertas: Alerta[] /* activas, ordenadas: crítico primero y luego por antigüedad */, filtros: { estado?: string, sistema?: string } }

// Cmo/Alertas/Show
{ alerta: Alerta & { explicacion: Explicacion, confirmacion: 'automatica' | 'manual' | 'no_aplica',
                     motivo_descarte: 'falso_positivo' | 'artefacto' | 'otro' | null, nota_cierre: string | null,
                     caso: { id: string, estado: 'abierto' | 'cerrado' } | null },
  ocurrenciasRecientes: { nivel: 'aviso' | 'critico', recibido_en: string }[],   // las 10 últimas
  recomendaciones: { texto: string, autor: string, prioridad: 'normal' | 'urgente', recibida_en: string }[],
  motivosDescarte: ['falso_positivo', 'artefacto', 'otro'],
  confirmacionMin: number }                          // alerta_confirmacion_min de M16 (30)

// Cmo/Casos/Show
{ caso: { id: string, tripulante: string | null, estado: 'abierto' | 'cerrado', abierto_en: string,
          cerrado_en: string | null, resultado: string | null },
  protocolo: { codigo: string, nombre: string, validado_en: string | null,   // de la copia del caso
               pasos: { numero: number, texto: string, fuente: string | null /* null antes de HU-13.04 */ }[] },
  acciones: { id: string, tipo: 'paso' | 'nota' | 'recomendacion', paso_numero: number | null,
              descripcion: string, autor: string, realizada_en: string }[],
  alertas: Alerta[] }

// Tierra/Alertas/Index
{ alertas: Paginado<Alerta & { decision: string | null /* lo decidido a bordo; null si nadie decidió aún */ }>,
  descartesPorRegla: { regla_codigo: string, total: number, falsos_positivos_pct: number }[] }

// Admin/Protocolos/Index
{ protocolos: { codigo: string, nombre: string, familias: string[], validado_por: string | null,
                validado_en: string | null, pasos: { numero: number, texto: string, fuente: string | null }[] }[],
  editable: boolean }                                 // true solo en la Tierra

// Props compartidas (middleware CompartirAlertasCriticas, F1 y F2)
alertasCriticas: { id: string, tripulante: string | null, texto: string, primera_en: string,
                   nivel_cambiado_en: string }[]
resumenAlertas: { activas: number, nivel_maximo: 'aviso' | 'critico' | null }   // insignia del menú
```

| Página | Acciones |
|---|---|
| `Cmo/Alertas/Show` | Confirmar; descartar (motivo obligatorio); cerrar sin caso (nota obligatoria); abrir caso |
| `Cmo/Casos/Show` | Marcar cada paso como hecho (crea una acción; se encola para funcionar sin red); añadir una nota; cerrar con resultado obligatorio |
| `Admin/Protocolos/Index` | Solo en la Tierra (`editable`): editar pasos y fuentes (anula la validación, con aviso previo); registrar la validación (nombre, profesión y fecha) |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `AlarmaCritica.vue` | Modal que no se cierra sin "Enterado"; genera el sonido con **Web Audio** (oscilador de 880 Hz intermitente) en bucle hasta entonces, sin archivo de audio; muestra la alerta más reciente y cuántas hay. Bocetos en las [historias de frontend](HU/README.md) |
| `Alertas/FilaAlerta.vue` | Nivel (icono + texto), tripulante, sistema, texto, ocurrencias y antigüedad |
| `Alertas/AvisoNoValidado.vue` | Banda "⚠️ Protocolo de demostración: pendiente de validación clínica" |
| `Explicacion.vue` (M12) | Detalle del porqué de la alerta |

## 5. Comportamiento

- `usePoll(5000, { only: ['alertasCriticas', 'resumenAlertas'] })` en los *layouts* de F1 y F2; la bandeja también se refresca cada 5 s.
- "Enterado" se recuerda por alerta en `localStorage`; si la alerta **escala** o llega otra crítica, la alarma vuelve a sonar.
- Al iniciar sesión, F1 y F2 muestran una vez "Activar alarmas sonoras" (el navegador exige un gesto del usuario).

## 6. Accesibilidad

- El modal de la alarma tiene `role="alertdialog"`, atrapa el foco y anuncia el texto con `aria-live="assertive"`.
- Además del sonido, la alarma parpadea el borde de la pantalla (con `prefers-reduced-motion`, borde fijo).
