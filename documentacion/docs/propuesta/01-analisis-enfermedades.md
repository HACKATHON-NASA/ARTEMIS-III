# 01 · Análisis de enfermedades y afecciones

> **AstroHealth** · Propuesta para el NASA International Space Apps Challenge 2026
> Documento 1 de 5 · [← Inicio](../../README.md) · [Siguiente: Módulos del sistema →](02-modulos-del-sistema.md)

---

## Contenido

1. [Propósito y fuentes](#1-propósito-y-fuentes)
2. [Marco de análisis: RIDGE](#2-marco-de-análisis-ridge)
3. [Catálogo de afecciones](#3-catálogo-de-afecciones)
4. [Agrupación en áreas funcionales](#4-agrupación-en-áreas-funcionales)
5. [Priorización para el producto](#5-priorización-para-el-producto)
6. [Conclusiones para el diseño](#6-conclusiones-para-el-diseño)

---

## 1. Propósito y fuentes

Este análisis traduce la investigación del equipo en **requisitos de monitoreo**: para cada afección se responde qué la causa, cómo se detecta, qué dato hay que capturar y qué debe hacer el software con él.

| Documento base | Aporte principal |
|---|---|
| [Monitorización de la salud (Fase 1)](../investigacion/02-monitorizacion-salud-fase1.md) | Magnitudes, parámetros, rangos, sensores y transmisión de datos |
| [Enfermedades de los astronautas (resumen)](../investigacion/04-enfermedades-resumen.md) | Contexto de Artemis III y resumen por afección |
| [Informe de fisiopatología espacial (HRP)](../investigacion/05-informe-fisiopatologia-hrp.md) | Mecanismos fisiopatológicos y contramedidas |
| [Arquitectura de largo plazo (Fase 2)](../investigacion/03-arquitectura-vision-fase2.md) | Funcionalidades, pantallas y escenarios de demo |
| [Proyecto Space Apps](../investigacion/01-proyecto-space-apps.md) | Objetivos, alcance y equipo |

> ⚠️ **Advertencia clínica:** los rangos y umbrales de este documento son **referencias terrestres y sugerencias** tomadas de la documentación. En un sistema real los fijarían médicos de vuelo. AstroHealth es una herramienta de **apoyo**, no un dispositivo médico certificado.

---

## 2. Marco de análisis: RIDGE

La NASA agrupa los peligros del vuelo espacial en cinco amenazas que actúan **a la vez** y se potencian entre sí. Cada afección del catálogo se etiqueta con la amenaza que la origina.

| Código | Amenaza | Qué implica para el software |
|:---:|---|---|
| **R** | Radiación espacial | Dosimetría personal, dosis acumulada frente a límites, alertas de eventos solares |
| **I** | Aislamiento y confinamiento | Cuestionarios, pruebas cognitivas, seguimiento del sueño y del ánimo |
| **D** | Distancia de la Tierra | Autonomía médica, soporte de decisiones a bordo, sincronización diferida |
| **G** | Campos gravitacionales alterados | Tendencias longitudinales de hueso, músculo, fluidos y ojos |
| **E** | Entornos hostiles y cerrados | Fusionar datos ambientales (CO₂, aire, ruido) con los fisiológicos |

---

## 3. Catálogo de afecciones

**Leyenda de columnas**
- **Origen:** amenaza RIDGE que la causa.
- **Indicadores:** síntomas o señales que permiten detectarla.
- **Datos a monitorear:** variables que el sistema debe capturar.
- **Abordaje del software:** qué hace AstroHealth con esos datos.

### 3.1 Cardiovascular y hematológico

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Redistribución de fluidos** | G | Sin gravedad, ~2 L de líquido migran hacia el tórax y la cabeza en 6–10 h | Afecta a toda la tripulación desde el primer día | Cara hinchada, congestión nasal, cefalea | Masa corporal, síntomas, PA | Registro de síntomas y tendencia de masa corporal; correlación con SANS |
| **Intolerancia ortostática** | G | Menor volumen plasmático y barorreflejo alterado al volver a la gravedad | ~20–30 % tras vuelos cortos; > 60 % tras ~6 meses (prueba de inclinación) | Mareo, presíncope, taquicardia al ponerse de pie | FC y PA posturales, VFC | Índice de riesgo antes de la reentrada; alerta por caída ortostática > 20/10 mmHg |
| **Arritmias y desacondicionamiento** | G/E/R | Atrofia del miocardio, cambios electrolíticos, estrés | Extrasístoles y arritmias ocasionales | Palpitaciones; hallazgos en ECG | ECG continuo, FC, QTc | Reglas: pausas > 3 s, QTc > 500 ms, FC < 40 o > 120 sostenida |
| **Trombosis venosa yugular** | G | Estasis del flujo en la yugular interna izquierda | 6 de 11 tripulantes con flujo estancado o retrógrado; 1 trombo oclusivo (≈ día 50) | **Asintomática**: solo se detecta por imagen | Resultado de Doppler de cuello, factores de riesgo | Recordatorio de Doppler periódico; registro de hallazgos y alerta si es anormal |
| **Anemia espacial** | G | El cuerpo destruye más glóbulos rojos | +54 % de hemólisis (experimento MARROW); sigue ~30 % elevada al año | Fatiga, menor fuerza al aterrizar | Hb, Hto, reticulocitos, bilirrubina | Módulo de exámenes: rangos de referencia y tendencia |

### 3.2 Musculoesquelético y renal

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Pérdida ósea** | G | Sin carga mecánica, la resorción supera a la formación | 1–1,5 % de densidad mineral ósea por mes en cadera y columna | **Silenciosa**: el riesgo aparece como fracturas | CTX, NTX, P1NP, calcio urinario, vitamina D, carga de ejercicio | Tendencia de marcadores óseos; cumplimiento del ejercicio de resistencia |
| **Atrofia muscular** | G | Los músculos antigravitatorios dejan de soportar peso | −13 % de volumen de pantorrilla en 6 meses | Debilidad, fatiga | Fuerza (dinamómetro), masa corporal, carga y FC de ejercicio | Registro de sesiones de ejercicio; alerta por pérdida de fuerza o de masa > 5 % |
| **Cálculos renales** | G/E | Calcio liberado del hueso, menos orina, cambios de pH y citrato | 17,3 por 1.000 personas-año en el primer año posvuelo | Cólico en el flanco, hematuria | Volumen, pH, calcio, oxalato y citrato urinarios | Exámenes de orina con rangos; recordatorios de hidratación |
| **Dolor de espalda** | G | La columna se alarga (hasta ~5 cm) y se atrofia la musculatura paraespinal | Frecuente en las primeras semanas | Dolor lumbar | Escala de dolor, estatura, analgésicos | Registro de síntomas con escala numérica y su evolución |

### 3.3 Neuro-ocular y sensoriomotor

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **SANS** (síndrome neuro-ocular) | G/E | Desplazamiento de fluidos hacia la cabeza y presión translaminar alterada; el CO₂ lo agrava | ~69–70 % en misiones largas (criterio OCT) | **A menudo sin síntomas**; visión borrosa de cerca | Δ TRT (OCT), agudeza visual, presión intraocular | Registro de exámenes oculares; alerta si Δ TRT ≥ 20 µm |
| **Mareo espacial** | G | Conflicto entre vestíbulo, vista y propiocepción | 60–80 % en los primeros 2–3 días | Náuseas, vómitos, desorientación | Cuestionario de síntomas | Cuestionario diario los primeros días; seguimiento de su resolución |
| **Alteraciones sensoriomotoras** | G | Readaptación vestibular | Crítico al aterrizar | Problemas de equilibrio y marcha | Tiempo de reacción, pruebas de equilibrio | Pruebas funcionales antes de la reentrada |
| **Cambios cerebrales** | G | Aumentan el líquido cefalorraquídeo y los ventrículos | Aumentos de 11–25 % en el volumen ventricular | Significado clínico incierto | Rendimiento cognitivo | Cubierto por el seguimiento cognitivo (ver 3.6) |

### 3.4 Radiación espacial

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Exposición acumulada** | R | Rayos cósmicos galácticos (GCR) | Límite de carrera NASA: **600 mSv**; Marte ≈ 1,01 Sv | Ninguno inmediato | Dosis acumulada personal | Barra de progreso frente al límite; alerta si la proyección supera el 80 % |
| **Evento de partículas solares (SPE)** | R | Protones solares con dosis altas en horas | Referencia de **250** (en la documentación, "por evento"; en NASA-STD-3001 parece ser un límite de 30 días en mGy-Eq: **a verificar**) | Subida súbita de la tasa de dosis | Tasa de dosis en tiempo real | Alerta crítica a toda la tripulación: "ir al refugio" |
| **Efectos a largo plazo** | R | Daño al ADN por iones pesados (HZE) | Cáncer, cataratas, daño cardiovascular | Tardíos | Historial de dosis, examen del cristalino | Historial longitudinal exportable para seguimiento de por vida |

### 3.5 Inmunidad e infecciones

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Reactivación viral** | I/E/R | Estrés, radiación y mal sueño suprimen la inmunidad celular | 61 % de los astronautas de la ISS eliminan herpesvirus | Generalmente asintomática; lesiones cutáneas | ADN viral en saliva y orina, cortisol, temperatura | Registro de resultados; alerta si hay fiebre > 38 °C o lesiones |
| **Piel y alergias** | I/E | Inmunidad innata sobreactivada, ambiente seco | Erupciones y dermatitis frecuentes | Erupciones, prurito | Síntomas, fotografías | Registro de síntomas con adjunto de imagen |
| **Microbiota intestinal** | E | Microgravedad y entorno cerrado | Cambios en la proporción Bacteroidetes/Firmicutes | Digestivos | (Solo investigación) | Fuera del alcance del MVP |

### 3.6 Salud mental, cognición y conducta

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Estrés, ansiedad y depresión** | I/D | Aislamiento, confinamiento, monotonía, distancia de la Tierra | Raros en vuelo, pero muy probables en misiones a Marte; en Mars500, uno de seis presentó síntomas depresivos | Irritabilidad, ánimo bajo | Escalas de ánimo y estrés, VFC, sueño | Seguimiento emocional periódico; tendencia y alerta por deterioro sostenido |
| **Deterioro cognitivo y fatiga** | I/G | Falta de sueño, carga de trabajo, cambios cerebrales | Peor atención asociada a la falta de sueño (*Sleep*, 2022) | Lentitud, errores | Tiempo de reacción (PVT), aciertos en pruebas aritméticas y de memoria | Tests cognitivos periódicos; **detección de deterioro frente a la línea base personal** |
| **Conflictos en la tripulación** | I | Hacinamiento, convivencia prolongada | Riesgo creciente con la duración | Tensión interpersonal | Escala de cohesión del grupo | Cuestionario confidencial; visible solo para el personal médico |

### 3.7 Sueño y ritmos circadianos

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Privación de sueño** | I/E | 16 amaneceres al día, ruido, luz, estrés | Promedio de ~6 h, frente a 8,5 h programadas | Somnolencia, peor rendimiento | Duración y eficiencia del sueño (actigrafía), medicación | Alerta si duerme < 6 h varias noches o la eficiencia es < 85 % |
| **Desajuste circadiano** | I | Falta de ciclo natural de luz y oscuridad | Uso de hipnóticos en ~75 % de la tripulación de la ISS | Insomnio | Horarios de sueño, luz ambiental, temperatura central | Gráfica de regularidad de horarios; correlación con los tests cognitivos |

### 3.8 Nutrición, metabolismo e hidratación

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Déficit calórico y pérdida de masa** | G/E | Ingesta insuficiente, menor sensación de sed | Pérdida de peso y de masa magra | Pérdida de masa corporal | Masa corporal, ingesta, balance hídrico, densidad urinaria | Tendencia semanal de masa; alerta por pérdida > 5 % |
| **Déficit de vitaminas** | E | Se degradan durante el almacenamiento | Vitaminas D, B12 y folato | Fatiga | Vitaminas D, B12, folato; electrolitos | Exámenes de laboratorio con rangos de referencia |

### 3.9 Ambiente del hábitat

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Exposición a CO₂** | E | Sin convección, el CO₂ se acumula localmente | ISS entre 1 y 9 mmHg; límite operativo 3,0 mmHg; objetivo < 2,5 (promedio de 7 días) | Cefalea, fatiga, errores en procedimientos | pCO₂ por módulo | Aviso si supera 3 mmHg sostenido, aunque nadie tenga síntomas; **correlación CO₂–cefalea** si además hay cefaleas |
| **Contaminantes de aire y agua** | E | Amoníaco, formaldehído, partículas, agua reciclada | Límites NASA (SMAC/SWEG) | Síntomas respiratorios | VOC, partículas, calidad del agua | Alerta al superar los límites |
| **Ruido** | E | Ventiladores y bombas continuos | Pérdida auditiva acumulativa | — | Dosis acústica | Registro acumulado; recordatorio de audiometría |
| **Estrés térmico** | E | El sudor se evapora peor sin convección | Temperatura central más alta en ejercicio | Calor, fatiga | Temperatura central y cutánea | Alerta si la temperatura central es > 38 °C |

### 3.10 EVA, emergencias y readaptación

| Afección | Origen | Causa | Magnitud documentada | Indicadores | Datos a monitorear | Abordaje del software |
|---|:---:|---|---|---|---|---|
| **Lesiones en EVA** | E/G | Guantes presurizados; riesgo de descompresión | Abrasiones y onicólisis | Lesiones en manos y uñas | FC, O₂, CO₂ del traje; revisión de manos | Lista de verificación después de cada EVA |
| **Emergencias médicas** | D | Imprevisibles; sin evacuación en el espacio profundo | Retardo de ~3–22 min por sentido en Marte | Variables | Todos los anteriores | Casos clínicos con protocolos guiados (lista de verificación) |
| **Readaptación al aterrizar** | G | Huesos debilitados, ortostatismo, ataxia | Riesgo de caídas y fracturas | Mareo, inestabilidad | Pruebas ortostáticas y funcionales | Informe de preparación para la reentrada |

---

## 4. Agrupación en áreas funcionales

Las afecciones se agrupan en **seis áreas clínicas**, que se convierten en módulos del sistema (ver [02 · Módulos](02-modulos-del-sistema.md)).

| Área funcional | Afecciones que cubre | Tipo de dato dominante | Módulo propuesto |
|---|---|---|---|
| **Cardiovascular y signos vitales** | Fluidos, ortostatismo, arritmias, trombosis, estrés térmico | Series de tiempo continuas | `Signos vitales` |
| **Musculoesquelético y metabólico** | Hueso, músculo, espalda, nutrición, masa corporal | Registros periódicos (diarios o semanales) | `Físico y metabólico` |
| **Psicología, cognición y sueño** | Estrés, ánimo, deterioro cognitivo, sueño, conflictos | Cuestionarios, tests y actigrafía | `Psicología y cognición` y `Sueño` |
| **Exámenes clínicos** | Anemia, marcadores óseos, orina, vitaminas, virus, SANS | Resultados de laboratorio e imagen cargados | `Exámenes médicos` |
| **Radiación** | Exposición acumulada, SPE | Dosis acumulada y tasa | `Radiación` |
| **Ambiente del hábitat** | CO₂, contaminantes, ruido | Series de tiempo ambientales | `Ambiente` |

Hay además **capacidades transversales** que no son un área clínica pero que todas necesitan:

- **Línea base personal y tendencias:** compara a cada astronauta consigo mismo.
- **Alertas y casos clínicos:** priorizar, confirmar, escalar y registrar.
- **Sincronización con la Tierra:** registrar siempre y enviar cuando haya enlace.
- **Síntomas:** registro libre y estructurado, común a todas las áreas.

---

## 5. Priorización para el producto

Se cruza la **relevancia clínica** (documentación) con la **viabilidad de simularla** (no hay equipos médicos reales). El equipo decidió construir **todas las áreas para el evento**; la columna de orden indica en qué secuencia se desarrollan y, si hubiera retrasos, cuáles se protegen primero.

| Área | Relevancia en Artemis III (órbita baja, ~2 semanas) | Relevancia en el espacio profundo (Luna y Marte) | Facilidad de simulación | Entrega y orden |
|---|:---:|:---:|:---:|:---:|
| Signos vitales y cardiovascular | Alta | Alta | Alta | 🟢 **MVP** · núcleo |
| Psicología y cognición | Media | Muy alta | Alta | 🟢 **MVP** · núcleo |
| Sueño | Alta | Alta | Alta | 🟢 **MVP** · núcleo |
| Exámenes médicos | Media | Alta | Alta (carga manual) | 🟢 **MVP** · núcleo |
| Ambiente (CO₂) | Alta | Alta | Alta | 🟢 **MVP** · núcleo |
| Radiación | Baja (órbita baja) | Muy alta | Alta | 🟢 **MVP** · ampliación |
| Físico y metabólico | Baja (misión corta) | Muy alta | Media | 🟢 **MVP** · ampliación |
| Imagen (OCT, Doppler) | Baja | Alta | Baja | 🟢 **MVP** como resultado estructurado en Exámenes; el análisis de la imagen es evolución |
| Microbiota, ómica | Investigación | Investigación | Baja | ⚪ Fuera de alcance |

> **Relación con la Fase 1.** La Fase 1 recomienda priorizar cuatro bloques: monitoreo cardiovascular continuo, dosimetría, sueño/conducta/cognición y CO₂. El MVP **cubre los cuatro**, y además incluye exámenes médicos (uno de los dos casos de uso centrales del proyecto, junto con los tests cognitivos), físico y metabólico, y síntomas.
>
> - **Núcleo:** áreas que sostienen los escenarios protagonistas de la demo (E3, E4 y E5). Se desarrollan primero.
> - **Ampliación:** radiación y físico-metabólico. Entran al MVP, pero se construyen después del núcleo; si el calendario se aprieta, son las primeras en simplificarse (ver el [orden de recorte](05-estructura-scrum.md#6-plan-de-entregas-y-sprints)).

---

## 6. Conclusiones para el diseño

1. **Línea base individual, no solo umbrales poblacionales.** En el espacio muchos parámetros cambian "normalmente"; lo importante es la desviación de cada astronauta respecto a sí mismo.
2. **Lo silencioso exige registro activo.** La pérdida ósea, el SANS y la trombosis no dan síntomas: el sistema debe **recordar** los exámenes periódicos, no solo reaccionar a lo que se reporta.
3. **Tendencias de semanas y meses.** El riesgo se acumula (hueso, radiación, sueño, cognición). Hacen falta series históricas y gráficas de evolución.
4. **Fusión de datos.** Las afecciones interactúan: el CO₂ agrava el SANS y las cefaleas, y el mal sueño empeora la cognición. El análisis debe poder cruzar módulos.
5. **Autonomía.** Sin enlace con la Tierra, el sistema debe detectar, alertar y guiar a bordo, y sincronizar después.
6. **El humano decide.** El software sugiere y prioriza; el oficial médico (CMO) o el médico de vuelo decide.

---

[← Inicio](../../README.md) · [Siguiente: Módulos del sistema →](02-modulos-del-sistema.md)
