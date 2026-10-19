# M06 · Sueño · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

| Método | Ruta | Autenticación | Descripción |
|---|---|---|---|
| `POST` | `/api/v1/sueno/registros` | Solo token: PWA `registros:escribir`, o de dispositivo `lecturas:escribir` (actígrafo asignado al tripulante, o `simulador`) | Un registro por noche; idempotente por `id` |

Cuerpo: `{ id, tripulante_id, acostado_en, despertado_en, duracion_h, medicacion, medicacion_nombre, luz_percibida, ruido_percibido }`. **`noche`, `eficiencia_pct` y `origen` los calcula el servidor** (si llegan en el cuerpo, se ignoran): la eficiencia es `duracion_h / horas en cama × 100` y el origen sale del token (`actigrafia`, `simulador` o `manual`); el token de cualquier otro tipo de dispositivo recibe `403`. Detalle en las [historias de backend](HU/README.md).

- **De quién es:** con el token de la PWA, `tripulante_id` se toma del usuario y se ignora el del cuerpo; con el del actígrafo, es el tripulante asignado al dispositivo; con el del simulador, el `tripulante_id` (código) es obligatorio.
- **La noche no la envía el cliente:** el servidor la calcula con la regla 1. Si el cuerpo trae un campo `noche`, se ignora.
- **Registro manual solo de medicación:** `acostado_en` y `duracion_h` son opcionales (los dos o ninguno) con el token de la PWA. `despertado_en` es **siempre** obligatorio: con él se calcula la noche (el formulario lo prellena con la hora de despertar de la agenda).

## 2. Eventos

| Publica | Consume |
|---|---|
| `ObservacionRegistrada` × 2 por noche (`AH-SUENO-DURACION`, `AH-SUENO-EFICIENCIA`), con `medido_en = despertado_en` y `contexto = sueno`. Solo se publica cuando el registro vigente de la noche **tiene** duración y eficiencia, y se vuelve a publicar si una fusión las cambia; `observacion_id` es el `id` del registro vigente | — |

## 3. Reglas de negocio

1. **A qué noche pertenece un registro:** `noche` = fecha UTC de (`despertado_en` − 12 h). Acostarse a las 23:00 o a las 00:30 y despertar a las 07:00 o 07:30 dan **la misma noche** (la del día anterior al despertar). La regla anterior ("fecha en que empezó el sueño") partía en dos fechas distintas las noches en que la hora de acostarse cruzaba la medianoche (el simulador se acuesta a las 23:00 ± 30 min, así que pasaba casi una noche de cada dos) y luego fusionaba noches diferentes.
2. `duracion_h` no puede superar `despertado_en − acostado_en`; si lo hace → `422`.
3. **Regularidad** = desviación estándar **poblacional**, en minutos, de la hora de acostarse de las últimas 7 noches (`sueno_regularidad_noches`); cada hora se expresa en minutos desde las 12:00 para que cruzar la medianoche no rompa el cálculo. Con menos de 3 noches (`sueno_regularidad_min_noches`), no se calcula.
4. **Aviso de mal sueño (`ReglaMalSueno`, regla `SUENO_MAL`):** una noche es *mala* si `duracion_h < 6` **o** `eficiencia_pct < 85` (valores de la fila `SUENO_MAL` de la misión, M16); **3 noches malas consecutivas** (la `persistencia_valor` de esa fila) (noches de calendario seguidas, con su registro vigente; una noche sin registro corta la racha) → aviso del sistema `sueno` ([04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)). La regla vive en M06, implementa `Analitica\Contratos\ReglaAnalitica` y toma los dos valores del umbral `SUENO_MAL` de M16, porque el motor genérico de M12 no combina dos variables con "o". Se activa solo con el evento de `AH-SUENO-DURACION` (cada noche publica dos y se evaluaría dos veces) y revisa las tres ventanas de 3 noches que contienen la noche recibida, para que una noche registrada tarde también complete una racha.
5. **Simulación** ([04 §3.3](../../../docs/propuesta/04-manejo-de-datos.md#33-parámetros-sugeridos-por-variable)): base por tripulante de 6,5 a 7,0 h con ruido de 0,3 h y eficiencia Normal(91 %; 2,5 %); E4 inyecta 5 noches de mal sueño. Con estos parámetros, 20.000 misiones normales simuladas dieron un aviso falso de `SUENO_MAL` en el 0,2 %.
6. **Fusión:** si llega un segundo registro de la misma noche, el caso de uso crea uno nuevo que anula al anterior y combina ambos: duración, eficiencia y horarios del registro medido (actígrafo o simulador, si alguno lo es) y medicación, luz y ruido del registro manual. La regla evalúa siempre el registro **vigente** de cada noche, nunca los anulados. Para que dos registros nuevos de la misma noche no queden los dos vigentes, el caso de uso usa un bloqueo con nombre (`Cache::lock`) por tripulante y noche.

## 4. Contratos

| Ofrece (`Sueno\Contratos\…`) | Uso |
|---|---|
| `ConsultaRegistrosSueno::ultimasNoches(tripulanteId, n)` | M05 y M12 (contexto de las alertas), M14 |
| `ConsultaRegistrosSueno::promedioHoras(tripulanteId, noches)` | M05 (contexto: "4,9 h en las últimas 4 noches") |
| `ReglaMalSueno` | Registrada en el motor de M12 |

| Usa | De |
|---|---|
| `ConsultaTripulantes` | M02 |
| `ReglaAnalitica`, umbral `SUENO_MAL` | M12, M16 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Registrar y ver el sueño propio | `ver-datos-propios` (`astronauta`, `cmo`) |
| Ver el sueño de la tripulación | `ver-datos-tripulacion` (`cmo`, `medico_vuelo`) |

## 6. Archivos

```
app/Modules/Sueno/
├── SuenoServiceProvider.php
├── routes.php
├── Contratos/ConsultaRegistrosSueno.php
├── Domain/
│   ├── Enums/OrigenSueno.php
│   └── Reglas/{CalculadoraRegularidad,FusionRegistrosNoche,ReglaMalSueno}.php
├── Application/{RegistrarNoche,ResumenSueno}.php
├── Http/
│   ├── Controllers/Web/SuenoController.php
│   ├── Controllers/Api/RegistroSuenoController.php
│   └── Requests/RegistroSuenoRequest.php
├── Infrastructure/
│   ├── Models/RegistroSueno.php
│   └── Repositorios/RegistrosSuenoEloquent.php
├── Database/Migrations/create_registros_sueno.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Acostarse el 12 a las 23:40 y despertar el 13 a las 07:00 → `noche = 12`; acostarse el 14 a las 00:20 y despertar a las 07:10 → `noche = 13`: son dos noches distintas y no se fusionan.
- [ ] Regularidad correcta cuando las horas de acostarse cruzan la medianoche (23:30 y 00:30 → 30 min de desviación respecto a la media 00:00).
- [ ] Actigrafía y manual de la misma noche → un solo registro efectivo.
- [ ] Un registro con duración mayor que el tiempo en cama devuelve `422`.
- [ ] Noches corta, de baja eficiencia y corta seguidas → aviso; dos malas y una buena → nada; dos malas, una noche sin registro y una mala → nada.
- [ ] Un registro manual solo con la medicación no publica `ObservacionRegistrada`; al fusionarse con la actigrafía, sí.
- [ ] El token del simulador sin `tripulante_id` recibe `422`; el de un oxímetro, `403`.
- [ ] Una eficiencia enviada en el cuerpo se ignora: la calcula el servidor con la duración y el tiempo en cama.
- [ ] Una noche mala que llega tarde y completa una racha genera el aviso.
- [ ] Dos registros de la misma noche que llegan a la vez dejan un solo vigente.
