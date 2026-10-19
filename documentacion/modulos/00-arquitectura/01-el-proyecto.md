# 1 · El proyecto

> [← Guía](README.md) · Siguiente: [Arquitectura](02-arquitectura.md)

## Qué es AstroHealth

Una plataforma web que **monitoriza la salud física y mental de una tripulación espacial** y que sigue funcionando cuando no hay comunicación con la Tierra. Lema: *"Registrar siempre, decidir a bordo, sincronizar cuando se pueda."*

La presentamos en el **NASA International Space Apps Challenge 2026**, en el reto de software de monitorización de la salud de astronautas.

## El problema que resuelve

- Muchos riesgos de salud en el espacio son **silenciosos**: la pérdida ósea, los cambios en el ojo (SANS) o el deterioro cognitivo avanzan sin síntomas.
- La salud mental casi nunca se mide de forma continua, aunque el aislamiento empeora el sueño, el ánimo y la atención.
- Lejos de la Tierra **no hay telemedicina en tiempo real**: en Marte la señal tarda de 3 a 22 minutos por sentido y hay apagones de dos semanas.

## Qué hace el sistema

1. **Registra** signos vitales, sueño, tests cognitivos, ánimo, síntomas, exámenes, ambiente de la nave y radiación.
2. **Compara a cada astronauta consigo mismo** (línea base personal), no solo con valores poblacionales.
3. **Detecta y explica** valores peligrosos, deterioros lentos y patrones combinados.
4. **Alerta y guía** al oficial médico a bordo con prioridades y protocolos paso a paso.
5. **Sincroniza con la Tierra** cuando hay enlace, primero lo crítico y sin perder datos.

## Para quién: los usuarios y sus aplicaciones

| Rol | Dónde trabaja | Aplicación | Qué hace |
|---|---|---|---|
| Astronauta | Nave | **F1 · Mi salud** (PWA, funciona sin conexión) | Registra sus datos y ve su estado |
| Oficial médico a bordo (CMO) | Nave | **F2 · Tripulación** (y F1 para sí mismo) | Ve a toda la tripulación, atiende alertas, sigue protocolos |
| Médico de vuelo | Tierra | **F3 · Centro médico Tierra** | Revisa tendencias, define umbrales, envía recomendaciones |
| Ingeniero biomédico, administrador, investigador | Nave o Tierra | **F4 · Administración** | Usuarios, dispositivos, tokens, catálogos, auditoría, exportación anonimizada |

Hay **dos instalaciones** del mismo código: el **nodo nave** y el **nodo Tierra**, que se comunican por un enlace simulado con retardo y apagones.

## Qué vamos a mostrar: los 9 escenarios de la demo

Un simulador en Python genera datos realistas de 4 tripulantes y provoca estas situaciones. Cada escenario debe funcionar en vivo:

| ID | Escenario | Qué debe pasar |
|---|---|---|
| E0 | Misión normal | Ninguna alarma falsa en 30 días |
| E1 | Arritmia en ejercicio | Alerta crítica y protocolo guiado |
| E2 | Evento solar | Alarma crítica a toda la tripulación: "ir al refugio" |
| E3 | CO₂ alto + dolores de cabeza | Aviso del módulo de la nave y relación con las cefaleas |
| E4 | Fatiga acumulada | Aviso de deterioro cognitivo al CMO |
| E5 | 14 días sin enlace | La nave sigue funcionando y al volver el enlace todo llega en orden |
| E6 | Sensor suelto | Datos marcados como artefacto, sin falsa alarma |
| E7 | Anemia progresiva | Aviso de tendencia en los exámenes |
| E8 | Oxígeno bajo | Aviso, confirmación y crítico si sigue bajando |

Detalle en [04 §7](../../docs/propuesta/04-manejo-de-datos.md#7-escenarios-de-simulación).

## Fechas

| Fecha (2026) | Qué pasa |
|---|---|
| lun 19 oct (noche) | Reunión de arranque |
| mar 20 – mar 10 nov | **Desarrollo** en 5 fases ([plan](../../scrum/plan-de-sprints.md#2-resumen-de-sprints)) |
| mié 28 oct | La NASA publica los enunciados y datos oficiales del reto; revisamos el alcance |
| mié 11 nov | Congelación del código: solo se corrigen errores |
| vie 13 nov | Ensayo general con los 9 escenarios |
| sáb 14 – dom 15 nov | **Evento**: presentación, demo en vivo y entrega oficial |

## El equipo

| Persona | Rol en el proyecto |
|---|---|
| Julieth | Backend: Laravel y MySQL de todos los módulos |
| Jhon | Frontend: Vue, Inertia y Tailwind |
| Gabriela | Frontend: Vue, Inertia y Tailwind |
| Miguel | Simulador en Python, QA, documentación y demo; *Scrum Master* |
| Santiago | *Product Owner*: prioriza, despliega en cPanel y mantiene el repositorio y la CI |

El reparto exacto por historia está en el [plan de sprints](../../scrum/plan-de-sprints.md).

## Qué es "ganar"

Llegamos al evento con:

1. **Los 9 escenarios funcionando en vivo**, sin trucos, sobre los dos nodos desplegados.
2. **Código limpio**: la misma estructura en los 16 módulos, pruebas en verde y nada de valores clínicos escritos a mano en el código (todo sale de la configuración de M16).
3. **Un pitch claro**: el problema, la autonomía sin la Tierra y la explicación de cada alerta.
4. **Material de entrega** completo: video, descripción y enlaces.

## Qué **no** es

- No es un dispositivo médico certificado: es un prototipo académico de **apoyo a la decisión**.
- Todos los datos son **simulados**.
- No usamos *machine learning*, WebSockets ni FHIR en esta versión: quedan como evolución para después del evento.

## Para profundizar

| Documento | Para qué |
|---|---|
| [README del proyecto](../../README.md) | Propuesta de valor y decisiones tomadas |
| [02 · Módulos del sistema](../../docs/propuesta/02-modulos-del-sistema.md) | Diseño completo de los 16 módulos y las 4 aplicaciones |
| [03 · Propuesta tecnológica](../../docs/propuesta/03-propuesta-tecnologica.md) | Por qué este stack y cómo se despliega |
| [04 · Manejo de datos](../../docs/propuesta/04-manejo-de-datos.md) | Datos, simulación, análisis y umbrales |
| [Glosario](../../docs/glosario.md) | Siglas (SANS, PVT, CMO, SPE…) |
