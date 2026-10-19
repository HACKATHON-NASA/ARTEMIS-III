# M13 · Alertas y casos clínicos · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

| Método | Ruta | Autenticación | Descripción |
|---|---|---|---|
| `POST` | `/api/v1/alertas/casos/{caso}/acciones` | Solo token PWA `registros:escribir` de un usuario `cmo` | Registra una acción (`paso` o `nota`) **del caso** sin conexión; idempotente por `id`; caso cerrado → `422` |

Las demás operaciones (confirmar, descartar, abrir y cerrar casos) son acciones web de Inertia, **solo en el nodo nave**.

## 2. Eventos

| Publica | Consume |
|---|---|
| `AlertaCreada`, `AlertaEscalada` | `HallazgoDetectado` (M12), `RecomendacionRecibida` (M15: si trae `alerta_id`, se agrega como acción del caso) |

## 3. Reglas de negocio

### 3.1 Creación y deduplicación

Al recibir un `HallazgoDetectado` (solo `aviso` o `critico`):

1. Busca una alerta del mismo **tripulante y familia** en estado `abierta` o `confirmada`.
2. **Si existe:** suma una ocurrencia y actualiza `ultima_en` y `explicacion`. Si el nuevo nivel es mayor (`aviso` → `critico`), **escala**: cambia el `nivel` y publica `AlertaEscalada`.
3. **Si no existe:** crea la alerta (`abierta`) y publica `AlertaCreada`.
4. **Una alerta no se cierra sola** aunque el valor vuelva a la normalidad: la cierra una persona (al cerrar su caso o descartarla). Así el semáforo no "olvida" un episodio que nadie revisó. La alerta muestra "Último hallazgo hace 2 h" para que el CMO vea que el problema ya no está activo.

### 3.2 Confirmación

| Tipo de alerta | Cómo se confirma |
|---|---|
| `aviso` de una familia continua (`FC`, `SPO2`, `FR`, `TEMP`, `GLUCOSA`, `CO2_MODULO`, `AMB_*`) | **Automática:** una segunda ocurrencia (u otro hallazgo de la misma familia) en los `alerta_confirmacion_min` siguientes (hoy 30 min) la pasa a `confirmada` |
| Cualquier otro `aviso` (PA, ortostática, exámenes, sueño, cognitivo, masa, proyección de dosis, CO₂ + cefalea) | **Manual:** el CMO la confirma, la descarta o la cierra con una nota (HU-13.02) |
| `critico` | No espera confirmación: se notifica de inmediato y abre un caso |

### 3.3 Notificación y prioridad

| Nivel | A quién | Cómo | Prioridad de sincronización (M15) |
|---|---|---|:---:|
| `critico` | Tripulante afectado y CMO; si `tripulante_id = null`, toda la tripulación | Ventana modal con sonido en F1 y F2 | 1 |
| `aviso` | CMO y el tripulante afectado | Bandeja de F2 e insignia en F1 | 2 |

### 3.4 Casos clínicos (HU-13.03)

- Toda alerta `critico` **abre un caso automáticamente** con el protocolo de su familia (o `generico`).
- El CMO puede abrir un caso desde cualquier alerta `aviso`.
- Cerrar un caso exige un `resultado`; cierra sus alertas (`cerrada`). Un `aviso` sin caso se puede cerrar con una nota.
- Al abrir un caso se **copian** los pasos del protocolo y su estado de validación (`protocolo_pasos`), para que una edición posterior del protocolo no descuadre las acciones.
- Una recomendación de la Tierra se agrega como acción `recomendacion` (con `autor` en texto, porque su usuario no existe en la nave); si la alerta aún no tiene caso, queda en la alerta y se copia al abrirlo.
- Cada hallazgo y ocurrencia se registra en `alerta_hallazgos`: un evento repetido no cuenta dos veces y un episodio ya descartado no vuelve a crear una alerta. Detalle en las [historias de backend](HU/README.md).

### 3.4 bis Dónde se gestionan las alertas

Las alertas **nacen y se gestionan en la nave**: allí están los datos, las reglas y el CMO, y la decisión a bordo no puede esperar a la Tierra. El nodo Tierra recibe una copia de solo lectura:

| Nodo | Qué puede hacer |
|---|---|
| Nave | El CMO confirma, descarta, abre y cierra casos y registra acciones |
| Tierra | El médico de vuelo ve las alertas, los casos y las decisiones tomadas a bordo, y **responde con una recomendación** (M15) ligada a la alerta; al llegar a la nave se agrega como acción del caso |

Antes, el médico de vuelo podía confirmar o descartar alertas en la Tierra, pero ese cambio no tenía forma de llegar a la nave y las dos copias quedaban distintas.

**Sincronización de los cambios:** cada cambio de una alerta o de su caso (confirmación, escalamiento, acción, cierre) encola un paquete `alerta` nuevo con el estado completo y su `actualizada_en`. La Tierra lo aplica solo si es más reciente que la copia que ya tiene, así los paquetes que llegan desordenados no deshacen un cambio.

### 3.5 Fuente y validación de los protocolos (HU-13.04)

- El *seeder* falla si algún paso no tiene `fuente`.
- `Cmo/Casos/Show` muestra la fuente de cada paso. Si `validado_en` es `null`, muestra arriba el aviso **"⚠️ Protocolo de demostración: pendiente de validación clínica"**.
- El admin edita los protocolos y registra la validación (`validado_por`, `validado_en`) en F4, **en la Tierra**, donde está el médico de vuelo que los valida. Llegan a la nave en el paquete `configuracion` (M15); allí son de solo lectura. **Editar cualquier paso** vuelve `validado_en` a `null`. Ambas acciones se auditan (M01).

### 3.6 Semáforo (lo usan F1, F2 y HU-04.02)

Para cada tripulante y cada sistema, considerando sus alertas y las globales (`tripulante_id = null`) en estado `abierta` o `confirmada`:

| Color | Condición | Se muestra con |
|---|---|---|
| 🔴 Rojo | Hay alguna `critico` | Icono ✖ y texto "Crítico" |
| 🟡 Amarillo | Hay alguna `aviso` | Icono ⚠ y texto "Atención" |
| 🟢 Verde | Ninguna | Icono ✔ y texto "Normal" |

F1 muestra los sistemas `corazon`, `sueno`, `mente`, `radiacion` y `cuerpo`; F2 añade `ambiente`.

### 3.7 Retroalimentación

Descartar exige un `motivo_descarte`. F3 muestra, por regla, el porcentaje de alertas descartadas como `falso_positivo` en los últimos `falsos_positivos_dias` (hoy 30), para que el médico de vuelo ajuste los umbrales en M16.

## 4. Contratos

| Ofrece (`Alertas\Contratos\…`) | Uso |
|---|---|
| `EstadoSemaforo::porTripulante(tripulanteId)` | M04 (F2), F1 |
| `EstadoSemaforo::alInstante(tripulanteId, instante)` | M14 (semáforo al final de un día pasado) |
| `ConsultaAlertas::activas(tripulanteId = null)`, `::delDia(tripulanteId, fecha)` | M14, M15 (resumen) |

| Usa | De |
|---|---|
| `ConsultaTripulantes` | M02 |
| `RegistroAuditoria` | M01 (confirmaciones, descartes y cierres) |
| `ParametrosMision` (`alerta_confirmacion_min`, `falsos_positivos_dias`) | M16 |
| `ColaSincronizacion`, `Importador`, `ConsultaRecomendaciones::deAlerta(alertaId)` | M15 (paquete `alerta`; protocolos dentro de `configuracion`; recomendaciones de una alerta) |

## 5. Permisos

| Acción | Roles |
|---|---|
| Ver las alertas propias | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver todas las alertas y casos | `ver-alertas` (`cmo`, `medico_vuelo`) |
| Gestionar alertas y casos (confirmar, descartar, abrir, cerrar, registrar acciones), solo en la nave | `gestionar-alertas` (`cmo`) |
| Responder a una alerta con una recomendación (Tierra) | `enviar-recomendaciones` (`medico_vuelo`) |
| Editar protocolos y registrar su validación, solo en la Tierra | `gestionar-protocolos` (`admin`) |

## 6. Archivos

```
app/Modules/Alertas/
├── AlertasServiceProvider.php
├── routes.php
├── Contratos/{EstadoSemaforo,ConsultaAlertas}.php
├── Domain/
│   ├── Enums/{EstadoAlerta,MotivoDescarte,EstadoCaso,ColorSemaforo}.php
│   ├── Reglas/{Deduplicador,PoliticaConfirmacion,CalculadoraSemaforo}.php
│   └── Eventos/{AlertaCreada,AlertaEscalada}.php
├── Application/{ProcesarHallazgo,ConfirmarAlerta,DescartarAlerta,CerrarAlerta,AbrirCaso,RegistrarAccion,CerrarCaso,EditarProtocolo,RegistrarValidacion}.php
├── Http/
│   ├── Controllers/Web/{AlertasController,CasosController,HistorialController,ProtocolosController}.php
│   ├── Controllers/Api/AccionController.php
│   ├── Requests/{DescartarAlertaRequest,CerrarAlertaRequest,AccionRequest,CerrarCasoRequest,ProtocoloRequest,ValidacionRequest}.php
│   └── Middleware/CompartirAlertasCriticas.php    # Props compartidas alertasCriticas y resumenAlertas
├── Infrastructure/
│   ├── Models/{Alerta,CasoClinico,Accion,Protocolo}.php
│   ├── Repositorios/*.php
│   ├── Listeners/{AlDetectarHallazgo,AlRecibirRecomendacion}.php
│   └── Importadores/{AlertaImportador,ProtocoloImportador}.php   # Importador de M15
├── Database/
│   ├── Migrations/{create_alertas,create_alerta_hallazgos,create_protocolos,create_casos_clinicos,add_caso_fk_to_alertas,create_acciones}.php
│   └── Seeders/ProtocolosSeeder.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] **E8:** `SPO2_AVISO` → alerta `aviso` → segundo hallazgo en 30 min → `confirmada` → `SPO2_CRITICO` → la **misma** alerta escala a `critico` y abre el caso `hipoxemia`.
- [ ] Diez hallazgos iguales en una hora → una alerta con 10 ocurrencias.
- [ ] Una alerta global (`DOSIS_SPE`) pone en rojo el sistema `radiacion` de los 4 tripulantes.
- [ ] Descartar sin motivo devuelve error de validación.
- [ ] Una alerta descartada no impide crear una nueva de la misma familia.
- [ ] Un protocolo con un paso sin `fuente` hace fallar el *seeder*.
- [ ] Editar un paso de un protocolo validado deja `validado_en = null` y vuelve a mostrar el aviso.
- [ ] En el nodo Tierra, confirmar o descartar una alerta no está disponible (`403`); una recomendación ligada a la alerta llega a la nave como acción del caso.
- [ ] Dos paquetes `alerta` de la misma alerta que llegan desordenados a la Tierra dejan el estado más reciente.
- [ ] Cuando la SpO₂ vuelve a la normalidad, la alerta sigue `abierta` hasta que el CMO la cierra.
