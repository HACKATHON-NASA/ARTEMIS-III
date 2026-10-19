# Simulador de datos (Python)

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Tipo** | Herramienta externa (no es un módulo de Laravel) |
| **Carpeta** | `simulador/astrohealth_sim/` |
| **Épica** | EP-17 |
| **Responsable** | Miguel (rol fijo: simulador) |
| **Sprints** | 1 (HU-17.01, HU-17.04) · 2 (HU-17.02; parte de simulación de HU-05.07, HU-10.03 y HU-10.04) · 3 (HU-17.03; parte de simulación de HU-08.03 y HU-10.05) |
| **Escenarios** | Los genera todos: E0–E8 |
| **Tecnología** | Python 3.12 · NumPy · NeuroKit2 · httpx · pytest |

## 2. Propósito

Generar **datos fisiológicos, conductuales y ambientales verosímiles** de una tripulación ficticia y enviarlos a la API **exactamente igual que un sensor real**, con su propio token de dispositivo. Permite desarrollar, probar y demostrar el sistema sin hardware ([04 §3](../../docs/propuesta/04-manejo-de-datos.md#3-generación-de-datos-simulados-realistas)).

## 3. Alcance

| Hace | No hace |
|---|---|
| Relleno histórico de N días y emisión en tiempo real | Leer la base de datos (solo usa la API) |
| Perfiles individuales reproducibles con semilla | Controlar el enlace nave–Tierra (se hace con comandos de M15) |
| Escenarios E0–E8 inyectables | Generar fotografías de síntomas ni PDF de laboratorio |
| Reintentos sin pérdidas ni duplicados | |

## 4. Estructura

```
simulador/
├── pyproject.toml
├── astrohealth_sim/
│   ├── __main__.py           # python -m astrohealth_sim …
│   ├── cli.py                # Comandos `historico` y `vivo`
│   ├── config.py             # URL de la API, token y valores por defecto
│   ├── perfiles.py           # Línea base individual de cada tripulante
│   ├── agenda.py             # Sueño, ejercicio, tests, tomas de PA, exámenes
│   ├── ids.py                # UUID deterministas a partir de la semilla
│   ├── generadores/
│   │   ├── vitales.py        # FC, SpO₂, FR, temperatura, PA, RMSSD, actividad
│   │   ├── ecg.py            # Ventanas de ECG con NeuroKit2
│   │   ├── sueno.py
│   │   ├── cognicion.py      # Sesiones PVT, aritmética y memoria con respuestas ítem a ítem
│   │   ├── escalas.py
│   │   ├── ambiente.py       # pCO₂, VOC, partículas y ruido por módulo de la nave
│   │   ├── radiacion.py      # Tasa de dosis
│   │   ├── examenes.py
│   │   ├── sintomas.py
│   │   ├── ejercicio.py      # Sesiones, masa corporal y fuerza de prensión
│   │   ├── nutricion.py      # Comidas y líquidos
│   │   └── glucosa.py        # Serie del CGM
│   ├── escenarios/
│   │   ├── base.py           # Interfaz Escenario
│   │   └── e0_normal.py … e8_hipoxemia.py
│   └── cliente_api.py        # Lotes, endpoints y reintentos
└── tests/
```

## 5. Interfaz de línea de comandos

```bash
# Relleno histórico: 30 días, 4 tripulantes, perfil de órbita baja
python -m astrohealth_sim historico --dias 30 --tripulantes 4 --perfil leo --semilla 42

# Histórico con varios escenarios a la vez (en una base limpia: ver §5.1)
python -m astrohealth_sim historico --dias 30 --tripulantes 4 --perfil leo --semilla 42 \
       --escenario E4:T2:20 --escenario E7:T3:1 --escenario E3:-:25

# Tiempo real: perfil de Marte, escenario E4 a T2 a los 10 minutos
python -m astrohealth_sim vivo --perfil marte --escenario E4:T2:10m
```

| Opción | Valores | Por defecto |
|---|---|---|
| `--perfil` | `leo`, `luna`, `marte` | `leo` |
| `--semilla` | Entero | `42` |
| `--tripulantes` | 1 – 6 (`T1`…`T6`) | `4` (la misión de demostración solo tiene T1–T4; para usar T5 y T6 hay que registrarlos antes en M02, si no la API responde `422 tripulante_desconocido`) |
| `--inicio-mision` | Fecha `AAAA-MM-DD` | Hoy (UTC) − (`--dias` − 1), igual que la fecha por defecto de `MisionDemoSeeder` (M02); si la misión se fijó con `MISION_INICIO`, se pasa esa misma fecha |
| `--escenario` | `EN:tripulante:inicio`, repetible. `inicio` es el día de misión en `historico` y el retraso en `vivo` (`10m`, `1h`); `-` en lugar del tripulante para los escenarios globales (E2, E3, E5) | Ninguno (E0) |
| `--velocidad` | Factor de aceleración en `vivo` | `1` |

Variables de entorno: `ASTROHEALTH_API_URL` (nodo nave) y `ASTROHEALTH_TOKEN` (token de un dispositivo de tipo `simulador`, [M01](../M01-identidad/database/README.md#1-tablas)).

### 5.1 Escenarios en histórico: siempre en una base limpia

Los `id` de las lecturas y de los registros salen de la semilla y del momento de la medición (§6.1). Por eso, **si se carga primero E0 y después se repite el histórico con un escenario, la API descarta como duplicadas todas las lecturas del escenario** (tienen los mismos `id`) y el escenario no aparece. Y si los `id` fueran distintos, quedarían dos lecturas contradictorias para el mismo minuto. La regla es:

1. Para un histórico con escenarios: `php artisan migrate:fresh --seed` y **un solo comando** `historico` con todos los `--escenario` juntos.
2. Para inyectar un escenario sobre una misión ya cargada (la demo en vivo): usar `vivo`, que genera datos desde ahora hacia adelante y no choca con el histórico.
3. `historico` comprueba antes de empezar si la API ya tiene lecturas de ese periodo (`GET /api/v1/ingesta/periodo?desde=&hasta=` con el token del simulador, [M03](../M03-ingesta/backend/HU/HU-03.01-envio-de-lecturas-por-lotes-con-token.md)) y, si las tiene y hay escenarios, se detiene con el mensaje "La base ya tiene datos de este periodo; los escenarios se perderían como duplicados. Usa una base limpia o `vivo`".

## 6. Reglas de generación

### 6.1 Reproducibilidad

- Un único generador `numpy.random.Generator(PCG64(semilla))` por tripulante y fuente (semilla derivada de la semilla global y del código).
- Todos los `id` y `lote_id` son UUID **derivados del generador** (`ids.py`). Los `id` de las lecturas siguen el formato de la versión 7 (los primeros 48 bits son el milisegundo de `medido_en` y el resto sale del generador), para que MySQL los inserte en orden ([M03 base de datos §5](../M03-ingesta/database/README.md#5-volumen)). Por eso, repetir el mismo comando produce los mismos identificadores y la API responde `200` sin duplicar ([HU-17.01](../../docs/propuesta/05-estructura-scrum.md#hu-1701--generar-el-historial-de-una-misión)).

### 6.2 Perfiles individuales (sorteados por tripulante)

| Variable | Rango de la base | Fuente |
|---|---|---|
| FC en reposo | 55 – 70 lpm | 04 §3.3 |
| SpO₂ | 97 – 99 % | 04 §3.3 |
| FR | 12 – 16 rpm | 04 §3.3 |
| Temperatura central | 36,6 – 37,0 °C | 04 §3.3 |
| PA | 110 – 125 / 70 – 80 mmHg | 04 §3.3 |
| RMSSD | 30 – 60 ms | 04 §3.3 |
| Sueño | 6,5 – 7,0 h | 04 §3.3 |
| Mediana del PVT | 250 – 300 ms | 04 §3.3 |
| Aritmética | precisión 92 – 98 %, 2 – 4 s por ítem | 04 §3.3 |
| Memoria | 6 – 8 elementos | 04 §3.3 |
| Glucosa basal | 85 – 100 mg/dL | Parámetro de diseño † (dentro de 70–99 en ayunas, Fase 1 §3) |
| Fuerza de prensión | H 40 – 50 kg, M 25 – 35 kg | Parámetro de diseño † |
| Sexo | T1 M, T2 F, T3 M, T4 F, T5 M, T6 F | Fijo |

### 6.3 Variables continuas (una lectura por minuto)

Fórmula de [04 §3.2](../../docs/propuesta/04-manejo-de-datos.md#32-fórmula-general-para-variables-continuas), con el mínimo circadiano a las **04:00 UTC**:

```
valor(t) = base + A · sin(2π · (hora(t) − 10) / 24) + actividad(t) + deriva · dia(t) + escenario(t) + N(0, σ)
```

| Variable | A (circadiana) | σ | Ejercicio |
|---|---|---|---|
| FC | 5 lpm | 2 lpm | +40 a +70 lpm; vuelve a la base en 5 min al terminar |
| SpO₂ | — | 0,5 % (máximo 100) | — |
| FR | 1 rpm | 1 rpm | +8 a +15 rpm |
| Temperatura | 0,3 °C | 0,1 °C | +0,5 a +1,0 °C |

Cada lectura lleva su `contexto` según la agenda.

### 6.4 Agenda diaria (UTC)

| Hora | Actividad | Contexto |
|---|---|---|
| 23:00 – 07:00 | Sueño | `sueno` |
| 07:00, 12:30 y 19:00 | Comidas (registro de nutrición) † | `reposo` |
| 07:30 | Toma de PA (y masa corporal y fuerza de prensión los lunes) | `reposo` |
| 08:00 | PVT (3 min) + aritmética (20 ítems) + memoria + escalas diarias (escalas semanales los lunes) | `reposo` |
| 10:00 – 11:00 y 16:00 – 17:00 | Ejercicio (ARED y cinta) | `ejercicio` |
| 14:00 | Ventana de ECG de 60 s | `reposo` |
| Resto | Actividad normal | `reposo` |

Exámenes: Hb y Hto cada lunes; vitamina D, OCT y Doppler los días 1 y 31.

### 6.5 Otras fuentes

| Fuente | Regla en E0 |
|---|---|
| Sueño | Noche ~ Normal(base; 0,3 h); eficiencia ~ Normal(91 %; 2,5 %), recortada a 60 – 98 %. Como el servidor calcula la eficiencia con el tiempo en cama, el simulador envía `acostado_en = despertado_en − duración / eficiencia` ([M06](../M06-sueno/backend/HU/HU-06.01-ver-cuanto-y-que-tan-bien-dormi.md)) |
| Cognición | PVT: tiempos log-normales alrededor de la mediana del perfil con **σ_ln = 0,20** †; intervalo U(2, 10) s; falsas salidas Poisson(0,2) por sesión †; mejora un 5 % por práctica en las sesiones 1 a 3. Aritmética: errores binomiales con la precisión del perfil y tiempo por ítem log-normal alrededor de la base del perfil con **σ_ln = 0,30** †. Son los valores del Monte Carlo de `COG_DETERIORO`: si cambian, se repite |
| Memoria | `longitud_max` = base en el 90 % de las sesiones y base ± 1 en el 10 % † (nunca 2 por debajo: E0 no puede formar rachas de z ≥ 2) |
| Escalas | Ánimo 7 ± 1, estrés 3 ± 1, fatiga 3 ± 1 (enteros 0 – 10) |
| pCO₂ | 2,0 ± 0,3 mmHg por módulo (`lab`, `habitacional`, `nodo1`) |
| VOC, partículas, ruido | Cada 5 min por módulo: 0,3 ± 0,05 mg/m3, 10 ± 3 µg/m3, 58 ± 2 dB † |
| Glucosa | Cada 5 min: basal + subida de 30 – 50 mg/dL con pico 45 min después de cada comida y vuelta a la basal en 2 h + N(0; 3) †; en E0 queda entre 75 y 155 mg/dL |
| Nutrición | 3 comidas (total diario Normal(2.700; 200) kcal †, 15 – 20 % de proteína) y 4 líquidos de 500 – 700 mL † |
| Fuerza | Lunes, ambas manos: base + N(0; 1,5) kg † |
| Masa corporal | Lunes a las 07:30: base del perfil (H 70 – 85 kg, M 55 – 70 kg †) − 0,2 % por semana + N(0; 0,3) kg †. En 30 días la pérdida es de ~1 %, lejos del 5 % de `MASA_PERDIDA` |
| Ejercicio | Dos sesiones al día que coinciden con la agenda (10:00 ARED y 16:00 cinta, 60 min); se envían a `/fisico/ejercicio` con las mismas horas en que las lecturas llevan `contexto = ejercicio` |
| Radiación | `leo` 0,5 mSv/d, `marte` y `luna` 1,8 mSv/d, ± 10 % |
| ECG | `neurokit2.ecg_simulate()` a 250 Hz con la FC del momento; RR con CV ≈ 0,05; QTc 400 ± 15 ms |
| Exámenes | Hb del perfil (H 14,5, M 13,2 g/dL) − 0,05 g/dL por semana + N(0; 0,2); **Hto = 3 × Hb + N(0; 0,1) %** (con N(0; 0,6) la tendencia del Hto daba un 8,4 % de avisos falsos, [`tendencia_hematocrito.py`](../../herramientas/montecarlo/tendencia_hematocrito.py)); **vitamina D** U(35; 50) + N(0; 3) ng/mL †; OCT Δ TRT N(5; 3) µm; Doppler grado 1 o 2 |
| Síntomas | Ninguno |

### 6.6 Envío

- `historico` **nunca genera datos posteriores a la hora actual**: si el periodo pedido llega al futuro, se detiene en el minuto actual y lo avisa (M03 rechaza las lecturas con más de 5 minutos en el futuro).
- **Identificadores de los dispositivos:** los de la convención de M02 (`astroskin-T<n>`, `oximetro-T<n>`, `dosimetro-T<n>`, `cgm-T<n>`, `masa-T<n>`, `ambiente-<módulo>`…), que crea `MisionDemoSeeder` ([M02 HU-02.02](../M02-mision/database/HU/HU-02.02-registrar-tripulantes-y-dispositivos.md)). El `dispositivo_id` de cada lote debe ser uno de ellos.
- Lecturas continuas: un lote **por dispositivo** (wearable, oxímetro, dosímetro) y hora en `historico` (`modo: historico`) y por minuto en `vivo` (`modo: vivo`). Los lotes de **cada dispositivo** se envían **en orden de tiempo**: M12 calcula la línea base inicial con el primer dato del día 8 de cada código y necesita que los días 1 a 7 de ese dispositivo ya estén cargados ([M12](../M12-analitica/backend/HU/HU-12.01-linea-base-de-referencia-por-tripulante-y-variable.md)).
- Ambiente: un lote por módulo de la nave con `ubicacion`.
- Glucosa: un lote por tripulante y hora (`historico`) o por 5 minutos (`vivo`), desde el dispositivo `cgm` del tripulante.
- Masa corporal: un lote con una lectura (`29463-7`, `contexto = reposo`) los lunes a las 07:30, desde el dispositivo `medidor_masa` del tripulante (`masa-T<n>`).
- Ventanas de ECG: una al día a las 14:00 por tripulante, enviada a `/api/v1/signos-vitales/ecg` en nombre del `wearable` del tripulante.
- Cada fuente usa su endpoint: `/api/v1/ingesta/lotes`, `/api/v1/signos-vitales/ecg`, `/api/v1/sueno/registros`, `/api/v1/psicologia/sesiones`, `/api/v1/psicologia/escalas`, `/api/v1/examenes/resultados`, `/api/v1/sintomas`, `/api/v1/fisico/ejercicio`, `/api/v1/fisico/nutricion`, `/api/v1/fisico/fuerza`.
- En los endpoints de registros (`/psicologia/*`, `/sueno/registros`, `/examenes/resultados`, `/sintomas`, `/fisico/*`) el simulador escribe con el token de su dispositivo `simulador` y envía siempre el `tripulante_id` (código) en el cuerpo.
- Reintentos ante error de red o `5xx`: espera creciente de 1, 2, 4… hasta 60 s, sin límite de intentos; un `422` detiene el comando y muestra el error.
- **Un `404` en un endpoint** (todavía no construido en ese sprint, como `/signos-vitales/ecg` antes del Sprint 4) **se omite**: el simulador muestra una vez "Endpoint <ruta> no disponible: se omiten sus datos" y sigue con lo demás.

## 7. Escenarios (HU-17.03)

Coinciden con [04 §7](../../docs/propuesta/04-manejo-de-datos.md#7-escenarios-de-simulación). "T" es el tripulante indicado con `--tripulante`.

| ID | Qué inyecta exactamente | Resultado esperado | Módulos |
|---|---|---|---|
| **E0** | Nada | 0 avisos y 0 críticos en 30 días con `--perfil leo --semilla 42` | Todos |
| **E1** | En el primer bloque de ejercicio: FC 150 – 170 lpm y una ventana de ECG con RR irregular (CV ≈ 0,30) | `ECG_IRREGULAR` crítico + caso `arritmia` | M04, M12, M13 |
| **E2** | Tasa de dosis × 50 para **todos** durante 2 h | `DOSIS_SPE` crítico global ("ir al refugio") | M09, M12, M13 |
| **E3** | Módulo `lab` a 4 – 5 mmHg durante 6 h; cefalea de intensidad 5 – 7 en 2 tripulantes dentro de esas horas | `CO2_CEFALEA` aviso para cada tripulante con cefalea y `CO2_ALTO` aviso global del módulo `lab`, con la recomendación de ventilar | M08, M11, M12 |
| **E4** | 5 noches de 4,5 – 5,0 h (eficiencia 80 %); mediana del PVT **+8 %** por día (antes +5 %, poco detectable con la σ mínima relativa) y lapsos ×3; memoria −1 a −2 elementos; RMSSD −30 %; fatiga 7 – 8 | Aviso `COG_DETERIORO` (suele escalar a crítico en los últimos días por los lapsos) con contexto de sueño y fatiga, y aviso `SUENO_MAL` | M05, M06, M12 |
| **E5** | Con la ventana sin enlace activa, un histórico de varios días que incluye una alerta crítica; después se cierra la ventana (receta de [M15 backend §3.4](../M15-sincronizacion/backend/README.md#34-ventanas-sin-enlace-e5)) | Operación autónoma y entrega 1 → 2 → 3 sin pérdidas; la crítica llega primero | M15 |
| **E6** | Dispositivo de T: calidad 0,2 – 0,4 durante 10 min y 3 lecturas de FC = 400. **Solo con `vivo`**: el aviso de colocación solo mira datos en vivo ([M03 §3.1](../M03-ingesta/backend/README.md#31-procesamiento-de-un-lote)) | Artefactos marcados, 3 rechazos, aviso de colocación y **ninguna** alerta | M03 |
| **E7** | Hb de T (hombre): 14,1 → 13,4 → 12,7 → 12,0 g/dL en 4 lunes seguidos (el Hto, derivado, también baja) | `EXAMEN_TENDENCIA` aviso de la Hb (y del Hto) | M07, M12 |
| **E8** | SpO₂ de T: 91 % durante 6 min y después 88 % durante 5 min | Aviso (minuto 3) → confirmación automática (segundo hallazgo en el minuto 6) → escala a crítico + caso `hipoxemia` | M04, M12, M13 |

> **E0 con otros perfiles:** con la misión de demostración (60 días), ni siquiera `--perfil marte` supera el 80 % del límite de carrera: el máximo es ~228 mSv (T1: 120 previos + 1,8 mSv/d × 60). Una misión real a Marte sí lo superaría ([Fase 1 §2.5](../../docs/investigacion/02-monitorizacion-salud-fase1.md#25-radiación-espacial)); para enseñar el aviso `DOSIS_PROYECCION` hace falta una `fecha_fin` de unos 200 días o más ([M09](../M09-radiacion/backend/HU/HU-09.01-ver-mi-dosis-acumulada-frente-al-limite-de-carrera.md)). E0 se valida con `leo`.

## 8. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-17.01 Historial de N días | 1 | 30 días reproducibles de vitales, sueño, cognición, escalas, ejercicio y exámenes |
| HU-17.04 Series de ambiente y radiación | 1 | pCO₂ por módulo y tasa de dosis |
| HU-17.02 Emisión en tiempo real | 2 | Comando `vivo` con lotes por minuto |
| HU-17.03 Escenarios E0–E8 | 3 | Cada escenario produce exactamente el resultado de la tabla 7 |

Miguel también hace la parte de simulación de HU-05.07 (memoria), HU-10.03 (nutrición) y HU-10.04 (fuerza) en el Sprint 2, y de HU-08.03 (VOC, partículas y ruido) y HU-10.05 (glucosa) en el Sprint 3.

## 9. Pruebas mínimas (pytest)

- [ ] Dos ejecuciones con la misma semilla generan los mismos lotes, byte a byte (salvo `enviado_en`).
- [ ] Ninguna variable de E0 cruza los umbrales de [M16 backend §3.2](../M16-configuracion/backend/README.md#32-umbrales-sembrados) en el contexto correspondiente.
- [ ] Las simulaciones de Monte Carlo de [`herramientas/montecarlo/`](../../herramientas/README.md) (riesgo de avisos falsos en las reglas estadísticas: cognición, tendencia de Hb y sueño) se ejecutan en CI con los parámetros actuales y fallan si alguno supera el 1 %.
- [ ] Un histórico con escenarios sobre una base que ya tiene datos de ese periodo se detiene con el mensaje de §5.1.
- [ ] `--escenario E4:T2:20 --escenario E7:T3:1` inyecta los dos escenarios en el mismo histórico.
- [ ] El mínimo circadiano de la FC cae entre las 03:00 y las 05:00 UTC.
- [ ] Tras un `5xx` simulado, el lote se reenvía con el mismo `lote_id`.
- [ ] Cada escenario modifica solo al tripulante y la ventana de tiempo indicados.
- [ ] En E0 ninguna glucosa sale de 70 – 180 mg/dL y ninguna `longitud_max` queda 2 por debajo de la base.
