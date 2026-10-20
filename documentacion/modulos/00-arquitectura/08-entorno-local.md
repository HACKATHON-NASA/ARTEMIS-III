# 8 · Preparar el entorno local

> [← Guía](README.md) · Anterior: [Forma de trabajo](07-forma-de-trabajo.md) · Siguiente: [Flujo de trabajo con Git](09-flujo-de-git.md) · [ARCHITECTURE.md](ARCHITECTURE.md)

Paso a paso para que **cada integrante** tenga AstroHealth funcionando en su computador (Windows), con **las mismas versiones** que el servidor. Siga los pasos **en orden** y no se salte las verificaciones.

> **Usted no crea el proyecto:** ya existe en el repositorio. Usted lo **clona** e instala sus dependencias. Vue, Inertia y Tailwind **no se instalan aparte**: vienen dentro del proyecto y se instalan con `npm install` (paso 6).

## Versiones obligatorias

| Herramienta | Versión | Por qué esa |
|---|---|---|
| **PHP** | **8.4** | Es la del servidor (cPanel). Laravel 13 exige 8.3 o más |
| **Composer** | 2.8 o más | Instala los paquetes de PHP |
| **Node.js** | **22** (LTS) | Lo pide Vite 8 |
| **npm** | **12** o más | npm 11.2 tiene un error con este proyecto (`edgesOut`) |
| **MySQL** | El que trae **XAMPP** | Base de datos local |
| **Git** | 2.40 o más | Control de versiones |

El proyecto usa **Laravel 13**, **Vue 3**, **Inertia 3**, **Tailwind CSS v4** y **TypeScript**.

---

## Paso 1 · Revisar lo que ya tiene

Abra **PowerShell** y ejecute:

```powershell
php -v
composer -V
node -v
npm -v
git --version
```

Anote lo que sale y compárelo con la tabla de arriba. Los pasos siguientes instalan lo que falte.

## Paso 2 · PHP 8.4 y Composer

1. Abra **PowerShell como administrador**: clic derecho en el menú Inicio → **Terminal (Administrador)**.
2. Pegue este comando. Es el instalador oficial que recomienda Laravel e instala **PHP 8.4, Composer y el instalador de Laravel**:

   ```powershell
   Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.4'))
   ```

3. **Si tiene XAMPP**, saque el PHP de XAMPP del PATH del sistema, porque si no, Windows seguirá usando el PHP viejo (8.2). En la misma terminal de administrador:

   ```powershell
   [Environment]::SetEnvironmentVariable('Path', (([Environment]::GetEnvironmentVariable('Path','Machine') -split ';' | Where-Object { $_ -and $_ -ne 'C:\xampp\php' }) -join ';'), 'Machine')
   ```

   XAMPP sigue funcionando igual (Apache, MySQL y phpMyAdmin no usan el PATH).

4. **Cierre todas las terminales y VS Code por completo**, y abra una terminal nueva.

✅ **Verificación:**

```powershell
php -v        # PHP 8.4.x
composer -V   # Composer version 2.8.x o más
```

Si `php -v` sigue diciendo 8.2, es que la terminal es vieja. Ciérrela, o pegue esto y vuelva a probar:

```powershell
$env:Path = [Environment]::GetEnvironmentVariable('Path','Machine') + ';' + [Environment]::GetEnvironmentVariable('Path','User')
```

## Paso 3 · Node.js 22 y npm 12

**Opción A · Si ya tiene `fnm`** (si `fnm --version` responde algo):

```powershell
fnm install 22
fnm use 22
fnm default 22
```

**Opción B · Si no tiene `fnm`:** descargue e instale **Node.js 22 LTS** desde nodejs.org (instalador de Windows `.msi`, opciones por defecto). Cierre y abra la terminal.

En los dos casos, actualice npm:

```powershell
npm install -g npm@latest
```

✅ **Verificación:**

```powershell
node -v   # v22.x.x
npm -v    # 12.x.x o más
```

## Paso 4 · MySQL (XAMPP) y la base de datos

1. Si no tiene XAMPP, instálelo desde apachefriends.org (solo hacen falta los componentes **MySQL** y **phpMyAdmin**).
2. Abra el **panel de XAMPP** y pulse **Start** en **MySQL**. Debe ponerse en verde.
3. Cree la base de datos:

   ```powershell
   C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE astrohealth CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

✅ **Verificación:**

```powershell
C:\xampp\mysql\bin\mysql.exe -u root -e "SHOW DATABASES LIKE 'astrohealth';"
```

Debe mostrar `astrohealth`.

> Si su MySQL de XAMPP tiene contraseña para `root`, añada `-p` a los dos comandos (le pedirá la contraseña) y póngala en `DB_PASSWORD` en el paso 7.

## Paso 5 · Clonar el repositorio

Escoja la carpeta donde guarda sus proyectos y clone:

```powershell
cd C:\Users\<su-usuario>\Documents
git clone https://github.com/HACKATHON-NASA/ARTEMIS-III.git ArtemisIII
cd ArtemisIII
git switch development
```

Configure su nombre y correo en Git, si nunca lo ha hecho:

```powershell
git config --global user.name "Su Nombre"
git config --global user.email "su-correo@ejemplo.com"
```

**Se trabaja siempre en la rama `development`** ([forma de trabajo](09-flujo-de-git.md#1-las-tres-ramas-permanentes)). El repositorio tiene tres carpetas: `documentacion/`, `astrohealth/` (la aplicación) y `simulador/`.

## Paso 6 · Instalar las dependencias

```powershell
cd astrohealth
composer install
npm install
```

- `composer install` instala Laravel y los paquetes de PHP en `vendor/`.
- `npm install` instala **Vue, Inertia, Tailwind, Vite y TypeScript** en `node_modules/`.

✅ **Verificación:** las dos terminan sin `error`. Los avisos `npm warn deprecated` se pueden ignorar.

> ⚠️ **No ejecute** `npm audit fix --force` **ni actualice** `vite-plus`: el proyecto usa `vite-plus` **0.3.0** fijo y una versión más nueva rompe Vite. Las alertas de `npm audit` sobre `tinypool` son de una herramienta de desarrollo y no llegan a la aplicación.

## Paso 7 · Configurar el `.env`

```powershell
copy .env.example .env
php artisan key:generate
```

El `.env.example` ya trae los valores del proyecto. Abra `.env` y compruebe que dice:

```env
APP_NAME=AstroHealth
APP_NODO=nave
APP_LOCALE=es

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=astrohealth
DB_USERNAME=root
DB_PASSWORD=

SESSION_LIFETIME=480
QUEUE_CONNECTION=database
```

> El `.env` es **suyo**: nunca se sube a Git (ya está en `.gitignore`). Si cambia algo que todos necesitan, se cambia en `.env.example`.

## Paso 8 · Crear las tablas

Con MySQL encendido en XAMPP:

```powershell
php artisan migrate
```

✅ **Verificación:** lista las tablas creadas (`users`, `sessions`, `cache`, `jobs`…) con **DONE**.

## Paso 9 · Arrancar la aplicación

```powershell
composer run dev
```

Arranca tres procesos en la misma terminal:

| Prefijo | Qué es |
|---|---|
| `[server]` | Laravel en http://localhost:8000 |
| `[queue]` | La cola de trabajos |
| `[vite]` | Vue: compila y recarga la página al guardar cambios |

Abra **http://localhost:8000**. Para detener todo: **Ctrl + C**.

> El `http://localhost:5173` que muestra Vite **no se abre**: lo usa Laravel por debajo.

## Paso 10 · Verificación final

Marque cada punto antes de empezar a programar:

- [ ] `php -v` → 8.4
- [ ] `node -v` → 22 y `npm -v` → 12 o más
- [ ] `composer install` y `npm install` sin errores
- [ ] `php artisan migrate` sin errores
- [ ] http://localhost:8000 abre la página de AstroHealth
- [ ] `php artisan test` → todas las pruebas en verde
- [ ] Leyó [ARCHITECTURE.md](ARCHITECTURE.md) y la [forma de trabajo](07-forma-de-trabajo.md)

---

## El día a día

Cada vez que trae cambios de otros, ejecute dentro de `astrohealth/` (el flujo completo de ramas está en [Flujo de trabajo con Git](09-flujo-de-git.md)):

```powershell
git switch development
git pull origin development
composer install
npm install
php artisan migrate
composer run dev
```

`composer install` y `npm install` solo tardan si alguien añadió paquetes. `php artisan migrate` crea las tablas nuevas que hayan llegado.

## Problemas frecuentes

| Mensaje | Causa | Solución |
|---|---|---|
| `php -v` dice **8.2.12** | La terminal es anterior a la instalación, o `C:\xampp\php` sigue en el PATH | Cierre VS Code y las terminales. Si sigue, repita el paso 2.3 |
| `Your Composer dependencies require a PHP version ">= 8.4.0"` | Mismo problema: se está usando PHP 8.2 | Igual que el anterior |
| `npm error Cannot read properties of null (reading 'edgesOut')` | npm 11.2 | `npm install -g npm@latest` (paso 3) y repita `npm install` |
| `"vp" no se reconoce como un comando` | `npm install` no terminó bien | Repita el paso 6 y revise que no haya errores |
| `Expected @voidzero-dev/vite-plus-core… but found vite@8…` | Alguien actualizó `vite-plus` | `npm install -D -E vite-plus@0.3.0` |
| `Vite manifest not found at …/public/build/manifest.json` | Vue no está corriendo | Use `composer run dev` (no solo `php artisan serve`) |
| `SQLSTATE[HY000] [2002] No se puede establecer una conexión` | MySQL apagado | **Start** en MySQL desde el panel de XAMPP |
| `SQLSTATE[HY000] [1049] Unknown database 'astrohealth'` | Falta la base | Paso 4.3 |
| `Failed to listen on 127.0.0.1:8000` | El puerto 8000 está ocupado (otro `php artisan serve` abierto) | Cierre la otra terminal o use `php artisan serve --port=8001` |
| `No application encryption key has been specified` | Falta la clave | `php artisan key:generate` |

Si el error no está aquí, cópielo **completo** en el chat del equipo, con el comando que lo produjo.
