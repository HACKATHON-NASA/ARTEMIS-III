# M05 · Psicología y cognición · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Sin conexión |
|---|---|---|---|:---:|
| F1 | `/astronauta/tests` | `Astronauta/Tests/Index` (pendientes de hoy y recordatorios) | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/tests/pvt` | `Astronauta/Tests/Pvt` | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/tests/aritmetica` | `Astronauta/Tests/Aritmetica` | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/tests/memoria` | `Astronauta/Tests/Memoria` | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/como-me-siento` | `Astronauta/Escalas/Create` | `astronauta`, `cmo` | ✅ |
| F3 | `/tierra/tripulantes/{codigo}/psicologia` | `Tierra/Psicologia/Show` | `medico_vuelo` | — |

El test PVT ocupa la pantalla completa, sin menús ni notificaciones, durante `duracionS` (`pvt_duracion_s`, hoy 3 minutos).

## 2. Archivos

```
resources/js/modules/Psicologia/Pages/Astronauta/Tests/{Index,Pvt,Aritmetica,Memoria}.vue
resources/js/modules/Psicologia/Pages/Astronauta/Escalas/Create.vue
resources/js/modules/Psicologia/Pages/Tierra/Psicologia/Show.vue
```

## 3. Props de cada página

```ts
// Astronauta/Tests/Index
{
  pendientesHoy: { tipo: 'pvt' | 'aritmetica' | 'memoria' | 'escalas_diarias' | 'escalas_semanales',
                   hora_utc: string, estado: 'hecho' | 'pendiente' | 'atrasado' }[],
  programacion: { tipo: string, hora_utc: string, dia_semana: number | null }[],  // para el estado sin conexión
  atrasoMin: number                                                               // 60: "Atrasado" pasado ese tiempo
}   // el astronauta no ve sus métricas: solo "Hecho"

// Astronauta/Tests/Pvt
{ duracionS: number /* 180 */, intervaloMinS: number /* 2 */, intervaloMaxS: number /* 10 */, esperaMaxS: number /* 30 */ }

// Astronauta/Tests/Aritmetica
{ items: number /* 20 */ }

// Astronauta/Tests/Memoria
{ longitudInicial: number /* 3 */, msPorElemento: number /* 1000 */, longitudTope: number /* 12 */ }

// Astronauta/Escalas/Create
{ semanalPendiente: boolean }       // true si faltan carga de trabajo o cohesión en la semana ISO actual

// Tierra/Psicologia/Show
{
  tripulante: { codigo: string, nombre: string },
  metricas: Record<string /* AH-PVT-MEDIANA … AH-MEM-SPAN */, { sesion: number, completada_en: string, valor: number, z: number | null }[]>,
  lineasBase: Record<string, { media: number, desviacion_efectiva: number } | null>,
  escalas: Record<'animo' | 'estres' | 'fatiga' | 'carga_trabajo' | 'cohesion', { fecha: string, valor: number }[]>,
  alertas: { nivel: 'aviso' | 'critico', creada_en: string, estado: string }[]   // COG_DETERIORO del periodo (M13)
}
```

Los parámetros de los tests vienen de `ParametrosMision` (M16) y se guardan en la instantánea offline: los tests funcionan sin red.

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Tests/MotorPvt.ts` | Lógica pura del PVT: programa el estímulo con un intervalo aleatorio, mide con `performance.now()`, detecta falsas salidas. Sin dependencias de Vue (se prueba con Vitest) |
| `Tests/MotorAritmetica.ts` | Genera 20 operaciones de dos cifras con resultado ≥ 0 |
| `Tests/MotorMemoria.ts` | Genera secuencias sin dígitos repetidos seguidos y aplica la regla de parada (dos errores seguidos en la misma longitud) |
| `Tests/PantallaCompleta.vue` | Contenedor del PVT: pide pantalla completa y oculta el menú y los avisos temporales durante `duracionS`. **No** oculta la alarma crítica (M13): si llega, el test se cancela como incompleto y se muestra la alarma |
| `Escalas/DeslizadorEscala.vue` | Deslizador 0–10 con etiquetas en los extremos ("Muy mal" / "Muy bien") |

## 5. Comportamiento

1. Al terminar un test, la página arma la sesión completa (`id`, `inicio`, `items[]`) y la **encola** (`/api/v1/psicologia/sesiones`); la pantalla final muestra "Test guardado" aunque no haya red.
2. Las **métricas las calcula el servidor**: la pantalla final no muestra un resultado "oficial", solo "Hecho".
3. Si el tripulante sale del PVT antes de `duracionS` (hoy 180 s), la sesión se envía con `completa = false`.
4. El PVT ignora toques repetidos en menos de 100 ms del mismo evento (rebote de la pantalla).
5. La memoria muestra cada dígito durante `msPorElemento` con 250 ms en blanco entre dígitos y después un teclado numérico grande.

Bocetos, textos y criterios de cada pantalla: [historias de frontend](HU/README.md).

## 6. Accesibilidad

- El estímulo del PVT es un contador grande y de alto contraste en el centro; responde a toque, clic y barra espaciadora.
- Los tests no dependen del color. El PVT no es apto para lectores de pantalla (es una prueba visual de tiempo de reacción); se indica en la pantalla de inicio.

## 7. Pruebas mínimas (Vitest)

- [ ] `MotorPvt`: un toque antes del estímulo se registra como falsa salida; un TR de 650 ms queda registrado como tal.
- [ ] `MotorMemoria`: la secuencia nunca repite el mismo dígito dos veces seguidas; dos errores en la longitud 7 terminan el test.
- [ ] Con la red desactivada, terminar un test deja un elemento en `cola_envios`.
