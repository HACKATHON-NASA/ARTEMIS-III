# M13 · Alertas y casos clínicos · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de M13: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hace **Gabriela**. Las *props* vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se siguen la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones) de M01.
- **Nivel siempre con icono y texto:** ✖ "Crítico" (el único rojo intenso del sistema) y ⚠ "Aviso". Estado en texto: "Abierta", "Confirmada", "Descartada", "Cerrada".
- **Tripulante:** el código ("T3") o "Toda la tripulación" si es global.
- **La explicación** se muestra con `Explicacion.vue` de [M12](../../../M12-analitica/frontend/HU/HU-12.03-explicacion-de-cada-hallazgo.md), que oculta la parte confidencial a quien no tiene permiso.


## Historias

| Historia | Archivo |
|---|---|
| HU-13.01 · Bandeja de alertas ordenada por gravedad | [HU-13.01-bandeja-de-alertas-ordenada-por-gravedad.md](HU-13.01-bandeja-de-alertas-ordenada-por-gravedad.md) |
| HU-13.02 · Confirmar o descartar una alerta | [HU-13.02-confirmar-o-descartar-una-alerta.md](HU-13.02-confirmar-o-descartar-una-alerta.md) |
| HU-13.03 · Caso clínico con protocolo guiado | [HU-13.03-caso-clinico-con-protocolo-guiado.md](HU-13.03-caso-clinico-con-protocolo-guiado.md) |
| HU-13.04 · Protocolos guiados con fuente y estado de validación clínica | [HU-13.04-protocolos-guiados-con-fuente-y-estado-de-validacion-clinica.md](HU-13.04-protocolos-guiados-con-fuente-y-estado-de-validacion-clinica.md) |
