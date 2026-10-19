# M09 · Radiación · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

M09 no expone API: la tasa de dosis entra por `POST /api/v1/ingesta/lotes` (M03) con el código `AH-DOSIS-TASA`.

## 2. Eventos

| Publica | Consume |
|---|---|
| — (la regla produce hallazgos dentro de M12) | `LoteIngerido` (M03) con `AH-DOSIS-TASA` → recalcula `dosis_diarias` |

## 3. Reglas de negocio

1. **Dosis de una lectura** = tasa (mSv/d) × tiempo que representa (en días): el tiempo hasta la siguiente lectura válida, con un **máximo de 5 minutos** (`dosis_hueco_max_min`). Se implementa **minuto a minuto** (cada minuto usa la última lectura válida de los 5 minutos anteriores, también si es del día anterior), y en el día de hoy solo cuentan los minutos hasta la última lectura. El día se recalcula con un trabajo único por tripulante y día, para que los 24 lotes horarios del relleno no recalculen 24 veces el día completo. Con una lectura por minuto da 1/1.440 d, pero si faltan lecturas o hay artefactos, la última tasa válida cubre el hueco (hasta 5 min) en vez de contar cero. Antes, cada artefacto o lectura perdida **restaba** dosis y la dosis acumulada quedaba por debajo de la real. Un hueco de más de 5 minutos se marca en `dosis_diarias.minutos_sin_dato` y la página lo indica.
2. **Dosis de carrera** = `dosis_previa_msv` + `acumulada_mision_msv`.
3. **Proyección** = dosis de carrera actual + tasa media de los últimos 7 días (`dosis_proyeccion_dias`) × días hasta `misiones.fecha_fin`. Sin fecha de fin no hay proyección (la página lo indica).
4. **Aviso de proyección** ([04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)): proyección > **80 % de 600 mSv** (480 mSv) → aviso del sistema `radiacion` para ese tripulante. Regla `DOSIS_PROYECCION` de M16.
5. **`ReglaEventoSolar` (HU-09.02):** si la tasa de **cualquier** tripulante es ≥ `spe_factor` × la mediana de sus últimas 24 h (con al menos 60 lecturas; si no, no se evalúa) durante **3 lecturas consecutivas** (evaluado en cada lectura del lote) → 🚨 crítico **para toda la tripulación** (`tripulante_id = null`), sistema `radiacion`, explicación "Tasa de dosis 26 mSv/d (×52 la mediana de 24 h). Ir al refugio." `spe_factor = 10` es un **parámetro de diseño** en M16, a validar.
6. Durante el evento, F1 muestra la **dosis acumulada del evento** y la de los **últimos 30 días** (`dosis_corto_plazo_dias`) frente a la referencia de **250** (`limite_evento_msv`; [Fase 1 §3.2](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#32-ambiente-y-radiación)).
   > ⚠️ **A verificar con NASA-STD-3001:** la documentación llama a esta cifra "250 mSv por evento", pero hasta donde sabemos, la norma la define como un límite de **corto plazo de 30 días** de 250 mGy-Eq para los órganos que forman la sangre, no como un límite por evento ni en mSv de dosis efectiva. Hasta confirmarlo, la página la muestra como "referencia de 30 días" y compara con la dosis de los últimos 30 días; la dosis del evento se muestra aparte, sin límite.
7. **Recalcular días anteriores:** si llega un lote atrasado (por ejemplo, del relleno histórico o de la cola de la Tierra), se recalcula ese día **y todos los siguientes**, porque `acumulada_mision_msv` depende de los días previos.

### Tasas de la simulación ([04 §3.3](../../../docs/propuesta/04-manejo-de-datos.md#ambiente-y-radiación-m08-y-m09))

| Perfil | Tasa base | Variación |
|---|---|---|
| `leo` | ~0,5 mSv/d | ±10 % |
| `marte` (crucero) | 1,8 mSv/d (MSL/RAD) | ±10 % |
| `luna` | 1,8 mSv/d como aproximación de espacio profundo (pendiente de fuente propia) | ±10 % |

> **Posible fuente para `luna`:** la sonda china Chang'e-4 midió en la superficie lunar unos **1,37 mSv/d** de dosis equivalente (Zhang *et al.*, 2020, *Science Advances*). Conviene verificar la cifra en el artículo antes de usarla y, si se confirma, reemplazar el 1,8 provisional del perfil `luna`.

## 4. Contratos

| Ofrece (`Radiacion\Contratos\…`) | Uso |
|---|---|
| `ConsultaDosis::resumen(tripulanteId)` (carrera, misión, proyección, tasa actual) | F1, M14 |
| `ReglaEventoSolar`, `ReglaProyeccionDosis` | Registradas en el motor de M12 |

| Usa | De |
|---|---|
| `ConsultaObservaciones::serie()` | M03 |
| `ConsultaMision::activa()` (fecha de fin) | M02 |
| `ParametrosMision` (`spe_factor`), umbral `DOSIS_PROYECCION` | M16 |
| `ReglaAnalitica` | M12 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Ver la dosis propia | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver la dosis de la tripulación | `ver-datos-tripulacion` (`cmo`, `medico_vuelo`) |

## 6. Archivos

```
app/Modules/Radiacion/
├── RadiacionServiceProvider.php
├── routes.php
├── Contratos/ConsultaDosis.php
├── Domain/Reglas/{CalculadoraDosis,ReglaEventoSolar,ReglaProyeccionDosis}.php
├── Application/{RecalcularDosisDiaria,ResumenDosis}.php
├── Http/Controllers/Web/RadiacionController.php
├── Infrastructure/
│   ├── Models/{DosisDiaria,DosisCarreraPrevia}.php
│   ├── Repositorios/DosisEloquent.php
│   └── Listeners/AlIngerirLote.php
├── Database/
│   ├── Migrations/{create_dosis_diarias,create_dosis_carrera_previa}.php
│   └── Seeders/DosisPreviaSeeder.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] 1.440 lecturas de 0,5 mSv/d suman 0,5 mSv en el día.
- [ ] Con 10 artefactos sueltos en el día, la dosis sigue sumando 0,5 mSv (la tasa anterior cubre cada hueco).
- [ ] Un hueco de 30 min sin lecturas suma solo 5 min de dosis y queda en `minutos_sin_dato`.
- [ ] Un lote atrasado del día 3 recalcula `acumulada_mision_msv` de los días 3 en adelante.
- [ ] Proyección: carrera 100 mSv + 1,8 mSv/d × 200 días = 460 mSv → sin aviso; con 250 días = 550 → aviso.
- [ ] Dos lecturas altas seguidas de una normal **no** disparan la regla de evento solar.
- [ ] Con E0, ninguna regla de radiación se dispara (tampoco `DOSIS_PROYECCION` con `marte`: con la misión de 60 días la proyección máxima es ~228 mSv).
- [ ] La última lectura de un día cubre el primer minuto del siguiente; los minutos de hoy que aún no han pasado no cuentan como "sin dato".
- [ ] Con menos de 60 lecturas en las últimas 24 h no se evalúa el evento solar.

Detalle de cada historia en las [historias de backend](HU/README.md).
