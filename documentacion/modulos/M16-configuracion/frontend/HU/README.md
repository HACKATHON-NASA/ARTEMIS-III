# M16 · Configuración · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de backend](../../backend/HU/README.md) · [Historias de base de datos](../../database/HU/README.md)

Qué debe **verse** en cada historia de M16. Lo hacen **Gabriela** (HU-16.01 y HU-16.03) y **Jhon** (HU-16.02). Todavía no hay patrón de diseño definitivo: se sigue la [guía visual provisional de M01](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional). Los bocetos indican contenido y orden, no estilo.

## Reglas comunes

- **Páginas de configuración clínica:** cada valor se muestra **con su unidad** (formateador por código del [índice de módulos](../../../README.md)) y **con su fuente**; los parámetros de diseño llevan "†" y el texto "A validar por médicos".
- **Motivo obligatorio** en todo formulario de cambio (campo de texto, ≤ 300 caracteres, con contador). Sin motivo, el botón Guardar está deshabilitado y el servidor también lo rechaza.
- **Solo lectura en la nave:** las páginas de F2 no muestran botones de editar (salvo "Ajuste temporal" en HU-16.03). Los errores `422` del servidor se muestran **junto al campo**, con el mensaje tal cual.
- **Nunca solo color:** el nivel (aviso/crítico), lo inactivo y lo pendiente llevan texto o icono.


## Historias

| Historia | Archivo |
|---|---|
| HU-16.01 · Editar los umbrales de alerta | [HU-16.01-editar-los-umbrales-de-alerta.md](HU-16.01-editar-los-umbrales-de-alerta.md) |
| HU-16.02 · Gestionar el catálogo de exámenes y sus rangos | [HU-16.02-gestionar-el-catalogo-de-examenes-y-sus-rangos.md](HU-16.02-gestionar-el-catalogo-de-examenes-y-sus-rangos.md) |
| HU-16.03 · Ajuste temporal de un umbral durante un apagón | [HU-16.03-ajuste-temporal-de-un-umbral-durante-un-apagon.md](HU-16.03-ajuste-temporal-de-un-umbral-durante-un-apagon.md) |
