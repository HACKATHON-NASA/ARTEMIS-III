# M15 · Sincronización · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de M15: la cola offline de la PWA, el panel del enlace y las recomendaciones. Lo hacen **Jhon** (HU-15.04 y HU-15.02) y **Gabriela** (HU-15.03). HU-15.01 no tiene pantalla. Las *props* vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se siguen la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones) de M01.
- **La cola offline es código compartido** (`resources/js/offline/`): todos los módulos la usan con `encolar(endpoint, cuerpo)`. Ninguna página escribe en IndexedDB por su cuenta.
- **Los tokens nunca van a `localStorage`, `sessionStorage` ni a la consola**: solo a IndexedDB (`credenciales`).


## Historias

| Historia | Archivo |
|---|---|
| HU-15.04 · La app funciona sin conexión | [HU-15.04-la-app-funciona-sin-conexion.md](HU-15.04-la-app-funciona-sin-conexion.md) |
| HU-15.02 · Sincronizar sin pérdidas tras un corte de enlace | [HU-15.02-sincronizar-sin-perdidas-tras-un-corte-de-enlace.md](HU-15.02-sincronizar-sin-perdidas-tras-un-corte-de-enlace.md) |
| HU-15.03 · Enviar una recomendación a la nave | [HU-15.03-enviar-una-recomendacion-a-la-nave.md](HU-15.03-enviar-una-recomendacion-a-la-nave.md) |
