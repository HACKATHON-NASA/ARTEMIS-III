# M08 · Ambiente · Backend

> [← Ficha del módulo](../README.md) · **Backend** · [Frontend](../frontend/README.md) · [Base de datos](../database/README.md)

Capa **Laravel** del módulo: API, eventos, reglas de negocio, contratos, permisos, archivos y pruebas. Las tablas están en [Base de datos](../database/README.md) y las páginas Vue en [Frontend](../frontend/README.md).

---

## 1. API REST

M08 no expone API: las lecturas entran por `POST /api/v1/ingesta/lotes` (M03) con `ubicacion` = código del módulo de la nave.

## 2. Eventos

| Publica | Consume |
|---|---|
| — (la regla produce hallazgos dentro de M12) | La regla se activa en M12 con `LoteIngerido` de `AH-PCO2` y con `ObservacionRegistrada` de `AH-SINTOMA-CEFALEA` (M11) |

## 3. Reglas de negocio

### `ReglaCo2Cefalea` (HU-08.02)

| Elemento | Definición |
|---|---|
| Condición ambiental | Algún módulo de la nave tiene un **promedio horario de pCO₂ > 3,0 mmHg** (hora de reloj UTC) en las últimas 24 h (`co2_cefalea_ventana_h` de M16) |
| Condición clínica | El tripulante registró una **cefalea** (intensidad ≥ `co2_cefalea_intensidad_min`, hoy 1) en esas mismas 24 h (`co2_cefalea_ventana_h`) |
| Resultado | ⚠️ Aviso del sistema `ambiente` para ese tripulante |
| Explicación | "Cefalea de T3 (intensidad 6) con pCO₂ de 4,6 mmHg en el módulo lab (promedio horario máximo de las últimas 24 h). Recomendación: ventilar el módulo lab." |
| Origen | Umbral de [04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales) (`pCO₂ > 3 mmHg + síntomas`, promedio de 1 h), editable en M16 |

Como no se sabe en qué módulo estaba cada tripulante, la regla usa **el módulo con el mayor promedio horario** de la ventana y lo nombra en la recomendación. Se evalúa al llegar una cefalea (ese tripulante) y al llegar un lote de pCO₂: si ninguna hora del lote supera el límite, termina sin consultar síntomas; si alguna lo supera, evalúa a cada tripulante con cefalea en la ventana.

### `CO2_ALTO`: CO₂ alto aunque nadie tenga síntomas (HU-08.02)

La regla de correlación solo avisa si alguien reporta una cefalea. Sin otra regla, un módulo a 8 mmHg **no generaría ninguna alerta** mientras nadie anotara un síntoma, y el CO₂ alto también causa fatiga y errores en los procedimientos antes de la cefalea ([Fase 1 §2.11](../../../docs/investigacion/02-monitorizacion-salud-fase1.md#a-co₂-elevado)). Por eso hay una regla ambiental pura:

| Elemento | Definición |
|---|---|
| Regla (M16) | `CO2_ALTO`, familia `CO2_MODULO`, motor genérico de M12 evaluado **por módulo de la nave** |
| Condición | `AH-PCO2` > **3,0 mmHg** en todas las lecturas válidas de **60 minutos** (`valor` y persistencia de la fila `CO2_ALTO`; el límite operativo de la ISS, 04 §8) |
| Resultado | ⚠️ Aviso **global** (`tripulante_id = null`), sistema `ambiente`: "pCO₂ 4,6 mmHg sostenido 60 min en el módulo lab (límite operativo 3,0). Recomendación: ventilar el módulo lab" |
| E0 | El simulador mantiene 2,0 ± 0,3 mmHg: no se dispara |
| E3 | Se dispara junto con `CO2_CEFALEA`: el CMO ve un aviso global del módulo y un aviso por cada tripulante con cefalea |

No hay nivel crítico: la documentación no tiene un valor de CO₂ crítico con fuente. Si el médico de vuelo lo carga con su fuente, se añade como una regla más de la misma familia (y la alerta escala).

### Umbrales de VOC, partículas y ruido (HU-08.03)

| Regla (M16) | Variable | Operador | Valor | Persistencia | Nivel | Estado inicial |
|---|---|---|---|---|---|---|
| `AMB_VOC` | `AH-VOC` | `mayor` | Sin valor | 12 consecutivos (60 min) | aviso | Inactiva |
| `AMB_PM` | `AH-PM` | `mayor` | Sin valor | 12 consecutivos (60 min) | aviso | Inactiva |
| `AMB_RUIDO` | `AH-RUIDO` | `mayor` | Sin valor | 12 consecutivos (60 min) | aviso | Inactiva |

> La persistencia es de **12 lecturas consecutivas** porque estas series llegan cada 5 minutos: con "60 min", la unidad `min` del motor de M12 (que exige al menos 60 lecturas en la ventana) nunca se habría cumplido.

1. Los límites NASA (SMAC para contaminantes y la norma de ruido NASA-STD-3001) no están transcritos con su fuente en la documentación. Por eso las reglas **nacen inactivas y sin valor** ([04 §8](../../../docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)).
   > ⚠️ **Ojo al buscar la fuente:** los SMAC son límites **por compuesto** (amoníaco, formaldehído…), no para un "VOC total". Si el equipo encuentra un SMAC, la serie simulada debe ser de ese compuesto (por ejemplo, `AH-FORMALDEHIDO`) en lugar de `AH-VOC`, o la fuente debe ser un valor de referencia para compuestos orgánicos totales. Comparar un VOC total con el SMAC de un compuesto sería un error.
   > Para mostrar HU-08.03 en la demo hace falta cargar al menos un límite con su fuente **antes del evento**; si no se consigue, la demo muestra la regla inactiva y lo explica.
2. El médico de vuelo las activa en `Tierra/Umbrales/Edit` cargando **valor y fuente**; M16 rechaza activar un umbral sin ellos.
3. Las evalúa el motor genérico de M12 **por módulo de la nave**. El hallazgo es global (`tripulante_id = null`, sistema `ambiente`) y la explicación nombra el módulo: "VOC 2,4 mg/m3 sostenido 60 min en el módulo lab (límite 2,0; fuente: …)".
4. Dos módulos que superan el mismo límite a la vez forman **una sola alerta** (familia `AMB_VOC`, tripulante nulo) con dos ocurrencias.

### Simulación ([04 §3.3](../../../docs/propuesta/04-manejo-de-datos.md#ambiente-y-radiación-m08-y-m09))

pCO₂ base 2,0 mmHg ± 0,3 por módulo. En E0 ningún promedio horario supera 3,0 mmHg. E3 lleva un módulo a 4–5 mmHg y registra cefaleas. VOC, partículas y ruido se generan **cada 5 minutos** por módulo (parámetros de diseño †: VOC 0,3 ± 0,05 mg/m3, partículas 10 ± 3 µg/m3, ruido 58 ± 2 dB) para no aumentar mucho el volumen del relleno histórico.

## 4. Contratos

| Ofrece (`Ambiente\Contratos\…`) | Uso |
|---|---|
| `ConsultaAmbiente::promedioHorarioMaximo(desde, hasta)` | M05 (contexto de las alertas cognitivas), M12, M14 |
| `ConsultaAmbiente::promediosHorarios(moduloId, codigo, desde, hasta)` | M08 (gráficas y regla) |
| `ConsultaAmbiente::estadoModulo(moduloId)` (`{ promedio_ultima_hora, sobre_limite }`) | Página de F2 |
| `ReglaCo2Cefalea` | Registrada en el motor de M12 |

| Usa | De |
|---|---|
| `ConsultaObservaciones::serieUbicacion()` | M03 |
| `ConsultaModulosNave` | M02 |
| `ConsultaSintomas::enVentana()` | M11 |
| `ReglaAnalitica`, umbral `CO2_CEFALEA` | M12, M16 |
| Umbrales `AMB_VOC`, `AMB_PM`, `AMB_RUIDO` (evaluados por el motor de M12) | M16, M12 |

## 5. Permisos

| Acción | Roles |
|---|---|
| Ver el ambiente de la nave | `ver-ambiente` (`cmo`, `medico_vuelo`, `ingeniero_biomedico`, `admin`) |

## 6. Archivos

```
app/Modules/Ambiente/
├── AmbienteServiceProvider.php
├── routes.php
├── Contratos/ConsultaAmbiente.php
├── Domain/Reglas/ReglaCo2Cefalea.php
├── Application/ResumenAmbiente.php
├── Http/Controllers/Web/AmbienteController.php
├── Infrastructure/Repositorios/AmbienteDesdeObservaciones.php
└── Tests/
```

## 7. Pruebas mínimas (Pest)

- [ ] Promedio horario de 3,1 mmHg + cefalea en la misma ventana → aviso.
- [ ] Promedio horario de 3,1 mmHg sin cefalea → ninguna alerta de correlación.
- [ ] pCO₂ de 4,6 mmHg durante 60 min en `lab` sin ningún síntoma → aviso global `CO2_ALTO` que nombra el módulo; 59 min → nada.
- [ ] E3 → un aviso global `CO2_ALTO` y un aviso `CO2_CEFALEA` por cada tripulante con cefalea.
- [ ] Una lectura aislada de 3,5 mmHg con promedio horario de 2,4 → nada.
- [ ] Cefalea 30 h después del pico → nada.
- [ ] Con `AMB_VOC` inactiva, VOC de 50 mg/m3 durante 2 h → nada.
- [ ] Con `AMB_VOC` activa en 1,0 y 12 lecturas seguidas de VOC 2,0 (60 min) en `lab` → aviso global que nombra el módulo `lab`; con 11, nada.
- [ ] Una lectura de 3,3 mmHg con un promedio de la última hora de 2,1 deja la tarjeta del módulo en "Normal".
- [ ] Activar `AMB_RUIDO` sin fuente → error de validación.
