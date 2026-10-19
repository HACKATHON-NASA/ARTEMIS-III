# AstroHealth — Arquitectura de largo plazo (Fase 2)

> Documento de diseño: flujo de la aplicación, arquitectura, funcionalidades, machine learning y stack tecnológico.
> Base: [informe de Fase 1](02-monitorizacion-salud-fase1.md) (problemas de salud, parámetros, sensores y comunicaciones).
>
> [← Volver al inicio](../../README.md) · **Documentos relacionados:** [Proyecto Space Apps](01-proyecto-space-apps.md) · [Enfermedades de los astronautas (resumen)](04-enfermedades-resumen.md) · [Informe de fisiopatología espacial](05-informe-fisiopatologia-hrp.md)

> 🔭 **Este documento es la visión a largo plazo**, pensada para un sistema de nivel industrial. **El stack oficial del MVP es Laravel + MySQL + Inertia/Vue + Tailwind CSS + Python (simulador)**, y está justificado en la [propuesta tecnológica](../propuesta/03-propuesta-tecnologica.md). Allí también se explica qué partes de esta visión se conservan y cuáles quedan para etapas posteriores.

## Índice

1. [Idea central](#1-idea-central)
2. [Diagramas de flujo](#2-diagramas-de-flujo)
3. [Qué debe VER la aplicación (pantallas)](#3-qué-debe-ver-la-aplicación-pantallas)
4. [Qué debe HACER la aplicación (funcionalidades)](#4-qué-debe-hacer-la-aplicación-funcionalidades)
5. [Machine Learning: qué modelos y para qué](#5-machine-learning-qué-modelos-y-para-qué)
6. [Stack tecnológico propuesto](#6-stack-tecnológico-propuesto)
7. [Arquitectura de microservicios (a bordo)](#7-arquitectura-de-microservicios-a-bordo)
8. [Modelo de datos (principal)](#8-modelo-de-datos-principal)
9. [Seguridad y privacidad (obligatorio)](#9-seguridad-y-privacidad-obligatorio)
10. [Plan de desarrollo por fases](#10-plan-de-desarrollo-por-fases)
11. [Estructura del repositorio](#11-estructura-del-repositorio)
12. [Escenarios de demo sugeridos](#12-escenarios-de-demo-sugeridos)
13. [Riesgos del proyecto](#13-riesgos-del-proyecto)

---

## 1. Idea central

El sistema **no puede depender de la Tierra**. Debe funcionar en tres capas:

1. **Capa corporal (sensores):** wearables y equipos médicos capturan señales.
2. **Capa a bordo (edge):** un servidor en la nave procesa, guarda, analiza con ML y genera alertas **en tiempo real, sin internet**.
3. **Capa Tierra (control de misión):** recibe los datos por un enlace con retardo (DTN), los médicos de vuelo analizan tendencias y envían recomendaciones.

> Regla de oro: **"Registrar siempre, decidir a bordo, sincronizar cuando se pueda."**

---

## 2. Diagramas de flujo

> Los diagramas están en **Mermaid**. Se ven en GitHub, GitLab, VS Code (extensión *Markdown Preview Mermaid*), Obsidian o en https://mermaid.live.

### 2.1 Arquitectura general

```mermaid
flowchart LR
    subgraph CUERPO["🧑‍🚀 Capa corporal"]
        S1[Camiseta inteligente<br/>ECG, respiración, SpO2, temp]
        S2[Reloj / actígrafo<br/>actividad, sueño, luz]
        S3[Dosímetro personal<br/>radiación]
        S4[Equipos médicos<br/>PA, ultrasonido, OCT, analizador]
        S5[Sensores ambientales<br/>CO2, VOC, ruido, temp]
        S6[App astronauta<br/>cuestionarios, síntomas, pruebas cognitivas]
    end

    subgraph NAVE["🛰️ Capa a bordo (Edge)"]
        GW[Gateway de sensores<br/>BLE / MQTT]
        ING[Ingesta y validación]
        DB[(Base de series de tiempo<br/>+ historia clínica local)]
        ML[Motor de ML<br/>anomalías y riesgo]
        RUL[Motor de reglas clínicas<br/>umbrales]
        ALR[Gestor de alertas]
        CDS[Asistente de decisión médica<br/>protocolos + IA local]
        UIB[Panel a bordo<br/>tripulación / CMO]
        DTNB[Nodo DTN<br/>almacenar y reenviar]
    end

    subgraph TIERRA["🌍 Capa Tierra"]
        DTNT[Nodo DTN Tierra]
        API[API central]
        DW[(Data lake / historia longitudinal)]
        MLT[Entrenamiento de modelos<br/>MLOps]
        DASH[Dashboard médicos de vuelo]
        REP[Reportes e investigación<br/>datos anonimizados]
    end

    S1 & S2 & S3 & S4 & S5 & S6 --> GW --> ING --> DB
    DB --> ML --> ALR
    DB --> RUL --> ALR
    ALR --> UIB
    ALR --> CDS --> UIB
    DB --> DTNB
    ALR -->|prioridad alta| DTNB
    DTNB <-->|enlace espacial<br/>con retardo / cortes| DTNT
    DTNT --> API --> DW
    DW --> DASH
    DW --> MLT -->|modelos nuevos firmados| DTNT
    DW --> REP
    DASH -->|recomendaciones| API
```

### 2.2 Flujo de un dato (del sensor a la pantalla)

```mermaid
flowchart TD
    A[Sensor toma medición] --> B{¿Conexión con gateway?}
    B -- No --> B1[Guarda en memoria del sensor] --> B
    B -- Sí --> C[Envía paquete firmado<br/>BLE → MQTT]
    C --> D[Ingesta: validar formato,<br/>timestamp, calidad de señal]
    D --> E{¿Señal válida?}
    E -- No --> E1[Marcar artefacto<br/>ej. movimiento, electrodo suelto] --> E2[Avisar al astronauta<br/>'reacomoda el sensor']
    E -- Sí --> F[Limpieza y extracción de features<br/>FC, VFC, FR, SpO2...]
    F --> G[(Guardar en BD local)]
    G --> H[Reglas clínicas]
    G --> I[Modelos de ML]
    H & I --> J{¿Hay hallazgo?}
    J -- No --> K[Actualizar paneles y tendencias]
    J -- Sí --> L[Crear alerta con nivel<br/>info / aviso / crítico]
    L --> M[Flujo de alertas 2.3]
    G --> N[Cola de sincronización<br/>por prioridad]
    N --> O[Nodo DTN → Tierra]
```

### 2.3 Flujo de alertas

```mermaid
flowchart TD
    A[Hallazgo detectado] --> B{Nivel}
    B -- INFO --> C[Se registra y aparece<br/>en reporte diario]
    B -- AVISO --> D[Notificación al astronauta<br/>y al CMO a bordo]
    B -- CRÍTICO --> E[Alarma sonora + visual<br/>a toda la tripulación]

    D --> F{¿Confirmado por<br/>segunda medición?}
    F -- No --> F1[Descartar como falso positivo<br/>y guardar feedback para ML]
    F -- Sí --> G[Abrir caso clínico]

    E --> G
    G --> H[Asistente de decisión:<br/>protocolo sugerido + checklist]
    H --> I[CMO ejecuta y registra acciones]
    I --> J[Envío prioritario a Tierra]
    J --> K{¿Hay enlace disponible?}
    K -- Sí, ISS/Luna --> L[Médico de vuelo responde<br/>casi en tiempo real]
    K -- No / retardo Marte --> M[Tripulación actúa autónoma;<br/>Tierra revisa después]
    L & M --> N[Cerrar caso y evaluar resultado]
    N --> O[Retroalimentar modelos]
```

### 2.4 Flujo de Machine Learning (ciclo de vida)

```mermaid
flowchart LR
    subgraph TIERRA
        D1[Datasets públicos<br/>+ análogos + datos de misión] --> P[Preprocesamiento<br/>y etiquetado]
        P --> T[Entrenamiento]
        T --> V[Validación clínica<br/>métricas + revisión médica]
        V --> R[Registro de modelos<br/>versionado]
        R --> X[Exportar a ONNX/TFLite<br/>+ firma digital]
    end
    X -->|paquete DTN| Y
    subgraph NAVE
        Y[Verificar firma] --> Z[Despliegue en modo sombra<br/>compara vs. modelo actual]
        Z --> AA{¿Mejor y estable?}
        AA -- Sí --> AB[Activar modelo]
        AA -- No --> AC[Mantener modelo anterior]
        AB --> AD[Inferencia en tiempo real]
        AD --> AE[Feedback de la tripulación<br/>verdadero/falso positivo]
        AE -->|sync| D1
        AD --> AF[Personalización local<br/>línea base individual]
    end
```

### 2.5 Sincronización DTN (secuencia)

```mermaid
sequenceDiagram
    participant Edge as Servidor a bordo
    participant DTN_N as Nodo DTN nave
    participant DTN_T as Nodo DTN Tierra
    participant API as API Tierra
    participant Dr as Médico de vuelo

    Edge->>DTN_N: Bundle (alerta crítica, prioridad 1)
    Edge->>DTN_N: Bundle (resumen horario, prioridad 2)
    Edge->>DTN_N: Bundle (señal cruda ECG, prioridad 3)
    Note over DTN_N: Sin enlace (conjunción solar)<br/>los bundles se guardan
    DTN_N-->>DTN_T: Ventana de contacto: envía por prioridad
    DTN_T->>API: Entrega bundles
    API->>Dr: Notifica alerta crítica
    Dr->>API: Escribe recomendación
    API->>DTN_T: Bundle de respuesta
    DTN_T-->>DTN_N: Siguiente ventana (~3–22 min de retardo)
    DTN_N->>Edge: Recomendación llega al CMO
```

### 2.6 Flujo de uso por rol

```mermaid
flowchart TD
    L[Login + MFA / tarjeta] --> R{Rol}
    R -- Astronauta --> A1[Mi estado hoy<br/>semáforo] --> A2[Registrar síntomas,<br/>sueño, ánimo] --> A3[Pruebas cognitivas<br/>5 min] --> A4[Ver mis tendencias]
    R -- CMO a bordo --> C1[Panel tripulación] --> C2[Alertas abiertas] --> C3[Protocolos guiados<br/>+ asistente IA] --> C4[Registrar procedimientos]
    R -- Médico de vuelo --> M1[Panel misión] --> M2[Detalle astronauta<br/>tendencias meses] --> M3[Conferencia médica privada] --> M4[Enviar recomendación]
    R -- Ingeniero biomédico --> B1[Estado de sensores<br/>batería, calidad] --> B2[Estado del enlace DTN<br/>colas, latencia]
    R -- Investigador --> I1[Datos anonimizados] --> I2[Exportar datasets]
    R -- Admin --> X1[Usuarios, roles,<br/>umbrales, modelos]
```

---

## 3. Qué debe VER la aplicación (pantallas)

### 3.1 App del astronauta (tablet a bordo)
- **Semáforo de salud del día** (verde/amarillo/rojo) por sistema: corazón, sueño, radiación, hueso/músculo, ojos, mente.
- Signos vitales actuales: FC, SpO2, temperatura, frecuencia respiratoria.
- **Dosis de radiación acumulada** vs. límite de carrera (barra de progreso).
- Sueño de anoche y tendencia semanal.
- Recordatorios: ejercicio, hidratación, toma de muestras, pruebas oculares.
- Formularios rápidos: síntomas, dolor, ánimo, estrés (escalas validadas).
- Pruebas cognitivas cortas (tipo PVT: tiempo de reacción).
- Estado de sus sensores (batería, si están bien puestos).

### 3.2 Panel del Oficial Médico a bordo (CMO)
- Vista de **toda la tripulación** en tarjetas con semáforo.
- **Bandeja de alertas** ordenada por gravedad.
- ECG en vivo y señales en tiempo real del tripulante seleccionado.
- **Protocolos guiados paso a paso** (checklists) para emergencias.
- **Asistente IA local** (preguntas en lenguaje natural sobre protocolos, botiquín, fármacos).
- Inventario médico: fármacos disponibles y fechas de vencimiento.
- Ultrasonido guiado (overlay de instrucciones).

### 3.3 Dashboard Tierra (médicos de vuelo)
- **Mapa de la misión**: estado de cada tripulante y del enlace (latencia actual, próxima ventana).
- **Tendencias longitudinales**: masa corporal, densidad ósea estimada, VFC, sueño, SANS (OCT), Hb.
- Comparación con **línea base personal** previa al vuelo.
- Historial de alertas y decisiones a bordo.
- Módulo de **conferencia médica privada** (notas cifradas).
- Predicciones de riesgo a 7/30 días.
- Reportes PDF automáticos (diario/semanal/post-misión).

### 3.4 Panel técnico (ingeniería biomédica)
- Salud de sensores, firmware, calidad de señal.
- Colas DTN, bundles pendientes, ancho de banda usado.
- Versión de modelos ML activos y su rendimiento.

---

## 4. Qué debe HACER la aplicación (funcionalidades)

| # | Módulo | Qué hace |
|---|---|---|
| 1 | **Ingesta de sensores** | Recibe datos por BLE/MQTT, valida, normaliza unidades y timestamps |
| 2 | **Calidad de señal** | Detecta artefactos (movimiento, electrodo suelto) y avisa |
| 3 | **Procesamiento de señales** | Calcula FC, VFC, FR, detección de latidos, arritmias, estadios de sueño |
| 4 | **Línea base personal** | Aprende el "normal" de cada astronauta antes y durante el vuelo |
| 5 | **Reglas clínicas** | Umbrales configurables por médicos (ej. SpO2 < 92 %) |
| 6 | **Detección de anomalías (ML)** | Detecta desviaciones raras aunque no rompan umbrales |
| 7 | **Predicción de riesgo (ML)** | Estima riesgo a futuro: ortostatismo al aterrizar, fatiga, deterioro del sueño |
| 8 | **Gestor de alertas** | Prioriza, deduplica, escala y registra |
| 9 | **Soporte de decisión clínica** | Protocolos + IA local que sugiere pasos (nunca decide solo) |
| 10 | **Dosimetría** | Acumula dosis, proyecta vs. límite, alerta de evento solar → refugio |
| 11 | **Ambiente** | Correlaciona CO2/VOC con síntomas (cefalea, fatiga) |
| 12 | **Salud mental** | Cuestionarios, pruebas cognitivas, tendencia de ánimo |
| 13 | **Historia clínica (EHR)** | Registro clínico local en estándar FHIR |
| 14 | **Sincronización DTN** | Almacenar-y-reenviar por prioridades, reintentos, sin pérdida |
| 15 | **Seguridad y privacidad** | Cifrado, roles, auditoría, separación clínico/investigación |
| 16 | **Reportes** | Automáticos y exportables (PDF/CSV/FHIR) |
| 17 | **Simulador** | Genera datos realistas de astronautas y escenarios (para desarrollar y probar sin hardware) |
| 18 | **Modo degradado** | Si cae el servidor, la tablet sigue mostrando vitales y alarmas básicas |

---

## 5. Machine Learning: qué modelos y para qué

| Problema | Tipo de modelo | Entrada | Salida | Corre en |
|---|---|---|---|---|
| Detección de arritmias | CNN 1D / ResNet sobre ECG | ECG crudo | Clase de ritmo (normal, FA, extrasístoles...) | A bordo |
| Anomalías multivariables | Isolation Forest / **Autoencoder LSTM** | FC, VFC, FR, SpO2, temp, actividad | Puntaje de anomalía | A bordo |
| Estadios de sueño | Modelo de series (LSTM / Transformer pequeño) | Actigrafía + FC + VFC | Sueño/vigilia, eficiencia | A bordo |
| Estrés y fatiga | Gradient Boosting (XGBoost / LightGBM) | VFC, sueño, PVT, cuestionarios | Índice de estrés/fatiga | A bordo |
| Riesgo de intolerancia ortostática | Regresión logística / XGBoost | Tendencia de VFC, volumen, ejercicio, días de misión | Probabilidad al aterrizar | Tierra + a bordo |
| Pronóstico de tendencias | Prophet / ARIMA / Temporal Fusion Transformer | Series diarias (masa, sueño, VFC) | Proyección a 7/30 días | Tierra |
| Análisis de imagen (SANS) | CNN (transfer learning) | OCT / fondo de ojo | Indicadores de edema | Tierra (luego a bordo) |
| Asistente médico | LLM pequeño local + RAG sobre protocolos | Pregunta del CMO | Respuesta con fuente | A bordo |

**Principios de ML:**
- **Explicable:** cada alerta dice *por qué* (ej. SHAP: "VFC bajó 35 % + sueño 4,5 h").
- **Personalizado:** compara al astronauta consigo mismo, no solo con la población.
- **Ligero:** modelos exportados a **ONNX/TFLite** para correr en hardware limitado.
- **Humano en el bucle:** la IA sugiere, el CMO/médico decide.
- **Modo sombra:** un modelo nuevo primero corre "a escondidas" antes de activarse.

**Datos para entrenar (como no hay datos reales de astronautas):**
- PhysioNet: MIT-BIH Arrhythmia, PTB-XL (ECG), Sleep-EDF, CinC.
- WESAD (estrés con wearables).
- Datos sintéticos del **simulador** del proyecto (escenarios de misión).
- NASA Open Science Data Repository / LSDA (datos públicos y solicitudes).

---

## 6. Stack tecnológico propuesto

### 6.1 Resumen

| Capa | Tecnología propuesta | Por qué |
|---|---|---|
| **Sensores (simulados)** | Python + `bleak` (BLE), ESP32 opcional | Simular sensores reales o usar hardware barato |
| **Mensajería a bordo** | **MQTT** (Eclipse Mosquitto / EMQX) | Ligero, ideal IoT, QoS, funciona offline |
| **Backend a bordo (edge)** | **NestJS (TypeScript)** | Modular, robusto, WebSockets nativos |
| **Servicio de ML / señales** | **Python + FastAPI** | Ecosistema científico (NumPy, SciPy, NeuroKit2, scikit-learn) |
| **Base de series de tiempo** | **PostgreSQL + TimescaleDB** | SQL estándar + series de tiempo eficientes |
| **Historia clínica** | **HL7 FHIR** (HAPI FHIR server o modelo FHIR en Postgres) | Estándar de interoperabilidad médica |
| **Caché / colas internas** | Redis (o NATS) | Alertas en tiempo real, pub/sub |
| **Tiempo real a pantallas** | WebSockets (Socket.IO) | Vitales en vivo |
| **Comunicación espacial** | **DTN: NASA HDTN o ION** (Bundle Protocol v7) | Estándar real de NASA para enlaces con retardo |
| **Simulación de enlace** | `tc netem` (Linux) para simular retardo/cortes | Probar escenario ISS / Luna / Marte |
| **Frontend web (dashboards)** | **React + TypeScript + Vite** | Rápido, ecosistema grande |
| **UI / gráficos** | Tailwind + shadcn/ui, **Apache ECharts** o uPlot (ECG en vivo) | Gráficos rápidos de alta frecuencia |
| **App tablet astronauta** | **Flutter** (o PWA en React) | Multiplataforma, funciona offline |
| **ML entrenamiento** | PyTorch, scikit-learn, XGBoost, NeuroKit2 | Estándar de industria |
| **MLOps** | **MLflow** (registro de modelos), DVC (versionado de datos) | Trazabilidad de modelos |
| **Inferencia ligera** | **ONNX Runtime** / TFLite | Correr modelos en hardware limitado |
| **LLM local (asistente)** | Ollama + modelo pequeño (Llama/Qwen/Phi) + RAG (pgvector) | Funciona sin internet |
| **Backend Tierra** | NestJS + PostgreSQL/TimescaleDB | Mismo stack que a bordo = menos código |
| **Data lake / analítica** | Parquet + DuckDB (o S3 + Athena en AWS) | Análisis longitudinal barato |
| **Monitoreo del sistema** | Prometheus + Grafana | Salud de servicios y colas |
| **Seguridad** | Keycloak (OAuth2/OIDC, roles, MFA), TLS/mTLS, cifrado AES-256, BPSec en DTN | Estándares de seguridad |
| **Contenedores** | **Docker + Docker Compose** (dev) → K3s (Kubernetes ligero) | Igual entorno a bordo y en Tierra |
| **CI/CD** | GitHub Actions | Pruebas y despliegue automático |
| **Cloud (Tierra)** | AWS (EC2/Lightsail, S3, RDS) | Servicios maduros y conocidos por el equipo |

### 6.2 Alternativas

| Si se prefiere... | Cambiar por |
|---|---|
| PHP | Laravel en lugar de NestJS (se pierde algo de tiempo real nativo) |
| Vue | Vue 3 + Vite en lugar de React |
| Menos piezas | MQTT → WebSockets directo; TimescaleDB → solo PostgreSQL |
| Más rendimiento en señales | Rust o Go para el gateway de ingesta |

### 6.3 Por qué separar NestJS y Python
- **NestJS**: usuarios, roles, alertas, API, tiempo real, FHIR, sincronización.
- **Python**: procesamiento de señales y ML (donde están las mejores librerías).
- Se comunican por **MQTT/Redis** o REST interno.

---

## 7. Arquitectura de microservicios (a bordo)

```mermaid
flowchart TB
    subgraph EDGE["Servidor a bordo (Docker / K3s)"]
        GW[sensor-gateway<br/>Python · MQTT]
        SIG[signal-processing<br/>Python · NeuroKit2]
        MLS[ml-inference<br/>FastAPI · ONNX]
        CORE[core-api<br/>NestJS]
        ALERT[alert-service<br/>NestJS]
        CDSS[clinical-assistant<br/>Ollama + RAG]
        SYNC[dtn-sync<br/>HDTN/ION]
        MQ[(Mosquitto MQTT)]
        RD[(Redis)]
        TS[(PostgreSQL + TimescaleDB)]
        KC[Keycloak]
    end
    GW --> MQ --> SIG --> MQ --> MLS --> MQ
    MQ --> CORE --> TS
    MQ --> ALERT --> RD
    CORE <--> KC
    CORE <--> CDSS
    CORE --> SYNC
    ALERT --> SYNC
    WEB[Panel web React] <--> CORE
    TAB[App Flutter] <--> CORE
```

---

## 8. Modelo de datos (principal)

```mermaid
erDiagram
    MISSION ||--o{ CREW_MEMBER : tiene
    CREW_MEMBER ||--o{ DEVICE : usa
    CREW_MEMBER ||--o{ OBSERVATION : genera
    DEVICE ||--o{ OBSERVATION : mide
    CREW_MEMBER ||--o{ BASELINE : tiene
    CREW_MEMBER ||--o{ ALERT : recibe
    ALERT ||--o| CLINICAL_CASE : abre
    CLINICAL_CASE ||--o{ ACTION : registra
    CREW_MEMBER ||--o{ QUESTIONNAIRE_RESPONSE : responde
    CREW_MEMBER ||--o{ RADIATION_DOSE : acumula
    ML_MODEL ||--o{ ALERT : produce
    SYNC_BUNDLE }o--|| MISSION : pertenece

    OBSERVATION {
      uuid id
      timestamptz time
      string code "LOINC (ej. FC, SpO2)"
      float value
      string unit
      float quality
    }
    ALERT {
      uuid id
      string level "info/aviso/critico"
      string source "regla o modelo"
      json explanation
      string status
    }
```

**Equivalencias FHIR:** `CREW_MEMBER → Patient`, `OBSERVATION → Observation`, `DEVICE → Device`, `ALERT → Flag/DetectedIssue`, `CLINICAL_CASE → Encounter`, `QUESTIONNAIRE_RESPONSE → QuestionnaireResponse`.

---

## 9. Seguridad y privacidad (obligatorio)

- **Autenticación:** Keycloak con MFA; roles: astronauta, CMO, médico de vuelo, BME, investigador, admin.
- **Autorización:** cada astronauta ve solo lo suyo; investigadores solo datos **anonimizados**.
- **Cifrado:** TLS/mTLS entre servicios, AES-256 en reposo, **BPSec** en los bundles DTN.
- **Auditoría:** log inmutable de quién vio o cambió qué.
- **Firma de modelos y actualizaciones:** nada se instala a bordo sin firma verificada.
- **Separación de datos:** clínicos vs. operativos vs. investigación (como NASA LSAH vs. LSDA).

---

## 10. Plan de desarrollo por fases

| Fase | Entregable | Duración aprox. |
|---|---|---|
| **0. Diseño** | Requisitos, casos de uso, este documento, mockups | 2 semanas |
| **1. Simulador** | Generador de datos de 4–6 astronautas (normal + escenarios: arritmia, mal sueño, evento solar, CO2 alto) publicando por MQTT | 2 semanas |
| **2. MVP a bordo** | Ingesta → TimescaleDB → reglas → alertas → panel React en vivo | 3–4 semanas |
| **3. ML v1** | Anomalías (Isolation Forest/autoencoder) + arritmias (CNN con MIT-BIH) + sueño | 3–4 semanas |
| **4. Tierra + DTN** | Backend Tierra, sincronización con HDTN/ION, simulación de retardo ISS/Luna/Marte con `netem` | 3 semanas |
| **5. App astronauta** | Flutter: semáforo, cuestionarios, PVT, modo offline | 2–3 semanas |
| **6. Asistente IA** | LLM local + RAG sobre protocolos médicos | 2 semanas |
| **7. Seguridad + pruebas** | Keycloak, cifrado, auditoría, pruebas de carga y de cortes de enlace | 2 semanas |
| **8. Demo final** | Escenario completo: "emergencia durante conjunción solar en Marte" | 1 semana |

**MVP mínimo para presentar:** simulador + edge (ingesta, reglas, 1 modelo ML, alertas) + dashboard + sincronización con retardo simulado.

---

## 11. Estructura del repositorio

```
astrohealth/
├── docker-compose.yml
├── docs/                    # este .md, informe Fase 1, diagramas
├── simulator/               # Python: generador de astronautas y escenarios
├── edge/
│   ├── sensor-gateway/      # Python
│   ├── signal-processing/   # Python + NeuroKit2
│   ├── ml-inference/        # FastAPI + ONNX Runtime
│   ├── core-api/            # NestJS (auth, FHIR, alertas, websockets)
│   ├── clinical-assistant/  # Ollama + RAG
│   └── dtn-sync/            # config HDTN/ION
├── ground/
│   ├── ground-api/          # NestJS
│   └── analytics/           # DuckDB / notebooks
├── ml/
│   ├── notebooks/
│   ├── training/            # PyTorch, scikit-learn, XGBoost
│   └── mlflow/
├── web-dashboard/           # React + TS + Vite
├── crew-app/                # Flutter
└── infra/                   # Keycloak, Mosquitto, Prometheus, Grafana, netem
```

---

## 12. Escenarios de demo sugeridos

1. **Arritmia en ejercicio:** el modelo detecta FA en el ECG → alerta crítica → protocolo guiado.
2. **Evento solar:** sube la tasa de dosis → alerta a toda la tripulación → "ir al refugio".
3. **CO2 alto + cefaleas:** el sistema correlaciona y recomienda ventilación.
4. **Fatiga acumulada:** 5 noches de mal sueño + PVT lento → aviso al CMO.
5. **Conjunción solar en Marte:** 14 días sin enlace → el sistema sigue operando y sincroniza todo al volver.

---

## 13. Riesgos del proyecto

- **No hay datos reales de astronautas:** usar datasets públicos + simulador; dejarlo explícito.
- **Falsos positivos:** generan "fatiga de alarmas"; por eso doble confirmación y umbrales ajustables.
- **Validación clínica:** el sistema es de apoyo, no un dispositivo médico certificado.
- **Hardware limitado a bordo:** modelos pequeños y optimizados (ONNX/TFLite).

