# 🧍 M3 · Cuerpo humano interactivo · Frontend

> [← Frontend](../README.md) · Código: `astrohealth/resources/js/modules/Cuerpo/` · Qué construir: [ficha de M3](../../../../documentacion/modulos/M3-cuerpo/README.md#6-frontend)

| Estado                                | Fase | Responsable |
| ------------------------------------- | :--: | ----------- |
| 🟨 Frontend listo con datos de prueba |  3   | Frontend    |

Este documento cuenta **cómo quedó hecho**. **Qué** hay que construir está en la ficha: aquí no se copia, se enlaza.

## Avance

**Principal:** `modules/Cuerpo/Home.vue` (`/inicio`): compone el cuerpo (M3) con el puntaje (M2), las alertas (M5), la rutina (M8) y la misión (M6).

**Componentes**

- [x] `BodyViewer`: elige el 3D o, si no hay WebGL o el modelo no carga, el SVG
- [x] `Body3D` + `Composables/useBodyScene.ts`: el cuerpo en 3D (modelo anatómico real)
- [x] `BodyMap` + `BodyFigure` + `BodyZone`: el cuerpo en SVG (respaldo)
- [x] `ZoneChips`: las 9 zonas como botones (teclado, lector de pantalla y zonas sin órgano en el 3D)
- [x] `BodyTooltip`, `BodyPartPanel` (función, valores, riesgo NASA HRP, qué hacer), `VitalsGrid`, `HomeGreeting`

**Store**

- [x] `useBodyStore` (⚠️ datos de prueba en `Utils/bodyMockData.ts`, con la forma de `GET /body-parts` y `GET /astronauts/{id}/body-status`)

## El cuerpo en 3D

- **Modelo:** `public/models/anatomy-male.glb` (7,6 MB), de [open-anatomy-3d](https://github.com/MervinR07/open-anatomy-3d) 0.1.0. Usa compresión `EXT_meshopt_compression`, por eso el cargador lleva `MeshoptDecoder`.
- **Qué se ve:** la piel como silicona azul translúcida y, adentro, **los órganos reales de cada zona pintados con su estado** (`Utils/bodyModel.ts`):

| Zona         | Piezas del modelo                               |
| ------------ | ----------------------------------------------- |
| `heart`      | heart                                           |
| `brain`      | brain, cerebellum, brainstem                    |
| `eyes`       | eye_l, eye_r                                    |
| `lungs`      | lung_l, lung_r, bronchi, trachea                |
| `immune`     | thymus, spleen                                  |
| `metabolism` | liver, pancreas, stomach                        |
| `bones`      | femur, lower_leg, patella (izq. y der.), pelvis |
| `ear`        | sin malla: un marcador junto al cráneo          |
| `whole_body` | la piel (se elige desde los botones de zona)    |

- **Movimiento:** las zonas en amarillo o rojo laten (brillo). No gira ni flota sola: se arrastra para girar y la rueda acerca. Con "reducir movimiento", sin latido.
- **Carga diferida:** Three.js y el modelo se descargan solo en `/inicio` (`defineAsyncComponent`), en un archivo aparte.
- **Colores:** salen de `--status-*` de `app.css` mediante `statusHex()` de `lib/status.ts`.

### ⚖️ Atribución obligatoria (CC BY 4.0)

La pantalla muestra el crédito debajo del cuerpo. Si se usa el modelo en otro lugar (video, diapositivas, README), va esta línea:

> Anatomía: BodyParts3D © The Database Center for Life Science (CC BY 4.0). Modificado por open-anatomy-3d (https://github.com/MervinR07/open-anatomy-3d), CC BY 4.0.

El modelo es educativo: no sirve para diagnóstico.

## Accesibilidad y animaciones

- [x] Funciona con el teclado (botones de zona en el 3D; zonas enfocables en el SVG)
- [x] Respeta `prefers-reduced-motion`
- [x] Colores del semáforo desde `lib/status.ts`

## Diferencias con la ficha

- La ficha pedía un SVG propio; el cuerpo principal es **3D con un modelo anatómico real**. El SVG queda como respaldo.
- La propuesta original de ChatGPT (en `docs/3d/`, como referencia) ponía 9 puntos de colores fijos que no seguían el semáforo y giraba sola; aquí se pintan los órganos según su estado y con nuestras 9 zonas.

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
