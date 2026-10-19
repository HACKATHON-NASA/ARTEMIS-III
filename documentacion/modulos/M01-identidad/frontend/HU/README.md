# M01 · Identidad y acceso · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de cada historia de M01: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hacen **Gabriela** (HU-01.01 a HU-01.03) y **Jhon** (HU-01.04). Las rutas y los datos (*props*) vienen de las [historias de backend](../../backend/HU/README.md).


## Historias

| Historia | Archivo |
|---|---|
| HU-01.01 · Inicio de sesión | [HU-01.01-inicio-de-sesion.md](HU-01.01-inicio-de-sesion.md) |
| HU-01.02 · Asignación de roles | [HU-01.02-asignacion-de-roles.md](HU-01.02-asignacion-de-roles.md) |
| HU-01.03 · Tokens de dispositivo | [HU-01.03-tokens-de-dispositivo.md](HU-01.03-tokens-de-dispositivo.md) |
| HU-01.04 · Auditoría de accesos | [HU-01.04-auditoria-de-accesos.md](HU-01.04-auditoria-de-accesos.md) |
| HU-15.04 · Token de la PWA (trabajo de M01 dentro de esa historia) | [HU-15.04-token-de-la-pwa.md](HU-15.04-token-de-la-pwa.md) |

---

## Guía visual provisional

Todavía **no hay un patrón de diseño** (colores, tipografía y marca). Hasta que exista, las pantallas de M01 siguen estas reglas para que después se puedan "vestir" sin rehacerlas.

| Elemento | Regla provisional |
|---|---|
| Componentes | Los que trae el *starter kit* (botones, campos, diálogos, menús) y los compartidos de HU-00.03 (`EstadoVacio`, `Cargando`, `InsigniaAlerta`). **No** se crean botones ni campos propios |
| Color | Solo la escala neutra de Tailwind (`neutral-*`). Un único color de acento para la acción principal, definido como **variable CSS** (`--color-acento`) en `resources/css/app.css`, para cambiarlo después en un solo lugar. Rojo **solo** para errores y acciones destructivas (desactivar, revocar) |
| Tipografía | La del *starter kit*. Tamaño base 16 px; títulos de página `text-2xl`; tablas `text-sm` |
| Espaciado | Múltiplos de 4 px (escala de Tailwind). Márgenes de página: 16 px en móvil y 24 px desde tableta |
| Tamaños de pantalla | **Tableta** (≥ 768 px) y **escritorio** (≥ 1024 px) son los principales. En móvil (< 768 px) las tablas se convierten en tarjetas apiladas. Nunca hay desplazamiento horizontal de la página |
| Tema | Claro y oscuro con las variantes `dark:` del *starter kit* |
| Iconos | Los del *starter kit* (Lucide). Todo icono que transmite un estado va acompañado de texto |
| Textos | En español, con las mayúsculas normales de una frase ("Emitir token", no "EMITIR TOKEN"). Los textos de esta guía son **exactos** |
| Fechas | `D12 · 14:05 UTC` cuando hay misión; si no hay misión (Sprint 0), `2026-10-16 14:05 UTC` |
| Accesibilidad | Contraste AA, foco visible, objetivos táctiles de 44 px, formularios usables solo con teclado, errores anunciados con `aria-live="polite"` |
| Rutas de las páginas | `resources/js/modules/<Modulo>/Pages/...`, como dice cada ficha ([ARCHITECTURE §16](../../../00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)) |

**Nombres de los roles en pantalla** (constante `ROLES` de `resources/js/contratos/roles.ts`, que Gabriela escribe en el Sprint 0 a partir del [contrato §9.1](../../../../scrum/plan-de-sprints.md#91-valores-enumerados)):

| Valor | Texto |
|---|---|
| `astronauta` | Astronauta |
| `cmo` | Oficial médico (CMO) |
| `medico_vuelo` | Médico de vuelo |
| `ingeniero_biomedico` | Ingeniería biomédica |
| `investigador` | Investigación |
| `admin` | Administración |

### Estructura común de las cuatro aplicaciones

Las cuatro aplicaciones (`AstronautaLayout`, `CmoLayout`, `TierraLayout` y `AdminLayout`) comparten la misma estructura. Cada módulo añade después sus elementos (por ejemplo, el indicador de sincronización y la alarma crítica de F1).

```
Tableta y escritorio (≥ 768 px)
┌───────────────────────────────────────────────────────────────────────┐
│ AstroHealth · <Nombre de la app>        [● Nave]   Ana Pérez ▾       │  ← cabecera fija, 56 px
├──────────────┬────────────────────────────────────────────────────────┤
│ Inicio       │  <Título de la página>                                 │
│ Usuarios     │                                                        │
│ Tokens       │  <contenido>                                           │
│ Auditoría    │                                                        │
│              │                                                        │
│ (menú, 240px)│                                                        │
└──────────────┴────────────────────────────────────────────────────────┘

Móvil (< 768 px)
┌──────────────────────────────┐
│ ☰  AstroHealth      [● Nave] │   ← el menú se abre como panel lateral
├──────────────────────────────┤
│ <Título>                     │
│ <contenido>                  │
└──────────────────────────────┘
```

| Elemento | Contenido |
|---|---|
| Nombre de la app | F1 "Mi salud", F2 "Tripulación", F3 "Centro médico Tierra", F4 "Administración" |
| Insignia del nodo | Icono + texto "Nave" o "Tierra" (de la *prop* `nodo`). Siempre visible, porque cada rol solo trabaja en un nodo |
| Día de misión | "Día 12 de misión" junto a la insignia, con `<DiaMision formato="largo" />` de M02, si `mision` no es `null`. En el Sprint 0 no aparece |
| Menú del usuario (▾) | Nombre, rol en texto, enlace a "Ajustes" (del *starter kit*, solo el cambio de contraseña: borrar cuenta y editar correo se quitan en HU-01.01) y "Cerrar sesión" |
| Menú lateral | Generado desde `resources/js/navegacion.ts`: lista de `{ texto, ruta, gate, app }`. Solo muestra las entradas cuyo *Gate* está en `auth.permisos` (el frontend oculta; el backend prohíbe). La entrada de la página actual se marca con `aria-current="page"` y con un estilo distinto que no depende solo del color |
| Cambio de aplicación | El CMO ve en su menú "Mi salud" (F1) y "Tripulación" (F2). El médico de vuelo ve "Centro médico Tierra" (F3) y "Auditoría" (F4) |
