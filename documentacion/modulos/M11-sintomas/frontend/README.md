# M11 · Síntomas · Frontend

> [← Ficha del módulo](../README.md) · [Backend](../backend/README.md) · **Frontend** · [Base de datos](../database/README.md)

Capa **Inertia + Vue 3 + Tailwind CSS v4** del módulo. Los datos llegan como *props* desde los controladores web del [backend](../backend/README.md); las capturas sin conexión usan la cola offline de [M15](../../M15-sincronizacion/frontend/README.md).

---

## 1. Páginas Inertia

| App | Ruta web | Componente | Roles | Sin conexión |
|---|---|---|---|:---:|
| F1 | `/astronauta/sintomas/registrar` | `Astronauta/Sintomas/Create` (cuadrícula de síntomas + deslizador 0–10; máximo 3 toques; botón opcional "Añadir foto") | `astronauta`, `cmo` | ✅ |
| F1 | `/astronauta/sintomas/mareo` | `Astronauta/Sintomas/Mareo` (aparece en el inicio los días 1 a 3) | `astronauta`, `cmo` | ✅ |
| F2 | `/cmo/tripulantes/{codigo}` | Pestaña "Síntomas" de `Cmo/Tripulante/Show` | `cmo` | — |
| F3 | `/tierra/tripulantes/{codigo}/sintomas` | `Tierra/Sintomas/Show` (mismo componente que la pestaña del CMO) | `medico_vuelo` | — |

## 2. Archivos

```
resources/js/modules/Sintomas/Pages/Astronauta/Sintomas/{Create,Mareo}.vue
resources/js/modules/Sintomas/Pages/Tierra/Sintomas/Show.vue
resources/js/offline/comprimirImagen.ts
```

## 3. Props de cada página

```ts
// Astronauta/Sintomas/Create
{ catalogo: { codigo: string, nombre: string }[],   // el icono lo pone el frontend (contratos/sintomas.ts)
  recientes: { tipo: string, intensidad: number, inicio_en: string, tieneFoto: boolean }[],
  fotoMaxKb: number }                                 // adjunto_foto_max_kb de M16 (500)

// Astronauta/Sintomas/Mareo
{ diaMision: number, respondidosAntes: { dia_mision: number, total: number }[] }   // días 1 a mareo_dias (M16)

// Cmo/Tripulante/Show — pestaña "Síntomas"
{ sintomas: { id: string, tipo: string, nombre: string, intensidad: number, inicio_en: string, notas: string | null,
              foto: { url: string, ancho: number, alto: number } | null }[],
  mareo: { dia_mision: number, total: number }[] }
```

El catálogo se guarda en la instantánea offline para registrar sin red.

| Página | Acciones |
|---|---|
| `Astronauta/Sintomas/Create` | Toque 1: síntoma de la cuadrícula; toque 2: intensidad (11 botones 0–10); toque 3: guardar. "Añadir foto" es opcional y no cuenta en los 3 toques. Se encolan el síntoma y, después, la foto |
| `Astronauta/Sintomas/Mareo` | Cinco deslizadores 0–10 y guardar; se encola |

## 4. Componentes propios

| Componente | Uso |
|---|---|
| `Sintomas/CuadriculaSintomas.vue` | Botones grandes con icono y nombre |
| `Sintomas/CapturaFoto.vue` | `<input type="file" accept="image/*" capture="environment">`, vista previa y botón quitar |
| `offline/comprimirImagen.ts` | Redimensiona con `<canvas>` (lado mayor 1.280 px) y exporta JPEG 0,8; baja la calidad hasta quedar en ≤ `fotoMaxKb` (hoy 500 KB) |

## 5. Comportamiento

- La foto se guarda como `Blob` en IndexedDB; si el dispositivo se queda sin espacio, se avisa y el síntoma se guarda sin foto.
- El cuestionario de mareo aparece como tarjeta en el inicio de F1 (*prop* `mareoPendiente`) los días 1 a `mareo_dias` (hoy 3) mientras no se haya respondido.

## 6. Accesibilidad

- La intensidad se elige con 11 botones 0–10 (más fáciles que un deslizador con guantes o con poca motricidad fina). Bocetos y textos en las [historias de frontend](HU/README.md).
- La foto tiene texto alternativo con el síntoma y la fecha.

## 7. Pruebas mínimas (Vitest)

- [ ] Una foto de 4 MB queda en ≤ 500 KB tras comprimirla (Vitest).
