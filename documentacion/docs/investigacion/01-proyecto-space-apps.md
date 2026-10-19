# Artemis III – NASA International Space Apps Challenge 2026

## Software offline-first de monitoreo de salud física y mental para astronautas

> [← Volver al inicio](../../README.md)

| | |
|---|---|
| **Reto** | NASA International Space Apps Challenge 2026 |
| **Integrantes** | Santiago Adrián Rodríguez Ramírez · Jhon Fredy Martínez Díaz · Miguel Hernando Osses Ribero · Julieth Dayana Leguizamón Gélvez · Gabriela Olivar Duarte |
| **Mentor** | Barney Jaimes |
| **Tecnologías** | Laravel (PHP) · MySQL · Inertia.js + Vue 3 · Tailwind CSS v4 · PWA offline-first · Python (simulador) |

> **Documentos relacionados:** [Enfermedades de los astronautas (resumen)](04-enfermedades-resumen.md) · [Informe de fisiopatología espacial](05-informe-fisiopatologia-hrp.md) · [Propuesta tecnológica](../propuesta/03-propuesta-tecnologica.md)

## Índice

1. [Resumen ejecutivo](#1-resumen-ejecutivo)
2. [Introducción](#2-introducción)
3. [Planteamiento del problema](#3-planteamiento-del-problema)
4. [Justificación](#4-justificación)
5. [Objetivos](#5-objetivos)
6. [Estado del arte](#6-estado-del-arte)
7. [Desarrollo del proyecto](#7-desarrollo-del-proyecto)
8. [Referencias](#8-referencias)

---

## 1. Resumen ejecutivo

### 1.1 Propuesta

El proyecto busca desarrollar un **software de monitoreo de salud física y mental** para astronautas en misiones espaciales como las del programa Artemis. Está basado en tecnologías web abiertas (Laravel, Vue, Tailwind CSS y MySQL) y **funciona sin conexión a internet**.

La propuesta responde al reto de la NASA International Space Apps Challenge y a los problemas que enfrentan las tripulaciones:

- cambios cardiovasculares,
- alteraciones del sueño,
- mareo espacial,
- problemas de salud mental causados por el aislamiento,
- comunicación limitada con la Tierra, que impide recibir atención médica en tiempo real.

### 1.2 Metodología

1. **Revisión documental:** documentación del Human Research Program (HRP) de la NASA y estudios en misiones análogas, para identificar los riesgos de salud más relevantes y definir las variables e indicadores a monitorear.
2. **Diseño de la arquitectura:** aplicación web progresiva (PWA) con enfoque *offline-first*.
3. **Programación de los módulos:** monitoreo físico, salud mental, alertas y sincronización, en sprints semanales (ver [estructura Scrum](../propuesta/05-estructura-scrum.md)).
4. **Pruebas:** con datos simulados de una tripulación ficticia, en escenarios con y sin conexión.

### 1.3 Funcionalidades

- Registro de signos vitales.
- Tests cognitivos y seguimiento del sueño y del estado de ánimo.
- Carga y análisis de exámenes médicos.
- Monitoreo del ambiente (CO₂) y de la radiación.
- Motor de alertas tempranas explicables.
- Sincronización con la Tierra tolerante a cortes.

### 1.4 Resultados esperados

Se espera que el software:

- facilite el seguimiento continuo de la salud de la tripulación,
- permita detectar a tiempo cambios físicos y emocionales,
- mantenga los datos disponibles sin conexión y los sincronice con el equipo médico en tierra cuando haya enlace.

### 1.5 Dificultades previstas

| Dificultad | Cómo se aborda |
|---|---|
| Conflictos en la sincronización de datos | Registros inmutables con identificador único y reglas de sincronización simples ([03 §6](../propuesta/03-propuesta-tecnologica.md#6-estrategia-offline-first)) |
| Definición de umbrales de alerta sin criterios médicos oficiales | Umbrales configurables, basados en la documentación de la NASA y probados con escenarios simulados |

### 1.6 Conclusión

La propuesta es una alternativa **viable, accesible y escalable** para apoyar la autonomía médica de los astronautas. Puede aplicarse a misiones futuras como Artemis IV y Marte, y a entornos terrestres con baja conectividad, como zonas rurales o bases científicas aisladas.

---

## 2. Introducción

La exploración espacial tripulada ha tenido un papel fundamental en el avance científico y tecnológico de la humanidad, especialmente con el programa Artemis de la NASA, que busca regresar a la Luna y preparar las futuras misiones a Marte.

Las misiones espaciales afectan la salud de la tripulación de varias formas:

- cambios cardiovasculares,
- pérdida ósea y muscular,
- alteraciones del sueño,
- mareo espacial,
- problemas de salud mental, como ansiedad y depresión, causados por el aislamiento y el confinamiento.

Estos problemas afectan el rendimiento y la toma de decisiones de los astronautas. Se agravan por la comunicación limitada con la Tierra: en una misión a Marte puede haber retrasos de hasta 22 minutos por sentido y cortes de unas dos semanas, lo que impide recibir atención médica en tiempo real.

Este proyecto propone desarrollar un software de monitoreo de salud para astronautas, basado en tecnologías web y con funcionamiento sin conexión a internet.

El objetivo principal es ofrecer una solución tecnológica accesible que permita:

- registrar y monitorear el estado físico y mental de la tripulación,
- generar alertas tempranas,
- sincronizar la información con el equipo médico en tierra,

y así mejorar la autonomía médica y la seguridad de la misión.

---

## 3. Planteamiento del problema

### 3.1 Riesgos de salud en misiones largas

La NASA avanza en el programa Artemis, cuyo objetivo es establecer una presencia humana sostenida en la Luna y preparar el viaje a Marte. Para lograrlo, los astronautas permanecerán cada vez más tiempo y más lejos de la Tierra, expuestos a riesgos que la NASA ha documentado de forma sistemática:

- cambios cardiovasculares,
- alteraciones del sueño,
- mareo espacial,
- pérdida ósea,
- efectos de la radiación,
- trastornos de conducta y salud mental.

### 3.2 Comunicación limitada con la Tierra

En la Estación Espacial Internacional, la tripulación puede comunicarse con médicos y familiares para recibir apoyo. En misiones de espacio profundo, los retrasos y cortes de comunicación harán inviable la atención en tiempo real desde la Tierra. En una misión a Marte, la tripulación debe seguir operando sin contacto con el control de misión, con retrasos de hasta 22 minutos por sentido y bloqueos de unas dos semanas durante la conjunción solar.

> **Contexto de Artemis III:** desde la reestructuración de febrero de 2026, Artemis III es una misión de unas dos semanas en órbita baja terrestre (2027), con comunicación casi en tiempo real. Los retrasos de 22 minutos aplican a Marte. Por eso el software se **diseña para Marte** y se **demuestra en los dos perfiles**: órbita baja (Artemis III) y espacio profundo.

### 3.3 Limitaciones de las herramientas actuales

- Las herramientas digitales de salud usadas en la Tierra, como las aplicaciones de telemonitoreo, suelen depender de una conexión constante a internet para almacenar y consultar datos, por lo que son poco adecuadas para entornos con conectividad limitada.
- La salud mental de la tripulación rara vez se registra de forma continua, aunque los estudios en ambientes de aislamiento muestran deterioro del sueño, el ánimo y el rendimiento cognitivo.

### 3.4 Pregunta de investigación

> ¿Cómo puede un software de monitoreo de salud con funcionamiento offline, desarrollado con tecnologías web abiertas, apoyar a los astronautas de las misiones Artemis y de futuras misiones a Marte en la detección temprana de riesgos físicos y de salud mental, sin depender de una conexión permanente con la Tierra?

---

## 4. Justificación

### 4.1 Importancia de la salud de la tripulación

La salud de la tripulación es indispensable para el éxito de cualquier misión espacial. La NASA mantiene un programa completo, el Human Research Program, dedicado a entender y reducir los riesgos para la salud humana en el espacio. A medida que las misiones se alejen de la Tierra, la tripulación necesitará más autonomía médica, porque no podrá esperar indicaciones inmediatas del equipo en tierra.

### 4.2 Aporte científico y tecnológico

El proyecto combina el monitoreo físico y el de salud mental en una sola herramienta que funciona sin conexión gracias al enfoque de aplicación web progresiva. Este enfoque permite:

- instalar el software en cualquier dispositivo con navegador, sin depender de un sistema operativo específico;
- sincronizar los datos cuando haya enlace.

### 4.3 Aporte humano

La evidencia de los análogos espaciales muestra que el aislamiento afecta el sueño, el ánimo y el rendimiento, y que estas alteraciones pueden pasar desapercibidas si no se registran de forma continua. Un registro diario con alertas tempranas ayuda a detectarlas a tiempo y a proteger el bienestar de la tripulación.

### 4.4 Aporte regional y académico

- Colombia ha manifestado su interés en participar en la exploración espacial y en investigaciones sobre el comportamiento humano en entornos espaciales asociadas al proyecto Artemis.
- El proyecto permite que estudiantes de Santander apliquen sus conocimientos de desarrollo de software a un problema real propuesto por la NASA.

### 4.5 Aplicaciones en la Tierra

El software puede usarse para el monitoreo de salud en:

- zonas rurales o de baja conectividad,
- bases científicas en la Antártida,
- plataformas aisladas.

Por estas razones, el proyecto se centra en un software de monitoreo de salud *offline-first* que ayude a los astronautas a vigilar su estado físico y mental, reduzca la dependencia de la comunicación con la Tierra y sirva de base para misiones futuras como Artemis IV y Marte.

---

## 5. Objetivos

### 5.1 Objetivo general

Desarrollar un software de monitoreo de salud física y mental para astronautas en misiones espaciales, basado en tecnologías web abiertas con funcionamiento offline, que permita la detección temprana de riesgos y la sincronización de la información con el equipo médico en tierra.

### 5.2 Objetivos específicos

1. **Identificar** los principales riesgos de salud física y mental de los astronautas, mediante la revisión de la documentación del Human Research Program de la NASA y de estudios en ambientes análogos, para definir las variables, indicadores y umbrales de alerta que el software debe monitorear.
2. **Diseñar** la arquitectura del software y su interfaz de usuario con el enfoque de aplicación web progresiva (PWA) *offline-first*, a partir de los riesgos priorizados, para garantizar el registro, la consulta y las alertas aun sin conexión a internet.
3. **Implementar** los módulos de monitoreo físico, salud mental, exámenes, alertas y sincronización con Laravel, MySQL, Vue (Inertia) y Tailwind CSS, usando almacenamiento local en el navegador y una API en el servidor, para procesar los datos de la tripulación y enviarlos al equipo en tierra cuando haya enlace.
4. **Validar** el funcionamiento del software mediante pruebas con datos simulados en Python de una tripulación ficticia, en escenarios con y sin conexión, para verificar la generación de alertas, la integridad de los datos y la correcta sincronización.

---

## 6. Estado del arte

### 6.1 Panorama general

La salud humana en el espacio se ha convertido en un campo de investigación prioritario a medida que las agencias espaciales planean misiones más largas y lejanas. La NASA organiza su investigación en torno a cinco peligros del vuelo espacial y a un conjunto de riesgos específicos para la salud de la tripulación. Otras agencias, como la ESA y la Agencia Espacial Canadiense, han desarrollado estudios y herramientas para aumentar la autonomía médica de los astronautas.

### 6.2 Fenómenos investigados

- **Cambios fisiológicos:** alteraciones cardiovasculares, pérdida ósea y muscular, cambios en el sistema inmune, mareo espacial y síndrome neuro-ocular asociado al vuelo espacial (SANS).
- **Salud mental y conducta:** el aislamiento y el confinamiento prolongados pueden aumentar el riesgo de ansiedad y depresión, y afectar el sueño, la moral y la toma de decisiones.
- **Comunicación y autonomía:** en misiones a Marte, los retrasos de hasta 22 minutos por sentido y los bloqueos por conjunción solar obligan a la tripulación a operar sin contacto con el control de misión.

### 6.3 Cómo se identificaron estos problemas

- Estudios a bordo de la Estación Espacial Internacional con mediciones médicas de los astronautas.
- Misiones análogas en la Tierra que simulan el aislamiento de un viaje a Marte, como Mars500 y CHAPEA.
- Modelado y análisis de trayectorias para estimar los retrasos y cortes de comunicación de una misión a Marte.

### 6.4 Evidencias empíricas y metodologías

- Seguimiento continuo del sueño, la actividad y el desempeño cognitivo de la tripulación durante el aislamiento.
- Revisión por expertos médicos de más de 100 condiciones de salud, clasificadas según su urgencia y su riesgo vital, para definir cuáles debe atender la tripulación por sí misma.
- Desarrollo de sistemas de telemonitoreo con dispositivos que miden signos vitales y envían los datos a una plataforma web para el equipo médico.

### 6.5 Productos de las investigaciones

- **Artículos científicos:** sobre los efectos del aislamiento en la salud y el diseño de sistemas de apoyo a decisiones médicas.
- **Prototipos y herramientas de software:** bases de conocimiento médico, sistemas de apoyo a la decisión clínica y aplicaciones de telemonitoreo de signos vitales.

### 6.6 Antecedentes

A continuación se presentan estudios y evidencias relacionados en los niveles local, nacional e internacional.

#### Nivel local (Bucaramanga, Santander)

La investigación sobre salud espacial en Santander es limitada, pero existen desarrollos de telemonitoreo de salud directamente relacionados con el objetivo de este proyecto.

- **Fundación Cardiovascular de Colombia (FCV) – Telsy:** sistema de telemonitoreo que mide presión arterial, saturación de oxígeno, temperatura, peso y frecuencias cardiaca y respiratoria. Los datos se suben a una plataforma web visible para el equipo médico, que además programa recordatorios de ejercicio y medicamentos. En 2022 los prototipos eran funcionales y estaban pendientes de validación con pacientes.
- **Universidad Industrial de Santander (UIS) – Interfaz de usuario para el telemonitoreo de falla cardiaca:** trabajo de grado que diseñó, con un enfoque centrado en el usuario, la interfaz de una aplicación móvil para que los pacientes con falla cardiaca comprendan el monitoreo de sus signos vitales y su autocuidado.

#### Nivel nacional (Colombia)

- **Programa espacial de la Fuerza Aeroespacial Colombiana:** opera los satélites de observación FACSAT-1 y FACSAT-2 y un Centro de Operaciones Espaciales. Ha reportado su participación en investigaciones sobre el comportamiento humano en entornos espaciales asociadas al proyecto Artemis, lideradas por un centro de medicina aeroespacial de la Fuerza Aérea junto con universidades.
- **Marco normativo:** la Ley 1419 de 2010 establece los lineamientos para el desarrollo de la telesalud en Colombia y respalda el uso de tecnologías de la información para atender a personas en lugares de difícil acceso. La Ley 1581 de 2012 regula la protección de los datos personales, incluidos los datos de salud, que son sensibles.

#### Nivel internacional

- **Mars500 (ESA y Rusia):** simulación de 520 días de una misión a Marte con seis tripulantes. La adaptación fue muy desigual: se observaron hipocinesia, desfases del ciclo de sueño y vigilia, síntomas depresivos y descensos del rendimiento en una parte de la tripulación (Basner *et al.*, 2013). Esto demuestra la necesidad de vigilar el sueño y el ánimo de forma continua e individual.
- **CHAPEA (NASA):** misión análoga de 378 días en un hábitat impreso en 3D de unos 1.700 pies cuadrados (≈ 158 m²) en el Centro Espacial Johnson, con cuatro tripulantes. La primera misión terminó en julio de 2024 y generó miles de datos sobre salud y desempeño. La segunda comenzó el 19 de octubre de 2025 y termina el 31 de octubre de 2026.
- **MEDEA (España):** propuesta de un sistema de apoyo a la decisión clínica en tiempo real para emergencias médicas en misiones a Marte. Incluye módulos de decisión autónoma a bordo, aprendizaje en la Tierra e interoperabilidad con los sistemas de salud de la nave.

---

## 7. Desarrollo del proyecto

### 7.1 Equipo de desarrollo

| Integrante | Rol | Capa del sistema a cargo |
|---|---|---|
| Santiago Adrián Rodríguez Ramírez | Líder del proyecto (*Product Owner*) y desarrollo backend | Arquitectura, API REST en Laravel, base de datos MySQL, despliegue en el dominio con cPanel |
| Julieth Dayana Leguizamón Gélvez | Desarrollo backend | Módulos clínicos y de analítica en Laravel con MySQL |
| Gabriela Olivar Duarte | Desarrollo frontend | Interfaz con Vue 3, Inertia y Tailwind CSS v4; gráficas; interfaz de alertas |
| Jhon Fredy Martínez Díaz | Desarrollo frontend | Interfaz con Vue 3, Inertia y Tailwind CSS v4; gráficas; PWA offline |
| Miguel Hernando Osses Ribero | Análisis de datos y pruebas (QA) | Simulador de datos en Python, plan y ejecución de pruebas, documentación y presentación |

### 7.2 Arquitectura tecnológica

| Capa | Tecnología |
|---|---|
| Frontend | Vue 3 + Inertia.js, Tailwind CSS v4, Apache ECharts |
| Funcionamiento offline | PWA *offline-first* con Service Worker e IndexedDB |
| Backend | Laravel (PHP) con API REST para el simulador y los dispositivos |
| Base de datos | MySQL |
| Simulación de datos | Python (NumPy, NeuroKit2) |
| Despliegue | Dominio propio administrado con cPanel |

El detalle y la justificación están en la [propuesta tecnológica](../propuesta/03-propuesta-tecnologica.md).

### 7.3 Módulos del sistema

1. **Monitoreo físico:** registro de signos vitales.
2. **Salud mental:** tests cognitivos y seguimiento del sueño y del estado de ánimo.
3. **Exámenes médicos:** carga, interpretación y evolución de resultados.
4. **Ambiente y radiación:** CO₂ por módulo de la nave, correlación con síntomas y dosis acumulada.
5. **Alertas:** motor de alertas tempranas con umbrales configurables.
6. **Sincronización:** envío de datos al equipo médico en tierra cuando haya enlace.

La lista completa de 16 módulos está en [Módulos del sistema](../propuesta/02-modulos-del-sistema.md).

---

## 8. Referencias

1. Basner, M. *et al.* (2013). Mars 520-d mission simulation reveals protracted crew hypokinesis and alterations of sleep duration and timing. *Proceedings of the National Academy of Sciences*, 110(7), 2635–2640. https://doi.org/10.1073/pnas.1212646110
2. NASA (2026). *Crew Health and Performance Exploration Analog (CHAPEA)*. https://www.nasa.gov/humans-in-space/chapea
3. Congreso de Colombia (2010). Ley 1419 de 2010, por la cual se establecen los lineamientos para el desarrollo de la telesalud en Colombia.
4. Congreso de Colombia (2012). Ley Estatutaria 1581 de 2012, por la cual se dictan disposiciones generales para la protección de datos personales.
5. Institute of Medicine (2001). *Safe Passage: Astronaut Care for Exploration Missions*. National Academies Press. https://doi.org/10.17226/10218

Las referencias biomédicas detalladas están en la [Fase 1](02-monitorizacion-salud-fase1.md#10-referencias), el [resumen de enfermedades](04-enfermedades-resumen.md#artículos-científicos-de-referencia) y el [informe HRP](05-informe-fisiopatologia-hrp.md#9-referencias-citadas). Las fuentes de Telsy (FCV), el trabajo de grado de la UIS, el programa de la Fuerza Aeroespacial Colombiana y MEDEA están pendientes de añadir (ver [registro de cambios](../../CHANGELOG.md)).
