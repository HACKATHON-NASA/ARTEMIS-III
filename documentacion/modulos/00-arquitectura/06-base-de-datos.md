# 6 · Base de datos y migraciones

> [← Guía](README.md) · Anterior: [Frontend con Vue e Inertia](05-frontend-vue.md) · Siguiente: [Forma de trabajo](07-forma-de-trabajo.md)

Las reglas de tipos y nombres están en las [convenciones de base de datos](../README.md#7-convenciones-de-base-de-datos), y qué módulo es dueño de cada tabla, en [propiedad de las tablas](../README.md#6-propiedad-de-las-tablas). Este documento explica **cómo se trabaja**.

## 1. Las reglas que no se rompen

1. **Cada tabla tiene un solo módulo dueño.** Solo ese módulo la migra, la escribe y la consulta. Los demás usan sus `Contratos/`.
2. **Una migración fusionada en `main` no se edita.** Si hay que cambiar algo, se crea una migración nueva. Editar una vieja deja a los demás con una base distinta y rompe los nodos de cPanel.
3. **La base se cambia solo con migraciones.** Nunca a mano en phpMyAdmin, ni en local ni en cPanel.
4. **Ningún valor clínico en una migración.** Los umbrales y parámetros se siembran con los *seeders* de M16.

## 2. Crear una migración

Cada módulo guarda sus migraciones en `app/Modules/<Modulo>/Database/Migrations/`, y su *ServiceProvider* las carga ([HU-00.01](../../scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md)):

```bash
php artisan make:migration create_sintomas_registrados --path=app/Modules/Sintomas/Database/Migrations
```

| Cambio | Nombre de la migración |
|---|---|
| Tabla nueva | `create_<tabla>` |
| Columnas nuevas | `add_<columnas>_to_<tabla>` |
| Cambio de una columna | `alter_<columna>_on_<tabla>` |

La migración copia **exactamente** la tabla de la historia de base de datos (`database/HU/HU-xx.yy-….md`): cada historia trae la línea de Laravel de cada columna.

```php
return new class extends Migration {
    public function up(): void
    {
        Schema::create('sintomas_registrados', function (Blueprint $table) {
            $table->uuid('id')->primary();                 // lo genera el cliente
            $table->uuid('tripulante_id');
            $table->string('tipo', 40);                    // validado con el catálogo de M16, no con ENUM
            $table->unsignedTinyInteger('intensidad');
            $table->dateTime('inicio_en', 3);              // UTC con milisegundos
            $table->string('notas', 500)->nullable();
            $table->dateTime('creado_en', 3);
            $table->index(['tripulante_id', 'inicio_en']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sintomas_registrados');
    }
};
```

> Ejemplo ilustrativo: las columnas reales de cada tabla son las de su historia.

## 3. Modelos

- Viven en `Infrastructure/Models/` del módulo dueño.
- Con `datetime(3)`, el modelo declara `protected $dateFormat = 'Y-m-d H:i:s.v';` o Eloquent pierde los milisegundos.
- Con `id` UUID: `use HasUuids;` (el servidor lo genera solo si el cliente no lo trae).
- Los valores enumerados se convierten con `casts()` a su *enum* de `Domain/Enums/`.
- Las relaciones Eloquent solo entre tablas **del mismo módulo**. Las referencias a tablas de otro módulo son lógicas (una columna `…_id` sin clave foránea).

## 4. *Seeders* y *factories*

| Pieza | Dónde | Para qué |
|---|---|---|
| *Seeder* | `Database/Seeders/` del módulo | Datos iniciales: usuarios de prueba (M01), misión de demostración (M02), umbrales y catálogos (M16). Se pueden ejecutar dos veces sin duplicar (`updateOrCreate`) |
| *Factory* | `Database/Factories/` del módulo, enlazada desde el modelo con `newFactory()` | Datos de las pruebas. `UserFactory` se queda donde la deja el *kit* |

El orden de los *seeders* lo fija `DatabaseSeeder`, según lo que diga cada ficha.

## 5. Comandos del día a día

| Comando | Cuándo |
|---|---|
| `php artisan migrate` | Después de cada `git pull` |
| `php artisan migrate:fresh --seed` | En **local**, para empezar de cero. En cPanel está bloqueado |
| `php artisan test` | Las pruebas usan SQLite en memoria: toda sentencia SQL escrita a mano (por ejemplo, los *triggers* de M01) necesita su variante para `mysql` y para `sqlite` |
