# AstroHealth

### Monitorización autónoma de la salud física y mental de astronautas

> **NASA International Space Apps Challenge 2026** · Fase de análisis y diseño
> *"Registrar siempre, decidir a bordo, sincronizar cuando se pueda."*

| | |
|---|---|
| **Reto** | Software de monitorización de la salud de astronautas en misiones espaciales |
| **Equipo** | Santiago Adrián Rodríguez Ramírez · Julieth Dayana Leguizamón Gélvez · Gabriela Olivar Duarte · Jhon Fredy Martínez Díaz · Miguel Hernando Osses Ribero |
| **Mentor** | Barney Jaimes |
| **Stack** | Laravel (PHP) · Inertia.js + Vue 3 · Tailwind CSS v4 · MySQL · Python (simulador) |
| **Estado** | Propuesta de diseño aprobada; desarrollo del 20 de octubre al 10 de noviembre de 2026, estabilización y ensayo hasta el 13 |
| **Evento** | 14 y 15 de noviembre de 2026 (evaluación desde el 16 de noviembre) |

---

## Documentación

### Propuesta

| # | Documento | Contenido |
|:---:|---|---|
| 01 | [Análisis de enfermedades y afecciones](docs/propuesta/01-analisis-enfermedades.md) | 33 afecciones con causa, indicadores, datos y abordaje; agrupación en áreas; priorización |
| 02 | [Módulos del sistema](docs/propuesta/02-modulos-del-sistema.md) | Arquitectura por capas, 16 módulos de backend, 4 aplicaciones de frontend, eventos, permisos y bocetos de pantallas |
| 03 | [Propuesta tecnológica](docs/propuesta/03-propuesta-tecnologica.md) | Stack justificado, evaluación de la visión de la Fase 2, despliegue y estrategia offline |
| 04 | [Manejo de datos](docs/propuesta/04-manejo-de-datos.md) | Tipos de datos, simulación realista, procesamiento, deterioro cognitivo, exámenes y umbrales |
| 05 | [Estructura Scrum](docs/propuesta/05-estructura-scrum.md) | Roles, 19 épicas, backlog, historias modelo y plan de sprints hasta el evento |

### Investigación de base

| # | Documento | Contenido |
|:---:|---|---|
| 01 | [Proyecto Space Apps](docs/investigacion/01-proyecto-space-apps.md) | Planteamiento, justificación, objetivos, estado del arte y equipo |
| 02 | [Monitorización de la salud (Fase 1)](docs/investigacion/02-monitorizacion-salud-fase1.md) | Problemas médicos, parámetros, sensores y comunicaciones nave–Tierra |
| 03 | [Arquitectura de largo plazo (Fase 2)](docs/investigacion/03-arquitectura-vision-fase2.md) | Visión industrial del sistema (no es el stack del MVP) |
| 04 | [Enfermedades de los astronautas (resumen)](docs/investigacion/04-enfermedades-resumen.md) | Resumen divulgativo y contexto de Artemis III |
| 05 | [Informe de fisiopatología espacial (HRP)](docs/investigacion/05-informe-fisiopatologia-hrp.md) | Mecanismos y contramedidas según el Programa de Investigación Humana de la NASA |

### Scrum

| Documento | Contenido |
|---|---|
| [Plan de sprints](scrum/plan-de-sprints.md) | Las 71 historias en orden por fase, responsables, dependencias, ritmo de trabajo, criterios de cierre, contratos compartidos y reglas para trabajar con IA |
| [Fundaciones (EP-00)](scrum/fundaciones/README.md) | Historias detalladas de la base técnica: repositorio y librería de componentes |

### Módulos

| Documento | Contenido |
|---|---|
| [Guía del proyecto y de la arquitectura](modulos/00-arquitectura/README.md) | **Empiece aquí:** el proyecto, las capas, el formato de API y errores, el manual de Vue y la forma de trabajo (Git y Jira) |
| [Fichas técnicas de los módulos](modulos/README.md) | Una ficha por módulo (M01–M16 y simulador), dividida en `backend/`, `frontend/` y `database/`: API, reglas, páginas, *props*, tablas, relaciones, migraciones, historias y pruebas |

**Apoyo:** [Glosario de siglas](docs/glosario.md) · [Registro de cambios](CHANGELOG.md) · [Herramientas de revisión](herramientas/README.md) · [Resumen de la revisión (16 de octubre)](docs/revision-2026-10-16.md)

---

## Propuesta de valor

### El problema

Lejos de la Tierra, la salud de la tripulación se vuelve un **riesgo de misión**:

- **Los riesgos son medibles, pero muchos son silenciosos.** El SANS afecta a ~70 % de las tripulaciones de larga duración y la pérdida ósea avanza al 1–1,5 % por mes, en ambos casos a menudo sin síntomas.
- **La salud mental rara vez se registra de forma continua**, aunque el aislamiento deteriora el sueño (~6 h de promedio), el ánimo y la cognición.
- **La telemedicina en tiempo real deja de existir.** En Marte el retardo es de ~3 a 22 minutos por sentido, con apagones de unas dos semanas en cada conjunción solar.
- **Las herramientas de telemonitoreo terrestres dependen de una conexión permanente.**

### La solución

**AstroHealth** es una plataforma *offline-first* que:

1. **Registra** signos vitales, sueño, tests cognitivos, estado emocional, síntomas y exámenes de cada tripulante.
2. **Compara a cada astronauta consigo mismo** mediante una línea base personal, no solo con umbrales poblacionales.
3. **Detecta y explica** valores peligrosos, deterioros lentos y patrones combinados (por ejemplo, mal sueño con atención más lenta).
4. **Alerta y guía** al oficial médico a bordo con niveles de prioridad y protocolos paso a paso.
5. **Sincroniza con la Tierra** cuando hay enlace, priorizando lo crítico y sin perder datos.

### Para quién

| Usuario | Qué gana |
|---|---|
| **Astronauta** | Conoce su estado en segundos y registra datos sin interrumpir sus tareas |
| **Oficial médico a bordo (CMO)** | Ve a toda la tripulación y recibe alertas explicadas y protocolos guiados |
| **Médico de vuelo (Tierra)** | Tendencias longitudinales e historial completo, incluso tras semanas sin enlace |

### Qué nos diferencia

| Diferenciador | Por qué importa |
|---|---|
| **Físico + mental en una sola herramienta** | Las afecciones interactúan; analizarlas por separado pierde información |
| **Autonomía real** | Diseñado para Marte y demostrado también en el perfil de órbita baja de Artemis III |
| **Línea base personal** | En el espacio lo "normal" cambia; importa la desviación individual |
| **Alertas explicables** | El médico ve por qué se generó cada alerta y decide con confianza |
| **Accesible y ligero** | Tecnologías web abiertas que funcionan en cualquier navegador |

### Impacto más allá del espacio

La misma arquitectura sirve donde la conectividad es limitada: **zonas rurales de Colombia, bases científicas en la Antártida o plataformas aisladas**, en línea con el marco de telesalud de la Ley 1419 de 2010 y con la protección de datos personales de la Ley 1581 de 2012.

---

## Uso de datos y recursos de la NASA

| Recurso | Cómo lo usa AstroHealth |
|---|---|
| Evidencia del **Human Research Program (HRP)** y del marco RIDGE | Catálogo de afecciones, priorización y módulos ([01](docs/propuesta/01-analisis-enfermedades.md)) |
| **NASA-STD-3001** y notas técnicas de la OCHMO | Límites de radiación y de CO₂, referencias de umbrales ([04 §8](docs/propuesta/04-manejo-de-datos.md#8-configuración-inicial-de-umbrales)) |
| Estudios de la NASA en la ISS y en análogos (Twins Study, CHAPEA, HERA) | Parámetros del simulador: sueño, hemólisis, pérdida ósea, rendimiento cognitivo ([04 §3](docs/propuesta/04-manejo-de-datos.md#3-generación-de-datos-simulados-realistas)) |
| Datos de radiación del **MSL/RAD** (Curiosity) | Tasa de dosis del perfil de crucero a Marte |
| Perfiles de comunicación (NSN, DSN, DTN) | Retardos y ventanas del enlace simulado ([03 §5](docs/propuesta/03-propuesta-tecnologica.md#5-arquitectura-de-despliegue)) |
| **NASA Open Science Data Repository (OSDR)** | Fuente para ajustar el simulador con los conjuntos de datos que publique el reto el 28 de octubre |

---

## Decisiones tomadas

| Decisión | Elección | Dónde se justifica |
|---|---|---|
| Alcance del MVP | **Producto completo:** los 16 módulos, las 4 aplicaciones y los 9 escenarios de demo (E0–E8) | [01 §5](docs/propuesta/01-analisis-enfermedades.md#5-priorización-para-el-producto) |
| Arquitectura | Monolito modular por capas, con módulos comunicados por eventos | [02 §1–2](docs/propuesta/02-modulos-del-sistema.md#1-principios-de-diseño) |
| Backend | **Laravel** (PHP) con MySQL | [03 §2](docs/propuesta/03-propuesta-tecnologica.md#2-decisión-de-stack) |
| Frontend | **Inertia.js + Vue 3 + Tailwind CSS v4** como PWA, con ECharts | [03 §3](docs/propuesta/03-propuesta-tecnologica.md#3-stack-por-capa) |
| Simulación | **Python** con perfiles individuales y escenarios inyectables | [04 §3](docs/propuesta/04-manejo-de-datos.md#3-generación-de-datos-simulados-realistas) |
| Comunicación espacial | **Enlace simulado** (retardo y ventanas) en lugar de DTN real | [03 §5](docs/propuesta/03-propuesta-tecnologica.md#5-arquitectura-de-despliegue) |
| Análisis | Reglas + estadística explicable en el MVP; machine learning como evolución | [04 §4.4](docs/propuesta/04-manejo-de-datos.md#44-métodos-de-análisis-explicables) |
| Calendario | 5 fases cortas (20 de octubre – 10 de noviembre) + estabilización hasta el 13, con un orden de recorte definido si hay retrasos | [Plan de sprints](scrum/plan-de-sprints.md) |

---

## Preguntas abiertas para el equipo

| # | Pregunta | Por qué importa | Propuesta por defecto |
|:---:|---|---|---|
| 1 | ¿El hosting de cPanel permite subdominios y tareas cron? | Necesario para los nodos nave y Tierra | Sí; si no, se aplica el plan B de un solo despliegue |
| 2 | ¿Qué escenarios serán los protagonistas del pitch? | Hay 9 escenarios y el pitch dura pocos minutos | E4 (fatiga acumulada), E3 (CO₂ + cefaleas) y E5 (conjunción solar) |
| 3 | ¿Se presenta con el contexto de Artemis III (órbita baja, ~2 semanas) o de espacio profundo? | Cambia el mensaje del pitch y los perfiles del simulador | Diseñar para Marte y demostrar en los dos perfiles |
| 4 | ¿El enunciado oficial del reto (28 de octubre) coincide con nuestro planteamiento? | Puede exigir datos o entregables concretos | Revisarlo en el refinamiento del Sprint 2 |
| 5 | ¿Las reglas del evento admiten trabajo previo al 14 de noviembre? | Define qué se presenta como "construido en el evento" | Confirmarlo con la organización local antes del Sprint 1 |
| 6 | ¿Cómo se escriben exactamente los nombres del equipo? | Aparecen en la entrega oficial | Las formas de la tabla de arriba |
| 7 | ¿El "250 mSv por evento" de NASA-STD-3001 es en realidad un límite de 30 días (250 mGy-Eq, órganos hematopoyéticos)? | Cambia la pantalla del evento solar ([M09](modulos/M09-radiacion/backend/README.md#3-reglas-de-negocio)) | Mostrarlo como "referencia de 30 días" hasta confirmarlo |
| 8 | ¿Qué límites de VOC, partículas o ruido tienen fuente pública? | Sin al menos uno, HU-08.03 no se puede mostrar activa en la demo | Buscar un SMAC de un compuesto concreto y simular ese compuesto ([M08](modulos/M08-ambiente/backend/README.md#3-reglas-de-negocio)) |
| 9 | ¿La tasa de dosis lunar de Chang'e-4 (~1,37 mSv/d) se confirma en el artículo original? | Reemplaza el valor provisional del perfil `luna` | Mantener 1,8 mSv/d hasta verificarlo |

---

> ⚠️ **Aviso:** AstroHealth es un prototipo académico de **apoyo a la decisión**, no un dispositivo médico certificado. Todos los datos son simulados y los umbrales son referencias que deberían validar profesionales de la salud.
