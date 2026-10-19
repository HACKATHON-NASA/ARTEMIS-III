# M08 · Ambiente

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M08 |
| **Grupo** | Clínico (núcleo del MVP) |
| **Carpeta Laravel** | `astrohealth/app/Modules/Ambiente/` |
| **Épica** | EP-08 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Gabriela · **Otros:** Miguel (simulación de HU-08.03) |
| **Sprints** | 2 (HU-08.01) · 3 (HU-08.02, HU-08.03) |
| **Escenarios** | E3 (CO₂ alto + cefaleas) |
| **Afecciones** | Exposición a CO₂, contaminantes, ruido ([01 §3.9](../../docs/propuesta/01-analisis-enfermedades.md#39-ambiente-del-hábitat)) |

## 2. Propósito

Vigilar **la atmósfera de cada módulo de la nave** y relacionarla con la salud de la tripulación. En microgravedad no hay convección natural y el CO₂ se acumula localmente; por encima de 3 mmHg aumentan las cefaleas y los errores en los procedimientos ([Fase 1 §2.11](../../docs/investigacion/02-monitorizacion-salud-fase1.md#a-co₂-elevado)). El módulo muestra el pCO₂ por módulo y aporta la regla que cruza el CO₂ alto con las cefaleas reportadas.

## 3. Alcance

| Hace | No hace |
|---|---|
| Series de pCO₂, VOC, partículas y ruido por módulo de la nave | Controlar la ventilación de la nave |
| Promedio horario y promedio de 7 días por módulo | Inventar límites: las reglas `AMB_*` nacen inactivas y sin valor |
| Alertas por VOC, partículas y ruido con los límites que cargue el médico de vuelo con su fuente (HU-08.03) | Calcular la dosis acústica acumulada (evolución) |
| Regla de correlación CO₂–cefalea (`ReglaCo2Cefalea`), ejecutada por M12 | Saber en qué módulo estaba cada tripulante (no hay localización) |
| Aviso por CO₂ alto sostenido aunque nadie tenga síntomas (`CO2_ALTO`) | Inventar un nivel crítico de CO₂ sin fuente |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Atmósfera | pCO₂ por módulo (`AH-PCO2`), VOC (`AH-VOC`) y partículas (`AH-PM`) |
| Ruido | Nivel de ruido (`AH-RUIDO`) |
| Correlación ambiente–salud | `ReglaCo2Cefalea` (implementa `Analitica\Contratos\ReglaAnalitica`) |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) (consultas e índices; sin tablas propias) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md) (HU-08.02 no tiene pantalla: los avisos los muestra M13).

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-08.01 Ver el pCO₂ por módulo | 2 | F2 muestra cada módulo con su valor actual y su gráfica de 24 h |
| HU-08.02 Relacionar el CO₂ con las cefaleas | 3 | **E3 pasa:** aviso de correlación por tripulante y aviso global `CO2_ALTO`, ambos con la recomendación de ventilar; un CO₂ alto sin síntomas también avisa |
| HU-08.03 Alertas por VOC, partículas y ruido | 3 | El simulador genera las tres series; las reglas `AMB_*` solo alertan cuando el médico de vuelo las activa con valor y fuente |
