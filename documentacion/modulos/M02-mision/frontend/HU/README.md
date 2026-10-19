# M02 · Misión y tripulación · Historias de frontend

> [← Ficha del módulo](../../README.md) · [Ficha de frontend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de backend](../../backend/HU/README.md)

Trabajo de **Inertia + Vue 3 + Tailwind CSS v4** de cada historia de M02: qué páginas hay, **cómo deben verse**, qué textos llevan y cómo se comportan. Lo hacen **Gabriela** (HU-02.01 y HU-02.02) y **Jhon** (HU-02.03). Las rutas y los datos vienen de las [historias de backend](../../backend/HU/README.md).

## Reglas comunes

- Se sigue la [guía visual provisional](../../../M01-identidad/frontend/HU/README.md#guía-visual-provisional) de M01 (componentes del *starter kit*, colores neutros, un acento en `--color-acento`, tabla en tableta y escritorio, tarjetas en móvil) y la [estructura común de las aplicaciones](../../../M01-identidad/frontend/HU/README.md#estructura-común-de-las-cuatro-aplicaciones).
- Todas las páginas de M02 **requieren conexión** (F3 y F4 no trabajan sin red).
- **Modo solo lectura** (*prop* `soloLectura: true`, nodo Tierra): arriba de la página aparece el aviso "Solo lectura: esta información se edita en la nave.", con icono ℹ. Los campos se muestran como texto (no como campos deshabilitados) y desaparecen los botones de crear, editar y guardar.
- Los errores `422` aparecen bajo cada campo (`useForm`). Los avisos de éxito y de error llegan en `flash.exito` y `flash.error` y se muestran como aviso temporal (5 s) con `aria-live="polite"`.
- Los menús muestran cada página solo si el usuario tiene su *Gate* (`gestionar-mision`, `registrar-dispositivos`, `registrar-linea-base`) en `auth.permisos`.

**Textos fijos de los valores enumerados** (`resources/js/contratos/mision.ts`):

| Valor | Texto |
|---|---|
| `leo` | Órbita baja (ISS, Artemis III) |
| `luna` | Luna |
| `marte` | Marte |
| `F` · `M` | Femenino · Masculino |
| `wearable` | Camiseta biométrica (wearable) |
| `oximetro` | Oxímetro |
| `tensiometro` | Tensiómetro |
| `actigrafo` | Actígrafo |
| `dosimetro` | Dosímetro |
| `cgm` | Monitor continuo de glucosa |
| `medidor_masa` | Medidor de masa corporal |
| `monitor_ambiental` | Monitor ambiental |
| `tableta` | Tableta |
| `simulador` | Simulador |


## Historias

| Historia | Archivo |
|---|---|
| HU-02.01 · Crear una misión con su perfil de enlace | [HU-02.01-crear-una-mision-con-su-perfil-de-enlace.md](HU-02.01-crear-una-mision-con-su-perfil-de-enlace.md) |
| HU-02.02 · Registrar tripulantes y dispositivos | [HU-02.02-registrar-tripulantes-y-dispositivos.md](HU-02.02-registrar-tripulantes-y-dispositivos.md) |
| HU-02.03 · Registrar la línea base previa al vuelo | [HU-02.03-registrar-la-linea-base-previa-al-vuelo.md](HU-02.03-registrar-la-linea-base-previa-al-vuelo.md) |
