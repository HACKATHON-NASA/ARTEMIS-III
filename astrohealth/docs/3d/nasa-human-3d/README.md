# Figura humana 3D con nueve puntos interactivos — Vue 3

Incluye **solo el visor central**: modelo humano de silicona, rotación, zoom, puntos de riesgos espaciales y selección. No hay dashboard ni panel lateral.

## Instalación en Laravel + Vue 3

```bash
npm install three
```

Copia `src/components/HumanSpaceBody.vue` a `resources/js/components/HumanSpaceBody.vue` de tu proyecto.

## MUY IMPORTANTE: descargar el modelo real GLB

Este ZIP no incluye el archivo binario (7–8 MB); descárgalo desde el proyecto Open Anatomy 3D:

https://cdn.jsdelivr.net/npm/open-anatomy-3d@0.1.0/models/anatomy-male.glb

Guarda el archivo en `public/models/anatomy-male.glb` de tu aplicación Laravel.

El archivo debe quedar servido en `/models/anatomy-male.glb`.

Si falta, verás un aviso de error; no se sustituye el cuerpo por cubos ni esferas.

## Uso

```vue
<script setup>
import HumanSpaceBody from '@/components/HumanSpaceBody.vue'
function riskSelected(risk) {
  console.log('Riesgo seleccionado', risk)
  // { id, label, risk } o null si se ha deseleccionado
}
</script>

<template>
  <HumanSpaceBody @select="riskSelected" />
</template>
```

## Qué incluye

- Figura 3D real y orgánica con iluminación satinada estilo silicona
- 9 marcadores luminosos pulsantes y clicables alrededor de la figura
- Giro automático lento, rotación con el mouse y zoom
- Botones de giro/pausa, zoom y restaurar vista
- Emisión de evento `select` para integrar con el dashboard
- Responsive y preferencia de movimiento reducido

## Límites

- El modelo es educativo; **no** es una reconstrucción del diseño conceptual de la imagen al 100 %.
- Algunos hotspots representan riesgos globales (sueño, radiación, nutrición, inmunidad), no un órgano anatómico aislado.
- El mapa de posiciones puede necesitar pequeños ajustes visuales dependiendo de la orientación del modelo GLB.
- El visor usa `three` y WebGL; no se ha verificado ejecutándolo dentro del proyecto Laravel particular.

## Atribución y licencia

La malla viene de Open Anatomy 3D de MervinR07, derivada de BodyParts3D (DBCLS), Human Reference Atlas/HuBMAP y MakeHuman, bajo CC BY 4.0. Debes mostrar los créditos apropiados en tu aplicación.

Fuente y condiciones: https://github.com/MervinR07/open-anatomy-3d

El componente Vue provisto aquí puede adaptarse libremente.
