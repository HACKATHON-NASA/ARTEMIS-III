# Monitorización de la salud de astronautas (Fase 1)

## Problemas médicos, parámetros a vigilar y cómo viajan los datos a la Tierra

> [← Volver al inicio](../../README.md)
>
> **Documentos relacionados:** [Fase 2 – Arquitectura de largo plazo](03-arquitectura-vision-fase2.md) · [Proyecto Space Apps](01-proyecto-space-apps.md) · [Enfermedades de los astronautas (resumen)](04-enfermedades-resumen.md) · [Informe de fisiopatología espacial](05-informe-fisiopatologia-hrp.md) · [Glosario](../glosario.md)

## Índice

- [Resumen](#resumen)
1. [Marco de referencia: las 5 amenazas RIDGE](#1-marco-de-referencia-las-5-amenazas-ridge)
2. [Problemas de salud del astronauta](#2-problemas-de-salud-del-astronauta)
3. [Qué monitorear: parámetros, rangos y alertas](#3-qué-monitorear-parámetros-rangos-y-alertas)
4. [Sensores y dispositivos](#4-sensores-y-dispositivos)
5. [Cómo llega la información a la Tierra](#5-cómo-llega-la-información-a-la-tierra)
6. [Panorama tecnológico para la fase de software](#6-panorama-tecnológico-para-la-fase-de-software)
7. [Tabla maestra: problema → parámetro → sensor](#7-tabla-maestra-problema--parámetro--sensor)
8. [Recomendaciones para el proyecto](#8-recomendaciones-para-el-proyecto)
9. [Advertencias y limitaciones](#9-advertencias-y-limitaciones)
10. [Referencias](#10-referencias)

---

## Resumen

Un software de monitorización de la salud para astronautas debe vigilar de forma continua, como mínimo:

- la función cardiovascular,
- el sistema musculoesquelético,
- los ojos y el cerebro,
- la exposición a radiación,
- el sistema inmune,
- el sueño y la conducta,
- el ambiente (CO₂).

Además, debe **funcionar de forma autónoma**. En misiones a Marte, el retardo de ~3 a 22 minutos por sentido y los apagones de comunicación impiden depender de la telemedicina en tiempo real, como se hace hoy en la ISS.

### Puntos clave (TL;DR)

**Los riesgos están bien cuantificados y son medibles:**

| Riesgo | Magnitud |
|---|---|
| Pérdida ósea | ~1–1,5 % al mes en la cadera |
| Atrofia muscular | 13 % en la pantorrilla en 6 meses |
| SANS | ~69 % de las tripulaciones de larga duración (criterio OCT) |
| Intolerancia ortostática | > 60 % al aterrizar tras ~6 meses |
| Anemia espacial | 54 % más destrucción de glóbulos rojos |
| Reactivación viral | ~61 % de los astronautas de la ISS |
| Sueño | ~6 h por noche |
| Radiación en una misión a Marte | ~1 Sv estimado, frente a un límite de carrera NASA de 600 mSv |

**Cómo se capturan los datos hoy (modelo híbrido):**

- Wearables, como el Bio-Monitor/Astroskin de la Agencia Espacial Canadiense (CSA).
- Equipos médicos del CHeCS: PA/ECG, ultrasonido, desfibrilador y camilla CMRS.
- Muestras biológicas, que en gran parte se analizan en la Tierra.

**Cómo llegan a la Tierra:**

- Por la red de la estación, a través de los satélites TDRS (hasta 600 Mbps).
- Los revisan médicos de vuelo e ingenieros biomédicos (BME) en Houston.
- En las misiones largas, los médicos de vuelo hacen conferencias médicas privadas semanales con cada tripulante. Estos datos están protegidos por la Privacy Act y la HIPAA.

**Para la Luna y Marte el diseño cambia:**

- procesamiento a bordo,
- almacenamiento y reenvío con DTN/Bundle Protocol (ya es servicio operativo de la NASA),
- priorización de alertas y cifrado,
- apoyo al oficial médico con IA; por ejemplo, el prototipo CMO-DA de NASA y Google logró entre 74 % y 88 % de precisión en escenarios simulados.

> El control de misión pasa de ser un **"monitor en vivo"** a un **consultor diferido**.

---

## 1. Marco de referencia: las 5 amenazas RIDGE

La NASA agrupa los riesgos de los vuelos a la Luna y Marte en cinco amenazas, resumidas en el acrónimo **RIDGE**: Radiación espacial, *Isolation* (aislamiento y confinamiento), Distancia a la Tierra, *Gravity fields* (gravedad alterada) y Entornos hostiles o cerrados.

El **Human Research Program (HRP)**, creado en octubre de 2005 en el Johnson Space Center, organiza su investigación en cinco elementos:

1. ISS Medical Projects
2. Space Radiation
3. Human Health Countermeasures
4. Exploration Medical Capability
5. Human Factors and Behavioral Performance

El estándar **NASA-STD-3001** traduce esos riesgos en requisitos de diseño.

| Amenaza | Qué es | Efectos principales | Implicación para el software |
|---|---|---|---|
| **R** – Radiación | Rayos cósmicos galácticos (GCR), eventos de partículas solares (SPE), cinturones de radiación | Cáncer, cataratas, daño al SNC, enfermedad cardiovascular, síndrome agudo | Dosimetría personal, alertas de SPE, dosis acumulada frente a límites |
| **I** – Aislamiento | Tripulación pequeña, espacio reducido, monotonía | Estrés, depresión, conflictos, fatiga, mal sueño | Cuestionarios, actigrafía, pruebas cognitivas |
| **D** – Distancia | Retardo, sin evacuación, logística limitada | Emergencias sin apoyo inmediato | Autonomía médica, soporte de decisiones, DTN |
| **G** – Gravedad | Microgravedad; 1/6 g en la Luna; ~3/8 g en Marte; transiciones | Hueso, músculo, fluidos, SANS, sistema vestibular, ortostatismo | Tendencias longitudinales |
| **E** – Entornos hostiles | Atmósfera artificial, CO₂, contaminantes, microbios, ruido | Cefaleas, toxicidad, infecciones, pérdida auditiva | Fusionar datos ambientales y fisiológicos |

> **Interpretación:** las cinco amenazas actúan a la vez y se potencian. No basta con "un monitor cardiaco": hay que fusionar datos fisiológicos, ambientales, de radiación y conductuales, y analizar tendencias de semanas y meses.

---

## 2. Problemas de salud del astronauta

### 2.1 Sistema musculoesquelético

#### a) Pérdida de densidad ósea

- **Causa:** sin carga gravitatoria, la resorción ósea (osteoclastos) aumenta rápido y la formación responde con lentitud.
- **Síntomas:** es silenciosa. El riesgo se manifiesta como fracturas al volver a la gravedad o en la superficie lunar o marciana. Es un problema de larga duración, crítico para Marte.
- **Magnitud:**
  - Cosmonautas de la Mir (4–12 meses): pérdida de 1 %/mes en la columna y 1,5 %/mes en la cadera (DXA).
  - 14 tripulantes de la ISS (4–6 meses), medidos con QCT: 0,9 %/mes en la columna, 1,4–1,5 %/mes en la cadera y 2,2–2,7 %/mes en el hueso trabecular de la cadera (Lang *et al.*, 2004).
  - La resistencia de la cadera cayó en promedio un 14 % en 13 astronautas; tres perdieron entre 20 % y 30 %, comparable a mujeres mayores con osteoporosis (Keyak *et al.*, 2009).
  - Un metaanálisis (*npj Microgravity*, 2020) halló que los marcadores de resorción suben un 113 % en unos 11 días, mientras que los de formación aumentan ~6 % al mes.
  - A un año del regreso, solo el 34,0 % había recuperado la densidad de la cadera y el 46,8 % la de la columna (cohorte de 94 astronautas).
- **Detección:** DXA/QCT antes y después del vuelo (en Tierra); marcadores en sangre y orina (muestras congeladas).
- **Contramedidas:** ejercicio de resistencia (ARED), cinta (T2) y cicloergómetro (CEVIS); calcio, vitamina D y energía suficiente; bisfosfonatos (en estudio).
- **Monitorear:** CTX, NTX, calcio urinario, fosfatasa alcalina ósea, P1NP, vitamina D, carga de ejercicio.

#### b) Atrofia muscular

- **Causa:** los músculos antigravitatorios dejan de soportar peso y baja la síntesis de proteínas.
- **Síntomas:** debilidad, fatiga, menor capacidad para actividades extravehiculares (EVA) y riesgo de caídas al regresar. Comienza en días.
- **Magnitud:**
  - 9 tripulantes de la ISS (6 meses): el volumen de la pantorrilla bajó 13 ± 2 %, con más atrofia del sóleo (−15 %) que del gastrocnemio (−10 %); dos perdieron más del 20 % (Trappe *et al.*, 2009).
  - En la Mir, la pérdida fue del 17 %; en reposo en cama sin contramedidas, ~29 %.
  - Las revisiones reportan −12 % en cuádriceps, −20 % en los músculos posteriores de la espalda y un 25–30 % menos de fuerza de contracción en la pantorrilla.
  - La capacidad de trabajo físico baja más del 40 %, y quienes partían con más músculo perdían más (Fitts *et al.*, 2010).
- **Monitorear:** carga de ejercicio, FC en ejercicio, fuerza (dinamómetro), masa corporal; resonancia magnética (MRI) en Tierra.

#### c) Cálculos renales

- **Causa:** calcio liberado del hueso, menor volumen urinario y cambios de pH y citrato.
- **Síntomas:** cólico en el flanco y hematuria. En vuelo podría obligar a abortar la misión.
- **Magnitud:**
  - Un análisis en *npj Microgravity* (2022) contabilizó 14 eventos sintomáticos en 5.434,5 personas-año hasta 2008: 7 antes del vuelo, 7 después y 0 durante el vuelo. La incidencia modelada en el primer año posvuelo fue de 17,3 por 1.000 personas-año.
  - Según la NASA (OCHMO, 2025, con datos del LSAH de 2022), hubo 43 eventos de litiasis en 29 tripulantes, una incidencia "a la par" con la de los pilotos comerciales.
- **Contramedidas:** hidratación, control del sodio, citrato de potasio (estudiado).
- **Monitorear:** volumen, densidad y química urinaria (calcio, oxalato, citrato); ecografía renal.

#### d) Dolor de espalda

- **Causa:** los discos intervertebrales se hidratan y la columna se alarga, a lo que se suma la atrofia de los músculos paraespinales.
- **Síntomas:** dolor lumbar en las primeras semanas y mayor riesgo de hernia discal al regresar.
- **Monitorear:** escala de dolor, estatura, uso de analgésicos.

### 2.2 Sistema cardiovascular y hematológico

#### a) Redistribución de fluidos

Los fluidos se desplazan hacia el tórax y la cabeza ("cara hinchada, piernas de pájaro"), con congestión nasal, cefalea y mayor presión venosa en el cuello y la cabeza. Es un factor clave del SANS y de la estasis yugular. El cuerpo responde perdiendo volumen plasmático.

#### b) Intolerancia ortostática

- **Qué es:** incapacidad de mantener la presión arterial al ponerse de pie al volver a la gravedad, por menor volumen plasmático y alteración del barorreflejo.
- **Síntomas:** mareo, presíncope o síncope, taquicardia.
- **Magnitud:**
  - Tras vuelos cortos del transbordador: ~20–30 %.
  - Tras unos 6 meses en la ISS, más del 60 % no pudo completar una prueba de inclinación de 10 minutos a 80° el día del aterrizaje (*Frontiers in Physiology*, 2020).
  - En la Mir, 5 de 6 astronautas tuvieron presíncope tras 129–190 días (Meck *et al.*, 2001).
  - Un metaanálisis (*Acta Astronautica*, 2026) con 221 astronautas estimó una incidencia del 30 % (IC 95 %: 24–38 %) sin asociación con la duración. La discrepancia con los estudios anteriores probablemente se debe a diferencias de protocolo y de contramedidas.
- **Contramedidas:** carga de líquidos y sal, prendas de compresión, ejercicio.
- **Monitorear:** FC y PA posturales, VFC, sensibilidad barorrefleja, volumen sistólico.

#### c) Arritmias y desacondicionamiento

Atrofia del miocardio, cambios electrolíticos y estrés. Se han documentado extrasístoles y arritmias ocasionales.

- **Monitorear:** ECG continuo/Holter, intervalo QT, ectopias, VO₂ estimado.

#### d) Trombosis venosa yugular

- **Hallazgo:** en 11 tripulantes de la ISS, con ecografías hacia los días 50 y 150, 6 tenían flujo estancado o retrógrado en la vena yugular interna izquierda; 1 tenía un trombo oclusivo y otro un posible trombo parcial (Marshall-Goebel *et al.*, 2019). La presión negativa en la parte inferior del cuerpo (LBNP) mejoró el flujo.
- **El caso clínico** (*NEJM*, 2020):
  - El trombo se sospechó en una ecografía de investigación hecha a una astronauta asintomática.
  - Ella misma hizo el seguimiento, guiada en tiempo real; dos radiólogos lo interpretaron desde la Tierra.
  - Se trató con enoxaparina que, según una revisión en *ATVB*, estaba a bordo "de forma fortuita" para otro uso.
- **Lección:** hay patologías silenciosas que solo detecta la imagen, y el ultrasonido guiado a distancia es crítico.
- **Monitorear:** Doppler de cuello periódico, signos (vena yugular externa prominente), anticoncepción hormonal como factor de riesgo.

#### e) Anemia espacial

- En el experimento **MARROW** de la CSA (14 astronautas, 6 meses), el cuerpo destruyó un **54 % más de glóbulos rojos**: 3 millones por segundo frente a 2 millones. El efecto fue igual en hombres y mujeres, y se midió por el CO espirado.
- Los niveles de glóbulos rojos se normalizaron a los 3–4 meses, pero un año después la destrucción seguía ~30 % elevada (Trudel *et al.*, 2022).
- La anemia reduce la energía y la fuerza al aterrizar.
- **Monitorear:** hemoglobina, hematocrito, reticulocitos, bilirrubina, CO espirado.

### 2.3 Ojos: síndrome neuro-ocular (SANS)

- **Qué es:** edema del disco óptico, aplanamiento del globo, pliegues coroideos, distensión de la vaina del nervio óptico, hipermetropía y manchas algodonosas (descrito por Mader *et al.*, 2011).
- **Causa:** desplazamiento de fluidos hacia la cabeza y presión translaminar alterada, aunque no se explica solo por la presión intracraneal. Hay hipótesis adicionales: congestión venosa, anemia, genética (folato/MTHFR), CO₂ y falta de sueño.
- **Síntomas:** a menudo ninguno. Los afectados no tuvieron cefaleas severas crónicas ni diplopía, así que los síntomas **no sirven** para detectarlo.
- **Magnitud:**
  - En 36 tripulantes (media de 5,3 meses), el 17 % tuvo signos clínicos, cifra que subió al **69 %** con criterios de OCT (Δ TRT ≥ 20 µm) (Sibony *et al.*, 2023).
  - Un estudio de 2025 con 43 tripulantes de la ISS (vuelo medio de 198 días) indica edema de disco en ~60 % de los astronautas en misiones largas.
  - Según una nota técnica de la NASA (OCHMO), el 70 % de los tripulantes presenta uno o más signos de SANS y el 18 % un SANS clínicamente preocupante.
  - Puede repetirse en misiones sucesivas.
- **Detección:** OCT a bordo, fundoscopia, tonometría, ultrasonido ocular, agudeza visual; MRI en Tierra.
- **Contramedidas en estudio:** LBNP, manguitos de muslo, vitaminas B, control del CO₂.

### 2.4 Sistema nervioso, vestibular y cerebro

#### a) Mareo espacial

- **Causa:** conflicto entre el vestíbulo, la vista y la propiocepción.
- **Síntomas:** malestar, palidez o rubor, sudor frío, vómitos súbitos, anorexia.
- **Magnitud:** según el *Integrated Medical Model* de la NASA, lo sufre el 60–80 % de los viajeros espaciales en los primeros 2–3 días. En el programa estadounidense de 1962 a 1998 fueron 325 de 445 (73 %). La proporción es similar al regresar.
- **Contramedidas:** prometazina, escopolamina, restricción de movimientos de la cabeza.

#### b) Desorientación y alteraciones sensoriomotoras

Ilusiones de orientación, dificultad para controlar vehículos y problemas de equilibrio y marcha al aterrizar, lo que es crítico en Marte.

- **Monitorear:** equilibrio, marcha, tiempo de reacción, unidad de medición inercial (IMU).

#### c) Cambios cerebrales

- El cerebro se desplaza hacia arriba y aumentan el líquido cefalorraquídeo y el tamaño de los ventrículos.
- Estudios previos reportaron aumentos de entre 11 % y 25 %.
- En 30 astronautas (McGregor, Hupfeld *et al.*, 2023):
  - la mayor expansión ocurre en los primeros 6 meses;
  - el agrandamiento persiste, con recuperación solo parcial a los 6–12 meses;
  - con menos de 3 años entre misiones, los ventrículos no recuperan su capacidad compensatoria.
- Su significado clínico aún es incierto.
- **Monitorear:** pruebas cognitivas; en el futuro, imagen portátil.

### 2.5 Radiación espacial

- **Fuentes:**
  - **GCR:** núcleos pesados, muy penetrantes.
  - **SPE:** protones impredecibles, con dosis altas en horas.
  - **Partículas atrapadas:** cinturones de Van Allen y Anomalía del Atlántico Sur.
- **Magnitud:**
  - El detector RAD del rover Curiosity midió **1,8 mSv/día** en el viaje de crucero.
  - Ida y vuelta a Marte: ~0,66 Sv. Sumando 500 días en superficie (0,67 mSv/día): **~1,01 Sv**, unas 10 veces la dosis de 6 meses en la ISS (Zeitlin *et al.*, 2013; Hassler *et al.*, 2014).
  - Según Zeitlin, "incluso un casco de aluminio de un pie de espesor no cambiaría mucho la dosis" de GCR.
- **Límites (NASA-STD-3001, vol. 1, rev. B, 2022):**
  - **600 mSv** de dosis efectiva de carrera, igual para todas las edades y sexos.
  - **250 mSv** por evento solar.
  - Según las Academias Nacionales y la NASA, una misión a Marte superaría ese límite.
- **Efectos:** cáncer (a largo plazo), cataratas, daño al SNC (sobre todo en modelos animales), enfermedad cardiovascular (evidencia experimental) y síndrome de radiación aguda ante un SPE sin refugio.
- **Detección:**
  - Dosímetros pasivos personales (TLD/OSL).
  - Dosímetros activos (DOSTEL, RAD, Timepix).
  - Alertas de clima espacial.
  - En Artemis I se probaron maniquíes (MARE) y el chaleco AstroRad.
- **Contramedidas:** blindaje, refugio durante un SPE; contramedidas biológicas en estudio (metformina, γ-tocotrienol, avasopasem, CDDO-Me).
- **Monitorear:** dosis acumulada personal, tasa de dosis en tiempo real, alertas de SPE, linfocitos, daño al ADN (en investigación), cristalino.

### 2.6 Sistema inmune y microbiota

- **Qué es:** desregulación inmune persistente y reactivación de herpesvirus latentes, causadas por el estrés (cortisol, adrenalina), la radiación, el mal sueño y el confinamiento.
- **Magnitud** (Rooney *et al.*, 2019):
  - La excreción viral (*shedding*) aumentó del transbordador a la ISS: VZV del 41 % al 65 %, EBV del 82 % al 96 % y CMV del 47 % al 61 %.
  - Eliminaron herpesvirus en saliva u orina 47 de 89 astronautas (53 %) en vuelos cortos y 14 de 23 (61 %) en la ISS.
  - En la ISS se detectaron VZV y CMV hasta 30 días después del vuelo. Solo 6 astronautas tuvieron síntomas (lesiones cutáneas menores).
  - Se ha detectado VZV infeccioso vivo en saliva.
- **Riesgos:** herpes zóster, dermatitis (descrita en asociación con el HSV-1), contagio en un hábitat cerrado.
- **Microbiota:** el Twins Study observó cambios en el microbioma intestinal que volvieron a niveles cercanos a los previos.
- **Monitorear:** ADN viral en saliva (EBV, VZV, HSV-1) y orina (CMV), linfocitos, citoquinas, cortisol salival, temperatura, lesiones cutáneas.

### 2.7 Salud mental y comportamental

- **Riesgos:**
  - estrés, ansiedad y depresión;
  - conflictos interpersonales y con el control de misión;
  - monotonía, fatiga y deterioro cognitivo.
  - En Marte se suman el retardo, el efecto de "Tierra fuera de vista" y la autonomía forzada.
- **Evidencia:**
  - En la ISS, la falta de sueño se asoció a peor atención y más estrés en misiones de 6 meses (*Sleep*, 2022).
  - Los análogos (HERA, Mars-500) muestran hipocinesia y cambios del sueño.
- **Contramedidas:** selección, conferencias psicológicas privadas, contacto familiar, horarios estables. La NASA promueve el acrónimo CONNECT.
- **Monitorear:** escalas validadas, PVT y batería *Cognition*, actigrafía, VFC, análisis de voz (en investigación).

### 2.8 Sueño y ritmos circadianos

- **Causa:** 16 amaneceres al día en la ISS, ruido, luz artificial, temperatura y estrés.
- **Magnitud** (Barger *et al.*, 2014; 64 astronautas en 80 misiones del transbordador y 21 en la ISS, más de 4.200 noches en el espacio):
  - Aunque se programaban 8,5 h, el promedio de sueño fue de 5,96 h en el transbordador y 6,09 h en la ISS.
  - Tres cuartas partes de los tripulantes de la ISS usaron medicación para dormir.
  - El 78 % de los tripulantes del transbordador la usó, en el 52 % de las noches, con solo un 1,3 % de mejora en la eficiencia del sueño.
  - En *Sleep* (2022), los astronautas durmieron ~6,5 h y solo alcanzaron las 8,5 h en el 5,9 % de las noches.
- **Contramedidas:** iluminación LED ajustable, horarios regulares, hipnóticos con cautela.
- **Monitorear:** actigrafía, luz ambiental, temperatura central, FC/VFC nocturna, medicación, PVT.

### 2.9 Nutrición, metabolismo e hidratación

- **Problemas:**
  - ingesta calórica insuficiente;
  - pérdida de peso y de masa magra;
  - vitaminas que se degradan durante el almacenamiento;
  - exceso de sodio y hierro;
  - menor sensación de sed.
- En el Twins Study, la reducción de masa corporal se correlacionó con cambios en los telómeros y con más folato sérico.
- **Monitorear:** masa corporal (medidor de masa inercial), ingesta, balance hídrico, densidad urinaria, glucosa (monitor continuo, CGM, en investigación), vitaminas D, B12 y folato, electrolitos.

### 2.10 Otros sistemas

| Sistema | Problema / causa | Evidencia | Qué monitorear |
|---|---|---|---|
| Piel, cabello y uñas | Sequedad, prurito, cicatrización lenta, dermatitis; daño de uñas por los guantes | Reportes consistentes; poca cuantificación | Fotografías, temperatura cutánea |
| Audición | Ruido continuo de ventiladores y bombas | Límites NASA de ruido; tapones | Audiometría, dosimetría acústica |
| Dental | Caries, fracturas, pérdida de empastes | Incluido en los modelos de riesgo médico | Inspección, cámara intraoral |
| Urinario | Litiasis, retención urinaria inicial, infecciones | Ver 2.1 c) | Volumen y química urinaria |
| Reproductivo | Radiación en las gónadas, hormonas; anticoncepción y trombosis | Evidencia humana escasa | Hormonas, historial |
| Genética y epigenética | Cambios de expresión génica, metilación, inestabilidad genómica | Twins Study (340 días): el 91,3 % de la expresión génica volvió a su nivel basal en 6 meses; ~7 % de los genes (reparación del ADN, inmunidad) seguía alterado | Multiómica (investigación) |
| Telómeros | Alargamiento en vuelo y más telómeros cortos al volver | Garrett-Bakelman *et al.*, 2019 | Longitud telomérica (investigación) |

### 2.11 Riesgos ambientales, EVA y readaptación

#### a) CO₂ elevado

- En la ISS, el CO₂ ha variado entre 1 y 9 mmHg (en la Tierra es < 0,5).
- Por cada 1 mmHg más, las probabilidades de cefalea se duplican. Para mantener ese riesgo por debajo del 1 %, el promedio de 7 días debería ser < 2,5 mmHg (Law *et al.*, 2014).
- La NASA reporta:

  | pCO₂ | Efecto |
  |---|---|
  | 2,3–2,7 mmHg | Fatiga y sensación de cabeza llena |
  | 2,7–3,0 mmHg | Pasos omitidos en procedimientos |
  | > 3,0–3,4 mmHg | Cefaleas |

- De ahí el **límite operativo de 3,0 mmHg**. La evidencia sugiere que entre 0,5 y 2,0 mmHg se preservarían la salud y el desempeño.
- En microgravedad no hay convección natural y el CO₂ se acumula localmente.
- **Monitorear:** pCO₂ por módulo y personal, cefalea, fatiga, cognición.

#### b) Toxicidad, aire y agua

Amoníaco, formaldehído, partículas que no se depositan, polvo lunar o marciano (abrasivo, con riesgo pulmonar y ocular) y agua reciclada.

- **Monitorear:** compuestos orgánicos volátiles (VOC), partículas, calidad del agua, síntomas respiratorios, SpO₂.

#### c) Estrés térmico

Sin convección, el sudor se evapora con menos eficiencia y la temperatura central sube más durante el ejercicio.

- **Monitorear:** temperatura central y cutánea, FC.

#### d) EVA y lesiones

Los guantes presurizados causan abrasiones y onicólisis. Hay riesgo de enfermedad descompresiva (que se previene con prerrespiración de oxígeno) y de traumatismos.

- **Monitorear en EVA:** FC, consumo de O₂, CO₂ del traje, temperatura, estado de las manos después de la EVA.

#### e) Aterrizaje y readaptación

Las fuerzas de reentrada actúan sobre huesos debilitados, y el ortostatismo y la ataxia pueden provocar caídas y fracturas. En Marte no habrá un equipo médico esperando.

- **Monitorear:** pruebas funcionales y ortostáticas antes de la reentrada; PA y FC después del aterrizaje.

### 2.12 Emergencias médicas y atención a distancia

| Escenario | Retardo | Evacuación | Estrategia |
|---|---|---|---|
| **ISS** | Casi en tiempo real | Horas a días | Estabilizar y evacuar. Telemedicina casi en tiempo real; un *Crew Medical Officer* (CMO) con entrenamiento limitado, no necesariamente médico. |
| **Luna** | ~1,3 s por sentido | Días | Telemedicina con latencia. |
| **Marte** | ~3–22 min por sentido, con apagones de unas dos semanas en cada conjunción solar (aprox. cada 26 meses) | Imposible durante meses | Autonomía médica con recursos limitados (fármacos que caducan, sin quirófano completo), modelos de riesgo (*Integrated Medical Model*) y soporte de decisiones a bordo. |

La NASA/JPL describió la conjunción del 11 al 25 de noviembre de 2023 como "una pausa de comunicaciones de dos semanas".

> **Ejemplo:** el caso de la trombosis yugular se resolvió con apoyo continuo desde la Tierra. En Marte habría que decidir sin respuesta inmediata y solo con lo previsto a bordo.

### 2.13 Sexo, edad y efectos posmisión

**Sexo:**

- Desde 2022, el límite de radiación es igual para todos (600 mSv). Se basa en una mujer de 35 años con un 3 % de riesgo de muerte por cáncer.
- Según la NASA, el SANS es más frecuente en hombres.
- La intolerancia ortostática se ha reportado más en mujeres.
- En reposo en cama, las mujeres tuvieron más atrofia de piernas en los primeros meses (Trappe *et al.*, 2023).
- La hemólisis fue similar en ambos sexos.

**Edad:**

- Los tripulantes mayores reportaron menos cefaleas por CO₂.
- La recuperación ósea es más lenta tras misiones de más de 6 meses.

**Efectos posmisión:**

- densidad ósea no recuperada del todo al año,
- hemólisis elevada al año,
- ventrículos cerebrales agrandados,
- telómeros cortos,
- genes inmunitarios alterados.
- En 379 astronautas de EE. UU. hubo solo 6 fracturas de cadera: dos tras misiones largas y a edades más jóvenes de lo habitual.

La NASA sigue a los astronautas de por vida con el programa **LSAH**.

---

## 3. Qué monitorear: parámetros, rangos y alertas

> **Importante:** los rangos son valores clínicos terrestres de referencia. En el espacio muchos parámetros cambian "normalmente", por lo que el software debe usar **líneas base individuales** y detectar desviaciones y tendencias. Los umbrales definitivos deben fijarlos médicos de vuelo.

### 3.1 Signos vitales y parámetros continuos

| Parámetro | Referencia (adulto en reposo) | Alerta sugerida | Sensor | Frecuencia |
|---|---|---|---|---|
| FC | 60–100 lpm (atletas 40–60) | < 40 o > 120 sostenida; cambio > 20 % frente a la línea base | ECG, PPG | Continua |
| ECG | Ritmo sinusal; QTc < 450 ms (H) / < 460 ms (M) | Arritmia, pausas > 3 s, QTc > 500 ms | ECG de 3 derivaciones / Holter | Continua |
| VFC | Individual | Caída sostenida (estrés/fatiga) | ECG | Diaria |
| Presión arterial | < 120/80 mmHg | > 140/90 o < 90/60; caída ortostática > 20/10 | Manguito, estimación sin manguito | Diaria |
| SpO₂ | 95–100 % | < 92 % (urgente < 90 %) | Oxímetro | Continua/puntual |
| Frecuencia respiratoria | 12–20 rpm | < 8 o > 25 | Pletismografía | Continua |
| Temperatura central | 36,5–37,5 °C | > 38 °C; < 35 °C | Sensor de flujo de calor | Continua |
| Actividad | Individual | Inactividad inusual | Acelerómetro/IMU | Continua |
| Sueño | 7–9 h | < 6 h varios días; eficiencia < 85 % | Actigrafía | Nocturna |
| Masa corporal | Línea base | Pérdida > 5 % | Medidor de masa inercial | Semanal |
| Glucosa | 70–99 mg/dL en ayunas | < 70 o > 180 | CGM | Opcional |

### 3.2 Ambiente y radiación

| Parámetro | Límite / referencia | Alerta sugerida | Fuente |
|---|---|---|---|
| Dosis de carrera | < 600 mSv | Proyección > 80 % del límite | NASA-STD-3001 |
| Evento solar | < 250 mSv por evento | Subida súbita de la tasa → ir al refugio | NASA-STD-3001 |
| Crucero a Marte | ~1,8 mSv/día | Desviación brusca | MSL/RAD |
| pCO₂ | Límite ISS 3,0 mmHg; objetivo < 2,5 (promedio de 7 días) | > 3 mmHg + síntomas | NASA; Law *et al.* |
| Ruido, aire, agua | Límites NASA (SMAC/SWEG) | Superación | NASA |

### 3.3 Biomarcadores de laboratorio

| Área | Biomarcadores | Cómo se mide hoy |
|---|---|---|
| Hueso | CTX, NTX, calcio urinario, P1NP, PTH, vitamina D | Muestras → Tierra |
| Riñón | Volumen, pH, oxalato, citrato, supersaturación | Muestras → Tierra; tiras reactivas |
| Sangre | Hb, Hto, reticulocitos, ferritina, bilirrubina, CO espirado | Analizador portátil, muestras |
| Metabolismo | Glucosa, lípidos, electrolitos, función hepática y renal | Analizador / Tierra |
| Inmune | Linfocitos, citoquinas; ADN viral en saliva/orina | PCR en Tierra |
| Estrés | Cortisol salival, catecolaminas | Saliva → Tierra |
| Nutrición | Vitaminas B, folato, B12, D | Muestras → Tierra |
| Ómica/radiación | Aberraciones cromosómicas, transcriptoma, telómeros | Investigación |

### 3.4 Ojos, cognición y conducta

| Área | Parámetro | Herramienta |
|---|---|---|
| SANS | Δ TRT ≥ 20 µm, pliegues, agudeza, presión intraocular | OCT, fundoscopio, tonómetro, ultrasonido |
| Cognición | Atención, memoria, tiempo de reacción | PVT, batería *Cognition* |
| Psicológico | Ánimo, estrés, carga de trabajo, cohesión del grupo | Escalas, entrevistas privadas |
| Sensoriomotor | Equilibrio, marcha | Pruebas funcionales, IMU |

---

## 4. Sensores y dispositivos

### 4.1 Wearables y biosensores

#### Bio-Monitor (CSA) / Astroskin (Carré Technologies – Hexoskin)

- **Qué es:** camiseta inteligente, cinta para la cabeza, unidad de datos con batería y aplicación para tableta. Opera en la ISS desde enero de 2019 (lo puso en servicio David Saint-Jacques) y lo han usado astronautas de la CSA, la NASA, la ESA, la JAXA y los Emiratos Árabes Unidos.
- **Qué mide:** ECG de 3 derivaciones, FC, respiración (frecuencia y volumen), presión arterial estimada, SpO₂, temperatura cutánea, actividad y calidad del sueño.
- **Cómo funciona:**
  - Registra de forma continua durante 24–72 h sin interferir con las tareas.
  - Durante el ejercicio, el astronauta ve sus signos en la tableta.
  - Según la CSA, al terminar se desconecta la batería y se coloca en una base que descarga los datos a la Tierra por el sistema de comunicaciones de la estación. Una tarjeta procesadora de Xiphos se encarga del envío.
  - En el análogo HERA de la NASA, el Bluetooth usado para verificar datos resultó fiable.
- **Precisión:**
  - En HERA dio datos de buena calidad en registros de 24 h y durante el ejercicio.
  - El desarrollador indica una precisión de "más o menos" ±10 mmHg en la presión estimada, así que no reemplaza al manguito.
- **Limitaciones:** artefactos de movimiento, contacto de los electrodos, batería y descarga por lotes (no transmite de forma continua en vivo).
- **Financiación:** la CSA otorgó hasta 2,4 millones de dólares canadienses en 18 meses (2016).

#### Otros dispositivos

- **Hexoskin:** versión comercial (ECG, respiración, actividad, Bluetooth). Astroskin también voló en Virgin Galactic.
- **Actiwatch / actigrafía:** acelerómetro y sensor de luz. Fue la base del estudio de Barger *et al.*, pero no distingue bien entre vigilia quieta y sueño.
- **Sensores de temperatura central (ESA/DLR):** miden el flujo de calor en la frente y el pecho (experimento *Circadian Rhythms*).
- **Dosímetros:**
  - Pasivos (TLD/OSL): se leen en la Tierra.
  - Activos (silicio o centelleadores): miden la tasa en tiempo real, como el CAD de la ESA/DLR en Artemis I.
  - La dosis biológica real tiene gran incertidumbre.
- **Holter de ECG y PA ambulatoria:** por ejemplo, el Tonoport V usado en el estudio de la yugular.

### 4.2 Equipamiento médico de la ISS: el CHeCS

El ***Crew Health Care System*** (CHeCS) tiene tres subsistemas:

1. ***Environmental Health System:*** aire, agua, microbiología, radiación y ruido.
2. ***Countermeasures System:*** ARED, T2 y CEVIS.
3. ***Health Maintenance System.***

Su rack se conecta a la ISS mediante el *Utility Interface Panel*, y sus usuarios principales son los ingenieros biomédicos (BME).

| Equipo | Función |
|---|---|
| BP/ECG | Presión arterial y ECG; entrada de paciente "totalmente flotante y aislada, protegida contra desfibrilación" |
| AED / desfibrilador Zoll | Desfibrilación |
| CMRS (*Crew Medical Restraint System*) | Camilla que sujeta al paciente y al reanimador; permite RCP, ACLS y ATLS, y ayuda a hacer tierra para desfibrilar |
| ALSP / AMP | Soporte vital avanzado / botiquín ambulatorio |
| *Respiratory Support Pack* | Oxígeno y ventilación |
| Bomba de infusión IV | Fluidos y fármacos |
| *Portable Clinical Blood Analyzer* | Gases y electrolitos (tipo i-STAT) |
| *Ultrasound 2* | GE Vivid q modificado; ecografía guiada desde la Tierra |
| Oftalmología | OCT, fundoscopio, tonómetro |

### 4.3 Laboratorio y sensores de nueva generación

Muchas muestras se centrifugan, se congelan y regresan a la Tierra: **esta es la gran brecha para Marte**. El secuenciador MinION ya identificó microbios en la ISS.

| Tecnología | Qué mide | Estado | Limitaciones |
|---|---|---|---|
| Sensores de sudor | Electrolitos, lactato, cortisol | Investigación | Calibración; el sudor no gotea en microgravedad |
| CGM | Glucosa | Comercial en Tierra | Deriva |
| CO₂ personal | pCO₂ cerca de la cara | Prototipos | Calibración |
| Parches ECG/PPG | Ritmo, SpO₂ | Comerciales | Adherencia |
| Implantables | ECG, presión, glucosa | Comercial en Tierra | Cirugía, ética |
| Imagen + IA | Ultrasonido guiado, OCT, fondo de ojo | Desarrollo | Validación clínica |
| Análisis de voz | Estrés, fatiga | Investigación | Privacidad |

> **Interpretación:** los signos vitales ya se resuelven con wearables; el hueco está en el laboratorio y la imagen, que todavía dependen de la Tierra.

### 4.4 IA a bordo

El **CMO-DA** (*Crew Medical Officer Digital Assistant*) de NASA y Google funciona sobre Google Cloud Vertex AI y es multimodal (voz, texto, imágenes). En escenarios simulados alcanzó:

| Escenario | Precisión |
|---|---|
| Lesión de tobillo | 88 % |
| Dolor de oído | 80 % |
| Dolor de flanco | 74 % |

No tiene fecha de lanzamiento y la NASA controla el código.

> **Interpretación:** es un resultado inicial alentador pero insuficiente para tomar decisiones autónomas. Además, una solución en la nube no funcionaría en Marte sin una versión local.

---

## 5. Cómo llega la información a la Tierra

### 5.1 Del sensor al dispositivo a bordo

| Tecnología | Uso | Ventajas | Desventajas |
|---|---|---|---|
| Cable | CHeCS, base del Bio-Monitor | Fiable, da alimentación | Limita el movimiento |
| Bluetooth/BLE | Wearables, oxímetros | Bajo consumo | Alcance corto; seguridad |
| Wi-Fi | Tabletas y portátiles de la estación | Ancho de banda | Consumo, interferencias |
| Zigbee (802.15.4) | Sensores ambientales | Red en malla, bajo consumo | Poco ancho de banda |
| WBAN (IEEE 802.15.6) | Redes corporales con banda médica | Diseñado para salud, QoS | Poca adopción |
| RFID/NFC | Inventario, muestras | Simple | Muy corto alcance |

En una nave hay que sumar la compatibilidad electromagnética con la aviónica, la certificación de las baterías de litio y el funcionamiento sin red.

### 5.2 Red a bordo y almacenamiento

- La ISS combina **buses MIL-STD-1553** para los sistemas críticos con una **LAN Ethernet/Wi-Fi** para las cargas útiles. Los datos de salud se agrupan para el enlace descendente.
- En Marte, el **almacenamiento a bordo debe ser la fuente principal de verdad**, con copia a la Tierra cuando sea posible.

### 5.3 Enlace nave → Tierra

#### ISS

- Usa los satélites geoestacionarios **TDRS** y antenas terrestres (*Near Space Network*). Según la NASA, el paso de los datos tarda menos de un segundo.
- **Banda S** para voz y telemetría; **banda Ku** para alta velocidad.
- En 2019, la NASA duplicó la velocidad de transferencia hasta **600 Mbps** al actualizar el módem de software y la arquitectura terrestre de la *Space Network*.
- Un informe técnico de la NASA (2024) describe el enlace Ku con 500 Mbps máximos de bajada, 600 ms de ida y vuelta y un rendimiento útil típico de 300–425 Mbps.
- La ISS también probó comunicación láser con **ILLUMA-T** y el relé **LCRD**.

#### Luna / Artemis

- Retardo de ~1,3 s por sentido.
- Orion usa la *Deep Space Network* y la *Near Space Network*.
- Artemis II probó un terminal óptico (O2O) y se prevén relés lunares (LunaNet).
- En 2013, la demostración **LLCD** logró 622 Mbps desde ~400.000 km.

#### Marte

- Retardo de ~3–22 minutos por sentido, ancho de banda limitado y apagones durante la conjunción solar.
- La demostración **DSOC** en la sonda Psyche:

  | Fecha | Distancia | Velocidad |
  |---|---|---|
  | Diciembre de 2023 | ~31 millones de km | 267 Mbps (vídeo) |
  | Abril de 2024 | ~226 millones de km | 25 Mbps |
  | Junio de 2024 | ~390 millones de km | 6,25 Mbps sostenidos |

  También envió una señal a ~460 millones de km (la distancia máxima Tierra–Marte) y completó 65 sesiones.
- La NASA estima que el láser mejora entre 10 y 100 veces lo que logra la radio.

> **Interpretación:** en Marte el cuello de botella será la latencia y la disponibilidad (nubes, conjunción, apuntamiento) más que el ancho de banda, por lo que la radio sigue siendo el respaldo.

#### DTN y Bundle Protocol

- Son protocolos de **"almacenar y reenviar"** sobre una red superpuesta. El *Bundle Protocol* es el "IP del Internet del Sistema Solar".
- **Funciones:** custodia, retransmisión automática, LTP, enrutamiento por grafo de contactos y BPSec (autenticación y cifrado).
- La ISS desplegó una pasarela DTN (ION) en 2016. La NASA mantiene ION, DTNME, HDTN y BPLib con soporte de BPv7.
- Según la NASA, con la conclusión del proyecto DTN en enero de 2026, DTN es un servicio operativo en la *Near Space Network* y la *Deep Space Network*. La misión PACE ha enviado más de 34 millones de *bundles* con un 100 % de éxito.
- **Para la salud:** permite "registrar siempre, enviar cuando se pueda", priorizar alertas y no perder datos durante los cortes.

#### CCSDS

Estándares internacionales que facilitan la interoperabilidad entre agencias: *Space Packet*, TM/TC/AOS/USLP, CFDP (usado en la ISS para archivos), *Bundle Protocol* y SDLS.

#### Comparación de escenarios

| Escenario | Retardo por sentido | Ancho de banda | Disponibilidad | Modelo médico |
|---|---|---|---|---|
| ISS | < 1 s (RTT ~600 ms) | Hasta 600 Mbps | Casi continua | Telemedicina casi en tiempo real; evacuación en horas |
| Luna | ~1,3 s | Decenas a cientos de Mbps | Alta con relés | Telemedicina con latencia; evacuación en días |
| Marte | ~3–22 min | Kbps a decenas de Mbps | Interrumpida | Autonomía; la Tierra como consultor diferido |

### 5.4 Cómo ven los datos los médicos de vuelo

#### Equipo

- En el Centro de Control de Houston, el ***Flight Surgeon*** (médico certificado) supervisa la salud de la tripulación desde la consola SURGEON.
- A su lado, el **BME** es el representante técnico del CHeCS y planifica y supervisa las actividades médicas.
- Juntos forman el ***Integrated Medical Group***. Cada expedición tiene un médico líder de la NASA y uno ruso (GCTC).
- Según la NASA, los astronautas no tienen acceso directo a videollamadas por seguridad, y el BME hace de puente.

#### Conferencias médicas privadas (PMC)

- Son regulares y también a petición, por enlaces de audio y vídeo privados desde la sala de apoyo biomédico del MCC-H.
- Según Richard Scheuring, médico de vuelo de la NASA, cada semana los médicos hacen una videoconferencia privada de 15 minutos con cada tripulante de la ISS, y están disponibles las 24 horas, todos los días. En ellas se pregunta, por ejemplo, por síntomas asociados al CO₂.
- También hay conferencias psicológicas y familiares privadas.

#### Telemedicina guiada

Expertos guían la ecografía en tiempo real; las imágenes se descargan en aproximadamente una hora.

#### Datos que se reciben

Experimentos, CHeCS, el traje durante las EVA, el ambiente y los signos vitales en el lanzamiento y la reentrada. Saint-Jacques reconoció que, antes del Bio-Monitor, la monitorización continua se limitaba prácticamente al lanzamiento, el regreso y las caminatas espaciales.

#### Privacidad y protección de datos

- Según la NASA, los datos médicos del LSAH están protegidos por la *Privacy Act* de 1974, la HIPAA (1996) y políticas adicionales de la NASA y del JSC.
- Los datos clínicos residen en el **LSAH** y los de investigación en el **LSDA**. Las publicaciones se revisan para excluir información identificable.
- Históricamente, la información médica se consideró confidencial, conocida solo por el médico de vuelo y el astronauta (IOM, *Safe Passage*, 2001).

#### Consideraciones de diseño

- **Tensión de diseño:** el astronauta es a la vez paciente y operador crítico. Se necesita control de acceso por roles, auditoría y separación de los datos clínicos, operativos y de investigación.
- **Cifrado:** en el enlace (SDLS), en la red (BPSec) y en reposo. Una implementación abierta del *Bundle Protocol* para la ISS usa HMAC-SHA256 y AES-256.
- **Redundancia:** múltiples caminos (Ku, S, óptico), almacenamiento duplicado y dispositivos de respaldo.
- **Autonomía:** sin comunicación, el sistema debe detectar, alertar y guiar a bordo, registrar las decisiones y sincronizar al recuperar el enlace, priorizando las alertas críticas.

---

## 6. Panorama tecnológico para la fase de software

Antecedentes para la [Fase 2](03-arquitectura-vision-fase2.md):

1. **Procesamiento en el borde (*edge*):** filtrar señales y calcular métricas (FC, VFC, respiración) en el wearable o en el servidor de a bordo; enviar resúmenes y eventos.
2. **IA para anomalías:** modelos personalizados con línea base individual, explicables y validados, con alertas graduales. Ejemplos: CMO-DA; aprendizaje profundo con imágenes de fondo de ojo para anemia y SANS (en investigación).
3. **Estándares:**
   - HL7 FHIR (Patient, Observation, Device),
   - IEEE 11073 para dispositivos,
   - IEEE 802.15.6 para WBAN,
   - CCSDS/*Bundle Protocol* para el transporte,
   - LOINC y SNOMED CT.
4. **Visualización:** paneles simples para la tripulación y paneles de tendencias para la Tierra.
5. **Referentes:** Bio-Monitor, CHeCS, *Integrated Medical Model*, *Exploration Medical Capability*, CMO-DA, HDTN, DSOC/LCRD, MARROW y las misiones privadas (Inspiration4, Axiom) con datos multiómicos.

---

## 7. Tabla maestra: problema → parámetro → sensor

| Problema | RIDGE | Cuándo | Parámetros clave | Sensor | Dónde se analiza hoy |
|---|---|---|---|---|---|
| Pérdida ósea | G | Acumulativa | CTX/NTX, Ca urinario, ejercicio | Orina, ARED, DXA | Mayormente en Tierra |
| Atrofia muscular | G | Días → meses | Fuerza, masa, FC en ejercicio | Dinamómetro, ultrasonido | Mixto |
| Cálculos renales | G/E | En vuelo y después | Química urinaria | Tiras, ultrasonido | Mixto |
| Ortostatismo | G | Al regresar | FC/PA posturales, VFC | ECG, PA, LBNP | Aterrizaje |
| Arritmias | G/E/R | En cualquier momento | ECG, QTc | Holter, Astroskin | A bordo |
| Trombosis | G | ~50 días | Flujo yugular | Doppler | A bordo con guía |
| Anemia | G | Todo el vuelo | Hb, CO espirado | Analizador | Mixto |
| SANS | G/E | Larga duración | Δ TRT, agudeza, PIO | OCT, ultrasonido | A bordo |
| Mareo espacial | G | Días 1–3 | Síntomas | Cuestionario, IMU | A bordo |
| Cambios cerebrales | G | Primeros 6 meses | Cognición | PVT, MRI | Mixto |
| Radiación | R | Acumulativa / SPE | Dosis, tasa | Dosímetros | Mixto |
| Inmune/viral | I/E/R | Todo el vuelo | ADN viral, cortisol | Saliva/orina | Tierra |
| Salud mental | I/D | Progresivo | Escalas, VFC, sueño | Apps, actigrafía | Mixto |
| Sueño | I/E | Todo el vuelo | Duración, eficiencia | Actigrafía | A bordo |
| CO₂ / toxicidad | E | Variable | pCO₂, VOC | Monitores ambientales | A bordo |
| Audición | E | Acumulativo | Dosis acústica | Dosímetro de ruido | A bordo |
| EVA | E/G | Durante la EVA | FC, O₂, CO₂ del traje | Telemetría del traje | A bordo / Tierra |
| Emergencias | D | Imprevisible | Todos | CHeCS, AED, CMRS, IA | A bordo |

---

## 8. Recomendaciones para el proyecto

1. **Diseñar para Marte y validar en condiciones de la ISS.** La arquitectura "independiente de la Tierra" (*edge* + almacenamiento local + DTN) funciona en órbita baja (LEO), pero un diseño basado en enviar todo en vivo a la nube no funciona en Marte.
2. **Usar una línea base individual** y analizar tendencias para cada astronauta.
3. **Priorizar cuatro bloques:**
   - monitoreo cardiovascular continuo,
   - dosimetría personal en tiempo real,
   - sueño, conducta y cognición,
   - CO₂.

   Además, planificar la integración a bordo de ultrasonido, OCT y analizadores de laboratorio, que son la brecha principal.
4. **Modelo de datos interoperable** con HL7 FHIR e IEEE 11073 sobre CCSDS/*Bundle Protocol*.
5. **Privacidad desde el diseño:** roles, consentimiento separado para investigación, cifrado y auditoría, conforme a la *Privacy Act*/HIPAA (o al RGPD en Europa).
6. **IA como apoyo, no como reemplazo:** resultados como los del CMO-DA (74–88 %) todavía no justifican decisiones autónomas.

---

## 9. Advertencias y limitaciones

- **Muestras pequeñas:** la mayoría de los estudios incluye entre 6 y 40 astronautas, a menudo hombres de mediana edad. El metaanálisis óseo de 2020 usó datos de solo ~189 de 565 astronautas.
- **Cifras que cambian con las contramedidas:** las pérdidas de la era Mir son mayores que las actuales con ARED. La intolerancia ortostática varía entre ~30 % (metaanálisis de 2026) y > 60–80 % (pruebas de inclinación tras misiones largas), según el protocolo.
- **Marte es una proyección:** la dosis de ~1 Sv se basa en lo que midió el RAD durante un ciclo solar concreto. Los riesgos de radiación para el SNC y el corazón se basan sobre todo en modelos animales.
- **Mareo espacial (60–80 %):** la cifra procede del *Integrated Medical Model* de la NASA (73 % en el programa estadounidense de 1962 a 1998). La nota técnica OCHMO MTB-004 indica que afecta hasta al 73 % de los tripulantes en los primeros 2–3 días.
- **Flujo de datos:** las fuentes primarias dicen que el Bio-Monitor descarga los datos por el "sistema de comunicaciones de la estación"; que use la banda Ku es una inferencia. La NASA describe las PMC como "privadas", pero no se halló documentación pública sobre su cifrado.
- **Rangos de la sección 3:** son referencias terrestres y sugerencias; los umbrales deben fijarlos médicos.
- **IA médica:** los resultados del CMO-DA provienen de pruebas iniciales publicadas por la empresa, sin una evaluación independiente revisada por pares conocida.

---

## 10. Referencias

1. Lang, T. *et al.* (2004). Cortical and trabecular bone mineral loss from the spine and hip in long-duration spaceflight. *Journal of Bone and Mineral Research*, 19(6). https://doi.org/10.1359/JBMR.040307
2. Keyak, J. H. *et al.* (2009). Reduction in proximal femoral strength due to long-duration spaceflight. *Bone*, 44(3). https://doi.org/10.1016/j.bone.2008.11.014
3. Stavnichuk, M. *et al.* (2020). A systematic review and meta-analysis of bone loss in space travelers. *npj Microgravity*, 6. https://doi.org/10.1038/s41526-020-0103-2
4. Trappe, S. *et al.* (2009). Exercise in space: human skeletal muscle after 6 months aboard the International Space Station. *Journal of Applied Physiology*, 106(4). https://doi.org/10.1152/japplphysiol.91578.2008
5. Fitts, R. H. *et al.* (2010). Prolonged space flight-induced alterations in the structure and function of human skeletal muscle fibres. *The Journal of Physiology*, 588(18). https://doi.org/10.1113/jphysiol.2010.188508
6. Meck, J. V. *et al.* (2001). Marked exacerbation of orthostatic intolerance after long- vs. short-duration spaceflight in veteran astronauts. *Psychosomatic Medicine*, 63(6). https://doi.org/10.1097/00006842-200111000-00003
7. Marshall-Goebel, K. *et al.* (2019). Assessment of jugular venous blood flow stasis and thrombosis during spaceflight. *JAMA Network Open*, 2(11). https://doi.org/10.1001/jamanetworkopen.2019.15011
8. Auñón-Chancellor, S. M. *et al.* (2020). Venous thrombosis during spaceflight. *New England Journal of Medicine*, 382. https://doi.org/10.1056/NEJMc1905875
9. Trudel, G. *et al.* (2022). Hemolysis contributes to anemia during long-duration space flight. *Nature Medicine*, 28. https://doi.org/10.1038/s41591-021-01637-7
10. Mader, T. H. *et al.* (2011). Optic disc edema, globe flattening, choroidal folds, and hyperopic shifts observed in astronauts after long-duration space flight. *Ophthalmology*, 118(10). https://doi.org/10.1016/j.ophtha.2011.06.021
11. Sibony, P. A. *et al.* (2023). Prevalencia de SANS según criterios de OCT. *Investigative Ophthalmology & Visual Science* (DOI pendiente).
12. McGregor, H. R. *et al.* (2023). Impacts of spaceflight experience on human brain structure. *Scientific Reports*, 13. https://doi.org/10.1038/s41598-023-33331-8
13. Zeitlin, C. *et al.* (2013). Measurements of energetic particle radiation in transit to Mars on the Mars Science Laboratory. *Science*, 340(6136). https://doi.org/10.1126/science.1235989
14. Hassler, D. M. *et al.* (2014). Mars' surface radiation environment measured with the Mars Science Laboratory's Curiosity rover. *Science*, 343(6169). https://doi.org/10.1126/science.1244797
15. Rooney, B. V. *et al.* (2019). Herpes virus reactivation in astronauts during spaceflight and its application on Earth. *Frontiers in Microbiology*, 10. https://doi.org/10.3389/fmicb.2019.00016
16. Barger, L. K. *et al.* (2014). Prevalence of sleep deficiency and use of hypnotic drugs in astronauts before, during, and after spaceflight. *The Lancet Neurology*, 13(9). https://doi.org/10.1016/S1474-4422(14)70122-X
17. Law, J. *et al.* (2014). Relationship between carbon dioxide levels and reported headaches on the International Space Station. *Journal of Occupational and Environmental Medicine*, 56(5). https://doi.org/10.1097/JOM.0000000000000158
18. Trappe, S. *et al.* (2023). Diferencias por sexo en la atrofia muscular en reposo en cama. *Journal of Applied Physiology* (DOI pendiente).
19. Garrett-Bakelman, F. E. *et al.* (2019). The NASA Twins Study: a multidimensional analysis of a year-long human spaceflight. *Science*, 364(6436). https://doi.org/10.1126/science.aau8650
20. NASA (2022). *NASA-STD-3001, Space Flight Human-System Standard, Vol. 1, Rev. B*. Límites de exposición a la radiación.
21. NASA (2026). *Delay/Disruption Tolerant Networking*. https://www.nasa.gov/communicating-with-missions/delay-disruption-tolerant-networking/
22. Institute of Medicine (2001). *Safe Passage: Astronaut Care for Exploration Missions*. National Academies Press. https://doi.org/10.17226/10218

> Las fuentes de algunas cifras del texto (recuperación ósea de la cohorte de 94 astronautas, litiasis renal, metaanálisis de *Acta Astronautica* 2026, estudio de SANS de 2025, *Sleep* 2022) están pendientes de añadir; ver el [registro de cambios](../../CHANGELOG.md).
