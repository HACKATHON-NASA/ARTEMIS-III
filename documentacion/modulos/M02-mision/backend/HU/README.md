# M02 · Misión y tripulación · Historias de backend

> [← Ficha del módulo](../../README.md) · [Ficha de backend](../README.md) · [Historias de base de datos](../../database/HU/README.md) · [Historias de frontend](../../frontend/HU/README.md)

Trabajo de **Laravel** (rutas, validaciones, contratos, casos de uso y pruebas Pest) de cada historia de M02. Lo hace **Julieth**. Las tablas y los *seeders* están en las [historias de base de datos](../../database/HU/README.md); las pantallas, en las [historias de frontend](../../frontend/HU/README.md).

## Reglas comunes

- **M02 no tiene API REST.** Sus pantallas usan rutas **web** de Inertia (sesión y CSRF). Los demás módulos usan sus **contratos** (`Mision\Contratos\…`).
- **La nave es la fuente de verdad.** Toda escritura de misión, tripulantes, módulos y dispositivos se hace en el nodo `nave`. En la Tierra esas páginas son de solo lectura. La excepción es la línea base previa al vuelo, que solo se escribe en la Tierra.
- **Middleware compartido `SoloEnNodo:<nodo>`** (`app/Http/Middleware/SoloEnNodo.php`, se crea en HU-02.01 y lo pueden usar todos los módulos). Si `config('app.nodo')` no coincide, responde `403` con el mensaje "Esta información se edita en la nave." o "Esta información se edita en la Tierra.". En peticiones Inertia, el mensaje vuelve como `flash.error` y se redirige atrás.
- **Primero las pruebas** ([plan §10](../../../../scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores)). Para probar los dos nodos: `config(['app.nodo' => 'tierra'])`.
- **Auditoría:** toda creación o cambio llama a `RegistroAuditoria::registrar()` de M01 (`crear` o `modificar`, con `recurso_tipo` = nombre de la tabla). Ver datos de salud de un tripulante (la línea base) registra `ver`.
- **Envío a la Tierra (parte B).** El paquete `mision` necesita la cola de M15 (`ColaSincronizacion`), que llega con **HU-15.01 en el Sprint 3**. En el Sprint 1 los dos nodos tienen los mismos datos gracias al *seeder*. La parte B de cada historia se construye dentro de HU-15.01 (Julieth) y se comprueba con HU-15.02 en el Sprint 4.


## Historias

| Historia | Archivo |
|---|---|
| HU-02.01 · Crear una misión con su perfil de enlace | [HU-02.01-crear-una-mision-con-su-perfil-de-enlace.md](HU-02.01-crear-una-mision-con-su-perfil-de-enlace.md) |
| HU-02.02 · Registrar tripulantes y dispositivos | [HU-02.02-registrar-tripulantes-y-dispositivos.md](HU-02.02-registrar-tripulantes-y-dispositivos.md) |
| HU-02.03 · Registrar la línea base previa al vuelo | [HU-02.03-registrar-la-linea-base-previa-al-vuelo.md](HU-02.03-registrar-la-linea-base-previa-al-vuelo.md) |
