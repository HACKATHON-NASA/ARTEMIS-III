# M10 · Físico y metabólico · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

| Método | Ruta | Autenticación | Descripción |
|---|---|---|---|
| `POST` | `/api/v1/fisico/ejercicio` | Token `registros:escribir` (PWA) o `lecturas:escribir` (simulador) | `{ id, tripulante_id, equipo, inicio, duracion_min, carga_kg, fc_media, fc_max }`; idempotente por `id` |
| `POST` | `/api/v1/fisico/nutricion` | Igual | `{ id, tripulante_id, tipo, descripcion, kcal, proteina_g, volumen_ml, consumido_en }`; idempotente por `id` |
| `POST` | `/api/v1/fisico/fuerza` | Igual | `{ id, tripulante_id, mano, valor_kg, medido_en }`; idempotente por `id` |

Autenticación: solo token ([M01 §3.3](../../M01-identidad/backend/README.md#33-tokens)). Con el token de la PWA, `tripulante_id` se toma del usuario y se ignora el del cuerpo; con el del dispositivo `simulador`, el `tripulante_id` (código) es obligatorio.

La glucosa y la masa corporal entran por `POST /api/v1/ingesta/lotes` (M03), desde un dispositivo de tipo `cgm` y desde uno de tipo `medidor_masa` (`masa-T<n>`, nuevo en M02: antes no había ningún tipo de dispositivo para la masa).

## 2. Eventos

| Publica | Consume |
|---|---|
| `ObservacionRegistrada` por cada sesión (`AH-EJERCICIO-MIN`, minutos), comida (`AH-INGESTA-KCAL`), líquido (`AH-INGESTA-LIQUIDO`) y prueba de fuerza (`AH-FUERZA-PRENSION`) | — |

## 3. Reglas de negocio

1. **Pérdida de masa** ([04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)): masa media de la última semana < 95 % de la línea base (pérdida > 5 %) → ⚠️ aviso del sistema `cuerpo`. Regla `MASA_PERDIDA` de M16, evaluada por M12. La masa se mide **una vez por semana** (los lunes), así que la "media de la última semana" suele ser una sola medición; si en los últimos 7 días no hay ninguna, la regla no se evalúa (no cuenta como pérdida). La línea base es la de M12: la previa al vuelo (`contexto = null`, M02) o, si no hay, la media de la semana 1.
2. **Cumplimiento semanal:** minutos de ejercicio de los últimos 7 días frente a **840 min** (`ejercicio_meta_semanal_min`; unas 2 h diarias, [resumen de enfermedades §3](../../../docs/investigacion/04-enfermedades-resumen.md#3-atrofia-muscular)). Solo se muestra; no genera alertas.
3. Una sesión no puede solaparse con otra del mismo tripulante (`422`).
4. **Nutrición:** se muestran los totales del día UTC (kcal, proteína y mL). No hay metas ni alertas: no hay valores con fuente para una misión concreta.
5. **Fuerza:** se muestra el cambio porcentual de cada mano frente a la **primera medición de la misión**. No genera alertas.
6. **Glucosa (HU-10.05):** regla `GLUCOSA` de M16 (< 70 o > 180 mg/dL en 2 lecturas consecutivas → ⚠️ aviso del sistema `cuerpo`; [Fase 1 §3](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#3-qué-monitorear-parámetros-rangos-y-alertas)), evaluada por el motor genérico de M12. El simulador genera una lectura cada 5 minutos con subidas tras las comidas que no superan 180 mg/dL en E0.

## 4. Contratos

| Ofrece (`FisicoMetabolico\Contratos\…`) | Uso |
|---|---|
| `ConsultaEjercicio::minutosSemana(tripulanteId)` | M14 |
| `ConsultaNutricion::totalesDia(tripulanteId, fecha)` | M14 |
| `ConsultaFuerza::historial(tripulanteId)` | M14 |

| Usa | De |
|---|---|
| `ConsultaObservaciones::serie()` (masa, FC y glucosa) | M03 |
| `ConsultaTendencias` | M12 (pendiente de la masa) |

## 5. Permisos

| Acción | Roles |
|---|---|
| Registrar y ver el ejercicio, la nutrición y la fuerza propios | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver los datos físicos y metabólicos de la tripulación | `ver-datos-tripulacion` (`cmo`, `medico_vuelo`) |

## 6. Archivos

```
app/Modules/FisicoMetabolico/
├── FisicoMetabolicoServiceProvider.php
├── routes.php
├── Contratos/{ConsultaEjercicio,ConsultaNutricion,ConsultaFuerza}.php
├── Domain/
│   ├── Enums/{EquipoEjercicio,TipoIngesta,Mano}.php
│   └── Reglas/CalculadoraCumplimiento.php
├── Application/{RegistrarSesionEjercicio,RegistrarIngesta,RegistrarFuerza,ResumenFisico}.php
├── Http/
│   ├── Controllers/Web/{EjercicioController,NutricionController,FuerzaController,FisicoController}.php
│   ├── Controllers/Api/{EjercicioController,NutricionController,FuerzaController}.php
│   └── Requests/{SesionEjercicioRequest,IngestaRequest,FuerzaRequest}.php
├── Infrastructure/
│   ├── Models/{SesionEjercicio,RegistroNutricion,PruebaFuerza}.php
│   └── Repositorios/*.php
├── Database/Migrations/{create_sesiones_ejercicio,create_registros_nutricion,create_pruebas_fuerza}.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Una sesión que se solapa con otra devuelve `422`.
- [ ] Línea base 70 kg y media semanal 66,4 kg (−5,1 %) → aviso; 66,6 kg (−4,9 %) → nada.
- [ ] Sin `fc_media`, se calcula **al mostrar la sesión** con las lecturas de FC del intervalo (al guardar todavía pueden no existir).
- [ ] Una corrección de una sesión (`anula_id`) no choca por solapamiento con la sesión que anula.

Detalle de cada historia en las [historias de backend](HU/README.md).
- [ ] Un registro `liquido` con `kcal` devuelve `422`.
- [ ] Totales del día: dos comidas de 800 y 900 kcal y un líquido de 500 mL → 1.700 kcal y 500 mL.
- [ ] Glucosa 68 y 66 mg/dL consecutivas → aviso; 68 y 75 → nada.
- [ ] Sin medición de masa en los últimos 7 días, `MASA_PERDIDA` no se evalúa.
- [ ] **E0 (semilla 42, 30 días): ningún aviso de masa ni de glucosa.**
- [ ] El token del simulador sin `tripulante_id` recibe `422`; con el token de la PWA se ignora el `tripulante_id` del cuerpo.
- [ ] 30 días de glucosa simulada (semilla 42) nunca bajan de 70 ni superan 180.
