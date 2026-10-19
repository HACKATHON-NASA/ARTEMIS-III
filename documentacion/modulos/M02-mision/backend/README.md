# M02 · Misión y tripulación · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

M02 no expone API pública; su gestión es web (F4 y F3), con las escrituras protegidas por el middleware `SoloEnNodo` (misión, tripulantes, módulos y dispositivos solo en la nave; línea base previa solo en la Tierra). Los demás módulos usan sus contratos. Rutas, validaciones y mensajes en las [historias de backend](HU/README.md).

## 2. Eventos

M02 no publica ni consume eventos. Lo que debe viajar al otro nodo lo encola directamente con `ColaSincronizacion` (M15), como hace M07 con los PDF.

## 3. Reglas de negocio

### 3.1 Misión

1. **Día de misión** de una fecha = `fecha (UTC) − fecha_inicio + 1`. Antes del inicio es `0` o negativo (fase previa al vuelo). Se calcula **para la fecha del dato**, no solo para hoy: el relleno histórico, la exportación anonimizada (M14) y el cuestionario de mareo (M11) usan el día de misión del momento de la medición.
2. Solo puede haber **una misión activa**; activar otra desactiva la anterior. Las consultas de tripulantes y módulos de la nave devuelven solo los de la misión activa. La pantalla gestiona la activa; **empezar otra misión** cierra la actual, que queda de solo lectura con sus datos y su configuración (M16 §3.6). **El *seeder* no mueve la fecha de inicio** si la misión ya existe, y fuera de `local` exige `MISION_INICIO` (la misma en los dos nodos).

### 3.2 Tripulantes y usuarios

3. Cada usuario con rol `astronauta` o `cmo` debe estar vinculado a **un** tripulante. Si inicia sesión sin vínculo, F1 muestra "Tu usuario no está vinculado a ningún tripulante; avisa al administrador" en lugar de páginas vacías.
4. Un tripulante tiene como máximo un usuario, y un usuario como máximo un tripulante **por misión**.

### 3.3 Dispositivos

5. Un dispositivo personal solo puede estar asignado a un tripulante a la vez; reasignarlo conserva las lecturas anteriores con su tripulante original.
6. **Batería y último contacto:** M03 llama a `ConsultaDispositivos::marcarContacto(identificador, bateria_pct)` con cada lote; `bateria_pct` es un campo opcional del lote ([M03 backend §1](../../M03-ingesta/backend/README.md#1-api-rest)).
7. El tipo `tableta` es solo de inventario: la PWA escribe con el token del usuario (M01), no con un token de dispositivo. Puede quedar sin asignar (tableta compartida). **No se pueden cambiar** después de crearlos el código del tripulante, el código del módulo de la nave, ni el identificador y el tipo del dispositivo: los lotes, el simulador y los tokens los usan.

### 3.4 Línea base previa al vuelo (HU-02.03)

8. Se registra por variable (código del [catálogo](../../README.md#8-catálogo-de-códigos-de-variables)) con media y desviación estándar, y se envía a M12 con `fuente = previa_vuelo`.
9. **Contexto:** las variables de signos vitales se guardan con `contexto = reposo` (la línea base de M12 se busca por tripulante, código **y** contexto); la masa corporal (`29463-7`) con `contexto = null`.
10. **Dónde se registra:** el médico de vuelo trabaja en el nodo Tierra ([M01 backend §3.2](../../M01-identidad/backend/README.md#32-inicio-de-sesión-por-nodo)). La línea base se guarda allí y viaja a la nave dentro del paquete `configuracion` de M15, porque es la nave la que evalúa las reglas. Hasta que exista la sincronización (Sprint 4), el *seeder* carga la misma línea base de demostración en los dos nodos.

### 3.5 Dos nodos

11. Los datos de M02 se crean en la **nave**, que es la fuente de verdad, y viajan a la Tierra en un paquete `mision` (prioridad 2) cuando cambian: una instantánea de las cuatro tablas sin `bateria_pct` ni `ultimo_contacto_en` (que cambian con cada lote). Se construye dentro de HU-15.01 (Sprint 3); hasta entonces, el *seeder* deja los mismos datos en los dos nodos.
12. Las claves primarias de misión, tripulantes, dispositivos y módulos de la nave son **iguales en los dos nodos**: el *seeder* las calcula como UUID v5 a partir del código (por ejemplo, `T2` o `astroskin-T2`) y lo que se crea después en la nave llega a la Tierra con su mismo `id`. Sin esto, los datos que llegan a la Tierra apuntarían a tripulantes que no existen allí.

## 4. Contratos

| Ofrece (`Mision\Contratos\…`) | Uso |
|---|---|
| `ConsultaMision::activa(): MisionDTO` | Perfil de enlace (M15), fechas (M09) |
| `ConsultaMision::diaMision(fecha): int` | M05, M11, M14 y cualquier módulo que muestre o exporte el día de misión de un dato |
| `ConsultaTripulantes::porId()`, `::porCodigo()`, `::porUsuario()`, `::todos()` | Todos los módulos clínicos; M03 traduce el código del lote (`T2`) a su `id` |
| `ConsultaDispositivos::porIdentificador()`, `::marcarContacto(identificador, bateria_pct)` | M03 (validar el lote y actualizar el último contacto y la batería) |
| `ConsultaDispositivos::porId()` (devuelve el modelo `Dispositivo`, que usa `HasApiTokens` de Sanctum), `::todos()` | M01 (emitir tokens de dispositivo, HU-01.03) |
| `ConsultaModulosNave::todos()`, `::porCodigo()` | M03, M08 |
| `Importador` del paquete `mision` (implementa `Sincronizacion\Contratos\Importador`) | M15 |

| Usa | De |
|---|---|
| `Analitica\Contratos\RegistroLineaBase`, `ConsultaLineaBase` | M12 (guardar y mostrar la línea base previa al vuelo; su versión mínima se crea en HU-02.03) |
| `Ingesta\Contratos\EstadoSensores::todos()` | M03 (columna de estado de la página de dispositivos, desde HU-03.03) |
| `Identidad\Contratos\RegistroAuditoria` | M01 |
| `Sincronizacion\Contratos\ColaSincronizacion` | M15 (paquete `mision` hacia la Tierra) |

## 5. Permisos

Usa los *Gates* de [M01 backend §5](../../M01-identidad/backend/README.md#5-permisos):

| Acción | *Gate* | Roles |
|---|---|---|
| Crear y editar la misión, los tripulantes y los módulos de la nave | `gestionar-mision` | `admin` |
| Registrar y asignar dispositivos | `registrar-dispositivos` | `admin`, `ingeniero_biomedico` |
| Registrar la línea base previa al vuelo | `registrar-linea-base` | `medico_vuelo` |

## 6. Archivos

```
app/Modules/Mision/
├── MisionServiceProvider.php
├── routes.php
├── Contratos/{ConsultaMision,ConsultaTripulantes,ConsultaDispositivos,ConsultaModulosNave}.php
├── Domain/
│   ├── Enums/{PerfilEnlace,TipoDispositivo,Sexo,VariablePrevuelo}.php
│   └── ValueObjects/{DiaMision,IdEstable}.php     # IdEstable: UUID v5 a partir del código
├── Application/{CrearMision,RegistrarTripulante,VincularUsuario,RegistrarDispositivo,RegistrarLineaBasePrevia,EncolarInstantaneaMision}.php
├── Console/EncolarInstantaneaCommand.php       # mision:encolar-instantanea (Sprint 3)
├── Http/
│   ├── Controllers/Web/{MisionController,TripulanteController,ModuloNaveController,DispositivoController,LineaBaseController}.php
│   └── Requests/{GuardarMisionRequest,GuardarTripulanteRequest,GuardarModuloNaveRequest,GuardarDispositivoRequest,LineaBasePreviaRequest}.php
├── Infrastructure/
│   ├── Models/{Mision,Tripulante,Dispositivo,ModuloNave}.php
│   ├── Repositorios/*.php
│   └── Sincronizacion/ImportadorMision.php
├── Database/
│   ├── Migrations/{create_misiones,create_tripulantes,create_modulos_nave,create_dispositivos}.php
│   └── Seeders/MisionDemoSeeder.php    # Ver base de datos §6
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] El día de misión se calcula bien en el día 1, en el día 30, antes del inicio y para una fecha pasada distinta de hoy.
- [ ] No se pueden tener dos misiones activas.
- [ ] Un dispositivo personal sin tripulante es rechazado por la validación; una tableta sin asignar se acepta.
- [ ] El código de un tripulante y el identificador de un dispositivo no se pueden cambiar.
- [ ] En la Tierra, las escrituras de misión, tripulantes y dispositivos se rechazan; en la nave, la de la línea base previa.
- [ ] Ejecutar `MisionDemoSeeder` en dos bases distintas produce los mismos `id` de misión, tripulantes, dispositivos y módulos.
- [ ] La línea base previa al vuelo de la FC queda con `contexto = reposo`; la de la masa corporal, con `contexto = null`.
- [ ] Un astronauta sin tripulante vinculado ve el aviso en F1 y no un error.
- [ ] Un lote con `bateria_pct = 40` actualiza la batería del dispositivo.
