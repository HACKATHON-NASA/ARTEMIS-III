# M08 · Ambiente · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Refresco |
|---|---|---|---|---|
| F2 | `/cmo/ambiente` | `Cmo/Ambiente/Index` (una tarjeta por módulo y gráficas de 24 h de pCO₂, VOC, partículas y ruido) | `cmo` | Sondeo 10 s |
| F3 | `/tierra/ambiente` | `Tierra/Ambiente/Index` (tendencia de 7 y 30 días) | `medico_vuelo` | — |

Las gráficas de VOC, partículas y ruido muestran su línea de límite solo si la regla `AMB_*` correspondiente está activa, con la fuente en la leyenda.

Las gráficas de pCO₂ muestran dos líneas de referencia de `referenciasPco2`: el límite operativo (hoy **3,0 mmHg**) y el objetivo del promedio de 7 días (hoy **2,5 mmHg**) ([Fase 1 §3.2](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#32-ambiente-y-radiación)).

## 2. Archivos

```
resources/js/modules/Ambiente/Pages/Cmo/Ambiente/Index.vue
resources/js/modules/Ambiente/Pages/Tierra/Ambiente/Index.vue
```

## 3. Props de cada página

```ts
// Cmo/Ambiente/Index
{
  modulos: {
    codigo: string, nombre: string,
    actual: Record<'AH-PCO2' | 'AH-VOC' | 'AH-PM' | 'AH-RUIDO', { valor: number, medido_en: string } | null>,
    estado: { promedio_ultima_hora: number | null, sobre_limite: boolean },   // la tarjeta usa esto, no `actual`
    promedioHorarioMax24h: number | null,             // pCO₂
    promedio7d: number | null,                        // pCO₂
    series24h: Record<string, { t: string, valor: number }[]>     // pCO₂ por hora; VOC, PM y ruido cada 5 min
  }[],
  limites: Record<'AH-VOC' | 'AH-PM' | 'AH-RUIDO', { valor: number, fuente: string } | null>,   // solo si AMB_* está activa
  referenciasPco2: { limite: number, objetivo7d: number }   // CO2_ALTO y co2_objetivo_7d_mmhg de M16 (hoy 3,0 y 2,5)
}

// Tierra/Ambiente/Index
{ modulos: { codigo: string, nombre: string,
             diarios30: Record<string, { fecha: string, media: number, maximo: number }[]> }[],
  limites: …, referenciasPco2: … }
```

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Ambiente/TarjetaModulo.vue` | Nombre del módulo, pCO₂ actual con estado ("Normal" o "Sobre el límite operativo (3,0)") **según el promedio de la última hora**, no la última lectura, y mini serie. Bocetos en las [historias de frontend](HU/README.md) |
| `Ambiente/GraficaAmbiente.vue` | `GraficaSerie` con las líneas de `referenciasPco2` (hoy 3,0 y 2,5 mmHg); para VOC, PM y ruido dibuja el límite **solo** si llega en `limites`, con la fuente en la leyenda |

## 5. Comportamiento

- `usePoll(10000, { only: ['modulos'] })` en F2.
- Si una regla `AMB_*` está inactiva, la gráfica muestra debajo "Sin límite configurado".
- El ambiente es del sistema `ambiente`: solo F2 y F3 lo muestran en el semáforo.

## 6. Accesibilidad

- Cada tarjeta expresa el estado con texto e icono además del color.
