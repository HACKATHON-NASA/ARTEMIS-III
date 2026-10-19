# 5 · Frontend con Vue e Inertia

> [← Guía](README.md) · Anterior: [API, JSON y errores](04-api-y-errores.md) · Siguiente: [Base de datos y migraciones](06-base-de-datos.md)

El frontend es **Vue 3 con TypeScript**, servido por **Inertia** y con estilos de **Tailwind v4**. El proyecto no usa Angular ni otra SPA aparte. Las reglas generales de pantalla (fechas, unidades, accesibilidad, *props* compartidas) están en las [convenciones del frontend](../README.md#convenciones-del-frontend), y la guía visual en las [historias de frontend de M01](../M01-identidad/frontend/HU/README.md#guía-visual-provisional).

## 1. Cómo funciona Inertia (lo esencial)

No hay una API para alimentar las pantallas. El controlador de Laravel devuelve **la página y sus datos** de una vez:

```php
return Inertia::render('Astronauta/Sintomas/Index', ['sintomas' => SintomaResource::collection($lista)]);
```

Vue recibe `sintomas` como *prop* de la página. Navegar, enviar formularios y refrescar se hace con el `router` y `useForm` de Inertia, que hablan con las rutas **web** de Laravel. Por eso:

- **No se usa** `fetch` ni `axios` para traer datos de una pantalla: los datos llegan como *props*.
- La única parte del frontend que llama a `/api/v1/...` es la **cola offline** de M15 (`offline/`), con el token de la PWA.

## 2. Estructura de carpetas

El frontend está **organizado por módulos**, igual que el backend ([ARCHITECTURE §16](ARCHITECTURE.md#16-modularización-del-frontend)):

```
resources/js/
├── app.ts                       # Resolver de páginas: busca cada nombre en modules/*/Pages y en pages/
├── modules/
│   ├── Sintomas/                # Mismo nombre que app/Modules/Sintomas
│   │   ├── Pages/               # Sus páginas, con el nombre de la ficha
│   │   │   ├── Astronauta/Sintomas/Create.vue      → página 'Astronauta/Sintomas/Create'
│   │   │   └── Tierra/Sintomas/Show.vue            → página 'Tierra/Sintomas/Show'
│   │   ├── Components/          # Solo de este módulo: SintomasTabla.vue, SelectorIntensidad.vue
│   │   ├── Composables/
│   │   ├── Types/               # Tipos de las props de sus páginas
│   │   └── Utils/
│   └── …                        # Uno por módulo, solo con las carpetas que necesite
├── pages/                       # Páginas que no son de ningún módulo: errores y catálogo de desarrollo
├── layouts/                     # AstronautaLayout, CmoLayout, TierraLayout, AdminLayout
├── components/
│   ├── ui/                      # Botones, campos, diálogos, tablas… (del kit, shadcn-vue)
│   └── EstadoVacio.vue …        # Los 6 de HU-00.03 y los que usa más de un módulo (DiaMision, IndicadorSincronizacion…)
├── composables/                 # Globales: usePermisos
├── lib/                         # Funciones puras globales: formato.ts
├── contratos/                   # Tipos y valores compartidos con el backend: roles.ts, semaforo.ts
├── offline/                     # Cola offline de M15: infraestructura que usan todos los módulos
└── types/                       # Tipos globales: props compartidas de Inertia
```

Las páginas del *starter kit* (inicio de sesión y ajustes) pasan a `modules/Identidad/Pages/`. Las fichas de cada módulo escriben las rutas completas de sus archivos (`frontend/README.md` §2).

| Necesito… | Va en |
|---|---|
| Una pantalla nueva | `modules/<Modulo>/Pages/<App>/<Recurso>/<Index|Show|Create|Edit>.vue`, con el nombre exacto de la ficha |
| Una pieza de un solo módulo | `modules/<Modulo>/Components/` |
| Una pieza que usan dos o más módulos | `components/` (se **mueve** ahí; un módulo nunca importa de otro) |
| Un botón, campo, diálogo, tabla o pestaña | **No se crea:** `components/ui/` del kit |
| Lógica de Vue reutilizable (`ref`, `computed`, ciclo de vida) | `modules/<Modulo>/Composables/` o, si es global, `composables/` |
| Un cálculo o formato sin Vue | `modules/<Modulo>/Utils/` o, si es global, `lib/` |
| Los tipos de las *props* de una página | `modules/<Modulo>/Types/` |
| Un tipo o lista de valores que también existe en el backend | `contratos/` |

## 3. Anatomía de una página

```vue
<!-- modules/Sintomas/Pages/Astronauta/Sintomas/Index.vue -->
<script setup lang="ts">
import { computed } from 'vue'
import { useForm, usePoll } from '@inertiajs/vue3'
import AstronautaLayout from '@/layouts/AstronautaLayout.vue'
import EstadoVacio from '@/components/EstadoVacio.vue'
import { fechaUtc } from '@/lib/formato'

defineOptions({ layout: AstronautaLayout })

const props = defineProps<{
  sintomas: { id: string, tipo: string, intensidad: number, inicio_en: string }[]
  catalogo: { codigo: string, nombre: string }[]
}>()

const recientes = computed(() => props.sintomas.slice(0, 10))

usePoll(10_000, { only: ['sintomas'] })   // solo si la ficha pide refresco
</script>

<template>
  <h1 class="text-2xl">Mis síntomas</h1>
  <EstadoVacio v-if="!recientes.length" texto="Todavía no has registrado síntomas." />
  <ul v-else>
    <li v-for="s in recientes" :key="s.id">{{ s.tipo }} · {{ s.intensidad }} · {{ fechaUtc(s.inicio_en) }}</li>
  </ul>
</template>
```

Reglas:

1. **Las *props* se tipan** con `defineProps<{...}>()`, copiando la forma que dice la ficha del módulo (`frontend/README.md` §3).
2. **El *layout*** se declara con `defineOptions({ layout })`, nunca envolviendo el contenido a mano.
3. **Nada de estado global** (no hay Pinia): lo que viene del servidor son *props*; lo de la pantalla, `ref` y `computed`.
4. **Sin reglas de negocio en el frontend.** Los umbrales, rangos y textos clínicos llegan en las *props*. El frontend **esconde** lo que no se puede hacer; el backend **lo prohíbe**.
5. **Textos exactos**: los que dice la historia, en español.

## 4. Formularios

**Con conexión** (pantallas de F2, F3 y F4): `useForm`.

```ts
const form = useForm({ name: '', email: '', rol: 'astronauta' })
form.post('/admin/usuarios', { onSuccess: () => cerrarPanel() })
// form.errors.email → el error 422 bajo el campo; form.processing → botón deshabilitado
```

**Sin conexión** (capturas de F1): el `id` se genera **antes** de guardar y el registro va a la cola de M15, que lo envía cuando hay red.

```ts
const id = crypto.randomUUID()
await encolar('/api/v1/sintomas', { id, tipo, intensidad, inicio_en: new Date().toISOString(), notas: null })
// La pantalla dice "Guardado en el dispositivo"
```

## 5. Componentes

- Una página con varias responsabilidades (filtros, tabla, formulario, diálogo, resumen) se divide en un componente por responsabilidad dentro de `modules/<Modulo>/Components/`; la página solo los coordina.
- Primero se busca en `components/ui` (kit) y en los compartidos de [HU-00.03](../../scrum/fundaciones/HU-00.03-libreria-de-componentes.md). El catálogo `/dev/componentes` (local) los muestra todos.
- Un componente **recibe *props* y emite eventos** (`defineEmits`); no llama al servidor.
- Colores solo con las variables de `app.css` (`--color-critico`…), nunca `bg-red-600` a mano. El color **nunca** va solo: siempre icono y texto.
- Nombres de archivo en `PascalCase.vue`; composables `useAlgo.ts`; funciones en `camelCase`.

## 6. Pruebas (Vitest)

Cada historia de frontend lista sus pruebas. Se prueban la lógica y el comportamiento, no el HTML exacto:

```ts
import { mount } from '@vue/test-utils'
import Semaforo from '@/components/Semaforo.vue'

it('muestra el texto además del color', () => {
  const w = mount(Semaforo, { props: { estados: { corazon: 'rojo' } } })
  expect(w.text()).toContain('Crítico')
})
```

`npm run test` corre todas; la CI las ejecuta en cada *pull request*.
