# Registro de cambios de la documentación

> [← Volver al inicio](README.md)

## 2026-10-19 · Repositorio en GitHub y ramas

- Repositorio: [HACKATHON-NASA/ARTEMIS-III](https://github.com/HACKATHON-NASA/ARTEMIS-III), con las ramas `main` (estable), `testing` (pruebas de fase) y `development` (**rama de trabajo diaria**).
- [Forma de trabajo](modulos/00-arquitectura/07-forma-de-trabajo.md#ramas): las ramas de historia salen de `development` y vuelven a `development` por *pull request*; `development` → `testing` al cerrar cada fase y `testing` → `main` cuando las pruebas pasan. URL real del repositorio en la [guía del entorno local](modulos/00-arquitectura/08-entorno-local.md).

## 2026-10-19 · Guía del entorno local y proyecto creado

- Proyecto creado en `astrohealth/`: Laravel 13, Vue 3, Inertia 3, Tailwind v4 y TypeScript, con PHP 8.4, Node 22 y MySQL. `.env.example` con los valores del proyecto (`AstroHealth`, `APP_NODO`, `es`, MySQL, sesión de 8 h); `vite-plus` fijo en 0.3.0.
- Nueva guía [Preparar el entorno local](modulos/00-arquitectura/08-entorno-local.md): versiones obligatorias, instalación paso a paso con verificación, el día a día y los errores frecuentes con su solución. Enlazada desde la guía y desde el README del repositorio.

## 2026-10-19 · Versiones del cPanel

- cPanel: **PHP 8.4**; todo el equipo usa PHP 8.4 (Laravel 13). Base de datos: el MySQL de XAMPP en local y el del cPanel en los nodos, con la conexión `mysql` de Laravel y bases en `utf8mb4_unicode_ci`. Anotado en [HU-00.01](scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md) y en las [convenciones de base de datos](modulos/README.md#7-convenciones-de-base-de-datos).

## 2026-10-19 · Repositorio ArtemisIII, aplicación astrohealth

- El repositorio se llama **ArtemisIII** y su raíz es la carpeta del proyecto: `documentacion/`, `astrohealth/` (la aplicación Laravel + Vue, antes `aplicacion/`) y `simulador/`. `interno/` queda en `.gitignore`.
- Rutas actualizadas: `aplicacion/app/Modules/` → `astrohealth/app/Modules/` en las fichas, la norma, HU-00.01 y la propuesta; árbol del repositorio con raíz `ArtemisIII/`; `git clone … ArtemisIII` y `cd ArtemisIII/astrohealth` en la forma de trabajo.

## 2026-10-19 · Un repositorio con tres carpetas

- La documentación se mudó a `astrohealth/documentacion/`. El repositorio queda con tres carpetas: `documentacion/` (todo lo escrito hasta hoy), `aplicacion/` (Laravel + Vue, con su `docs/` técnico) y `simulador/` (Python). `interno/` queda fuera del repositorio.
- Rutas actualizadas en las fichas, la norma, HU-00.01, el plan y la propuesta: `backend/app/Modules/` → `aplicacion/app/Modules/` y `simulator/` → `simulador/`.
- Nuevos `README.md` y `.gitignore` en la raíz del repositorio y `simulador/README.md`.

## 2026-10-19 · ARCHITECTURE.md: norma arquitectónica obligatoria

- Nueva [ARCHITECTURE.md](modulos/00-arquitectura/ARCHITECTURE.md) en la guía de arquitectura: objetivo, principios, decisiones de adaptación, estructura general y de un módulo, las 4 capas (Domain, Repository = `Infrastructure/`, Service = `Application/`, API = `Http/`), reglas de dependencia, de repositorio, de servicio y de dominio, HTTP, estándar JSON, códigos funcionales, seguridad, Vue e Inertia, frontend modular, nombres, límites de tamaño, pruebas (con prueba de arquitectura en Pest), rendimiento, logging, transacciones, comunicación entre módulos, prácticas prohibidas, excepciones documentadas y dos ejemplos de flujo (crear un usuario y recibir un lote de lecturas). Incluye la regla `ARCHITECTURE VIOLATION` para personas y agentes de IA.
- **Sobre JSON en toda la API:** `success`, `code`, `message`, `data`, `errors` y `meta` (`traza_id`, `modulo`, `paginacion`, `debug` solo en local). Lo que las fichas llaman "cuerpo de la respuesta" va en `data`. Códigos funcionales en mayúsculas con prefijo del módulo (`SINTOMAS_REGISTRADO`, `INGESTA_LOTE_MIXTO`). Reemplaza el formato plano de la guía anterior; [API, JSON y errores](modulos/00-arquitectura/04-api-y-errores.md) reescrito.
- **Repositorios** separados en lectura y escritura por agregado; **transacciones** con la abstracción `Transaccion` (M03 HU-03.01 actualizada).
- **Frontend modular:** `resources/js/modules/<Modulo>/{Pages,Components,Composables,Types,Utils}`, con un resolver de páginas que conserva los nombres de página de las fichas. Rutas de archivos actualizadas en las fichas de frontend de los módulos (57 líneas), en [módulos §3](modulos/README.md#3-estructura-interna-de-cada-módulo) y en el [plan §9.2](scrum/plan-de-sprints.md#92-reglas-de-datos); archivos de la cola offline en `.ts`.
- [HU-00.01](scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md) monta la base común (`RespuestaApi`, `CodigoGeneral`, `Transaccion`, `ArquitecturaTest`, `max-lines` de ESLint) y la estructura modular del frontend. El [plan §10](scrum/plan-de-sprints.md#10-cómo-trabajar-con-ia-sin-introducir-errores) y la [forma de trabajo](modulos/00-arquitectura/07-forma-de-trabajo.md) piden la norma en cada solicitud a la IA, en la plantilla del *pull request* y en la definición de terminado.

## 2026-10-19 · Guía de arquitectura y plan en fases cortas

- **Nueva [guía del proyecto y de la arquitectura](modulos/00-arquitectura/README.md)** en `modulos/00-arquitectura/`, punto de entrada para el equipo: el proyecto (objetivo, usuarios, escenarios, fechas y qué es "ganar"), la arquitectura (módulos, capas, puertas web y API, nodos), el manual de backend por capas con un ejemplo completo, el formato de API, JSON y errores, el manual de Vue e Inertia, el trabajo con migraciones y la forma de trabajo (Jira, Git, *pull requests*, pruebas cruzadas y definición de terminado).
- **Formato común de errores de la API:** `message`, `codigo`, `modulo`, `traza_id` y `errors` (más `debug` solo en local); errores de negocio como excepciones que heredan de `ErrorDeNegocio`; `traza_id` en el encabezado `X-Traza-Id`, en el *log* y en las páginas de error. Respuestas de un solo recurso sin envoltorio `data` (como ya estaban especificadas en los módulos).
- [HU-00.01](scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md) monta esa base (`AsignarTrazaId`, `ErrorDeNegocio`, manejador común, páginas de error, `withoutWrapping` y Scramble para documentar la API) y corrige la carga de rutas: el `routes.php` de cada módulo se carga sin middleware y declara sus grupos web y API, para que la API no herede la sesión ni el CSRF.
- **[Plan de sprints](scrum/plan-de-sprints.md) comprimido:** desarrollo del 20 de octubre al 10 de noviembre en 5 fases cortas (20–21 oct, 22–25 oct, 26–31 oct, 1–5 nov y 6–10 nov), estabilización del 11 al 13 (congelación el miércoles 11, ensayo el viernes 13). Nueva sección de ritmo de trabajo (qué ceremonias se hacen y cuáles se saltan) y dos revisiones de velocidad (25 y 31 de octubre). Fechas actualizadas también en [05](docs/propuesta/05-estructura-scrum.md) y en el [README](README.md).
- Nuevo [índice de la carpeta scrum](scrum/README.md).

## 2026-10-19 · HU-00.01 y HU-00.03 detalladas

- Nueva carpeta [scrum/fundaciones](scrum/fundaciones/README.md) con las historias de EP-00. Hasta ahora HU-00.01 y HU-00.03 solo tenían una línea en el plan.
- [HU-00.01](scrum/fundaciones/HU-00.01-repositorio-y-estructura-de-modulos.md): estructura del repositorio, carga de módulos sin paquetes externos (*provider* base, rutas, migraciones y pruebas por módulo), configuración común (`APP_NODO`, UTC, `es`, cola `database`), Vitest y decisiones del *starter kit* (carpetas en minúscula del *kit*, TypeScript, componentes de shadcn-vue). Sanctum se queda en HU-01.01.
- [HU-00.03](scrum/fundaciones/HU-00.03-libreria-de-componentes.md): los 6 componentes compartidos con sus *props* (`EstadoVacio`, `Cargando`, `Semaforo`, `TarjetaSigno`, `InsigniaAlerta` y `GraficaSerie`, que cubre las gráficas de M04, M05, M07, M08, M09 y M10), las variables de color, los tipos `Sistema` y `Color` (antes sin definir), las funciones de formato y un catálogo `/dev/componentes` solo en local.
- Plan, Sprint 0: el indicador de sincronización sale de los entregables de HU-00.03 (es de M15, HU-15.04).
- [Convenciones del frontend](modulos/README.md#convenciones-del-frontend): `flash` incluye `descargarUrl` (M14).

## 2026-10-19 · M01 listo para empezar a construir

- **Sanctum se instala en HU-01.01**, con `tokenable_id` de 36 caracteres y `sanctum.expiration = null` desde la primera migración. Se quitan el truco `Schema::hasTable('personal_access_tokens')` de HU-01.02 (su propio criterio necesitaba la tabla) y la migración de corrección de HU-01.03.
- **HU-01.04 va después de HU-02.02** (usa `ConsultaTripulantes` para la regla de `ver`, el filtro y los criterios) y antes de HU-01.03 (que audita los tokens). Se reordenó el Sprint 1 del [plan](scrum/plan-de-sprints.md#4-sprint-1--los-datos-fluyen) y se actualizó la dependencia en las tres capas.
- `GatesIdentidad::que_cumple` pasa a `queCumple` (convención de PHP) y aparece en el árbol de archivos; usuarios inactivos y `sistema` se resuelven con un solo `Gate::before()`.
- `HandleInertiaRequests` comparte también `flash.descargarUrl` (M14), que el frontend ya esperaba.
- [Backend §3.2](modulos/M01-identidad/backend/README.md#32-inicio-de-sesión-por-nodo): el rechazo por nodo lo hace el inicio de sesión con `Rol::puedeIniciarSesionEn()`; el middleware `RolPermitidoEnNodo` solo cierra sesiones abiertas. Mensajes exactos como en la historia.

## 2026-10-19 · Todo el backend pasa a Julieth

- **Backend y base de datos de las 61 historias de M01–M16** (también las de M14, que tenía Miguel): responsable **Julieth**. Se cambió en la tabla de cada historia de `backend/HU` y `database/HU`, en la frase "Lo hace…" de cada README de capa, en la fila *Responsables* de cada ficha de módulo, en el [índice de módulos](modulos/README.md#1-índice-de-módulos), en la columna *Backend* del [plan de sprints](scrum/plan-de-sprints.md) y en los roles de [05 · Estructura Scrum](docs/propuesta/05-estructura-scrum.md).
- **Santiago** queda como *Product Owner* y despliegue: solo conserva HU-00.01, HU-00.02 y HU-00.04 (repositorio, CI y cPanel, Sprint 0).
- **Miguel** deja los reportes de M14 (la columna *Otros* de HU-14.01 a HU-14.03 queda en "—"); conserva el simulador, las fuentes de los protocolos (HU-13.04), el QA, la documentación y la demo.
- **Frontend sin cambios:** Jhon y Gabriela, como estaba.
- ⚠️ **Carga:** Julieth pasa de 24 a 61 historias de backend (23 en el Sprint 2 y 18 en el Sprint 3). Pendiente de decidir en el equipo cómo se absorbe (ver el [plan de contingencia](scrum/plan-de-sprints.md#11-plan-de-contingencia)).

## 2026-10-18 · Pasada general: valores de M16 nombrados en todas las capas

Revisión de todas las fichas e historias (M01 a M15) buscando números con unidad clínica u operativa escritos sin el parámetro de M16 que los define. Los ejemplos de pantalla, los datos del simulador, los volúmenes y los límites técnicos (tamaños de lote, reintentos, cachés, sondeos, sesión y tokens) se dejan como están. Se nombra el parámetro (o la fila de umbral) en:

- **M03:** los 5 min sin artefactos para volver a `ok` (`colocacion_ventana_min`).
- **M04:** el valor "actual" de 10 min y la PA de 24 h (`vital_reciente_min`, `pa_reciente_h`); el criterio ortostático 20/10 mmHg (filas `PA_ORTO_SIS` y `PA_ORTO_DIA`).
- **M05:** los criterios de racha, z y lapsos (`cog_*`), el contexto de 4 noches (`contexto_sueno_noches`), la pendiente de 7 días (`cog_pendiente_dias`), la duración del PVT (`pvt_duracion_s`) y las sesiones sin z (`cog_sesiones_*`).
- **M06:** la regularidad de 7 noches con un mínimo de 3 (`sueno_regularidad_noches`, `sueno_regularidad_min_noches`).
- **M07, M11:** los tamaños máximos de PDF y foto (`adjunto_pdf_max_kb`, `adjunto_foto_max_kb`).
- **M08:** la intensidad y la ventana de la cefalea (`co2_cefalea_*`), el límite y la persistencia de `CO2_ALTO`, y las líneas de referencia de las gráficas (`referenciasPco2`).
- **M09:** el hueco máximo de 5 min, la tasa media de 7 días, la ventana, el mínimo de lecturas y la racha del evento solar, y las referencias de 600 y 250 (`dosis_*`, `spe_*`, `limite_carrera_msv`, `limite_evento_msv`).
- **M10:** la meta de 840 min y el 5 % de `MASA_PERDIDA`.
- **M11:** los días del cuestionario de mareo (`diasTotal`).
- **M12:** los 10 min posteriores al ejercicio (`recuperacion_fc_min`).
- **M13:** los 30 min de la confirmación automática (`alerta_confirmacion_min`).
- **M14:** las pendientes de 30 días (`tendencia_dias_larga`).
- **M15:** los 4 MB por petición y los 14 días de la demo de E5 (`sync_max_bytes_peticion`, `conjuncion_dias`).

## 2026-10-18 · M16: capas alineadas

- **Botón "Ajuste temporal" sin datos para decidir:** F2 solo debe activarlo durante un apagón, pero `Cmo/Umbrales/Index` no recibía el estado del enlace; ahora recibe `apagon` (la misma condición que valida el backend). La ruta del ajuste (`POST /cmo/umbrales/{regla}/ajuste`) no estaba escrita en ninguna capa.
- **"Era 92 → 91":** los ajustes pendientes (en F2 y en F3) se muestran junto al valor de la Tierra, que no estaba en las *props*; se añade `tierra`.
- **Tipo `Umbral` del frontend:** faltaban `evaluador` (para "Regla especializada"), `descripcion` (la vista previa de `DescripcionUmbral` del backend), `parametro_diseno` (la marca †, que sale de la `fuente`) y el nombre y la unidad de la variable; `variable_codigo` y `modificado_por` pueden ser `null` (filas sembradas).
- **Edición de un umbral:** la página recibía `operadores` y `contextos`, que no se pueden cambiar, y no recibía el rango permitido de cada valor que muestra como ayuda ("entre 20 y 250 lpm"); ahora recibe `limites`.
- **Catálogos y parámetros:** las *props* eran `…[]`. Se definen los campos, `editable` (solo lectura en la nave), `tiene_resultados` (unidad bloqueada; M07 ofrece `ConsultaResultados::hayDePrueba()`), y en los parámetros `categoria`, `bloqueado` (🔒 desde el día 1), la misión y qué grupo puede editar cada usuario.
- **Fichas de backend y base de datos:** contratos con `misionId` y `CatalogoPruebas::activas()`, índices y unicidad con `mision_id`, columna `activo` de `catalogo_pruebas`, y los archivos (`ValoresPorDefecto`, `InicializarConfiguracion`, `DescripcionUmbral`, *Form Requests* e importador).

## 2026-10-18 · M15: capas alineadas

- **Panel del enlace en la Tierra:** F3 mostraba la misma pantalla que la nave (ventana sin enlace, "último ciclo" y aviso del cron), pero en la Tierra no corre el ciclo y `ventanas_enlace` solo existe en la nave. Allí el panel muestra `ultimo_contacto_en` (la última petición de la nave) y los pendientes de su cola sin hora estimada.
- **Cupo y hora estimada:** el frontend tenía `cupo: number` y una sola hora por prioridad; el cupo es en bytes y en paquetes (`cupo: { bytes, paquetes }`) y la pantalla muestra un intervalo (`estimado_desde`, `estimado_hasta`). La fórmula de §3.6 usaba el cupo antiguo, por paquetes.
- **API de salientes:** la ficha de backend tenía `?limite=N`; la historia usa `?max_bytes=N&max_paquetes=M`.
- **Recomendaciones:** faltaban la ruta y las *props* del formulario de la Tierra (`enlace.perfil` y `retardo_min` para "tarda unos 12 min"), `autor` en la lista del CMO, el tipo de la *prop* compartida `recomendacionUrgente` y `mensajesMedico` en `Astronauta/Inicio` (ficha de M04). La hora estimada vuelve en `flash.exito`.
- **Cola offline:** la ficha de backend no tenía los campos `metodo`, `multipart` y `siguiente_intento_en` de `cola_envios`, ni los `403` y `413` que también van a `rechazados`.
- **Fichas:** permisos con su Gate (`ver-enlace` también para `admin` en la página del enlace), comandos y casos de uso del empaquetador y del importador de recibidos, `ConsultaRecomendaciones`, columna `comprimido` y el índice de importación en orden; los 4 MB por petición, los 14 días de la conjunción y la hora del reporte diario apuntan a sus parámetros de M16.

## 2026-10-18 · M14: capas alineadas

- **Lista de reportes y exportaciones:** el tipo `Reporte` del frontend no tenía `estado`, `error`, `bytes`, `parametros` ni `nodo_origen`, que las pantallas usan ("↻ Generando…", "✖ Error", "Descargar PDF (182 KB)", "D10–D16 · FC, SpO₂", "Automático (nave)"). El backend define ahora los campos de cada lista.
- **Descarga al terminar de generar:** el frontend descarga el PDF al terminar, pero el backend no decía qué devolvía el `POST`. Responde con `flash.descargarUrl`, que se añade al tipo `flash` de M01.
- **Columnas del CSV:** la ficha de backend ponía `sexo,grupo_edad` siempre; la historia (y la pantalla) los dejan opcionales y desmarcados por defecto.
- **Hora del reporte automático:** las 06:00 UTC pasan a ser el parámetro operativo `reporte_diario_hora_utc` de M16. El máximo de 7 días por exportación es un límite técnico (cPanel) y llega al formulario como `maxDias`, sin repetirlo.
- **Fichas:** Gate `exportar-anonimizados`, `ReporteRequest`, el importador del PDF en la Tierra, `ParametrosMision` en los contratos y los índices reales de `reportes_generados`.

## 2026-10-18 · M13: capas alineadas

- **Dónde se editan los protocolos:** M13 decía que el admin los edita en la nave y que viajan a la Tierra en el paquete `configuracion`, pero ese paquete va de la Tierra a la nave (M15). Se editan **en la Tierra**, donde está el médico de vuelo que los valida (`SoloEnNodo:tierra` en las escrituras); en la nave la página es de solo lectura (`editable`) y los casos abiertos siguen con su copia.
- **Alarma crítica:** la *prop* `alertasCriticas` de la ficha de frontend no tenía `nivel_cambiado_en`, que la historia usa para que la alarma vuelva a sonar si la alerta escala.
- **Insignia de alertas (menú de F2 y cabecera de F1):** no había *prop* de la que sacar "Alertas · 3"; el middleware comparte ahora `resumenAlertas` (`activas`, `nivel_maximo`).
- **`Cmo/Alertas/Show`:** la ficha de frontend no tenía `recomendaciones`, `ocurrenciasRecientes`, `motivo_descarte`, `nota_cierre` ni el estado del caso; el texto "Se confirmará sola si se repite en 30 min" usa `confirmacionMin` (`alerta_confirmacion_min` de M16). Falta la acción "Cerrar sin caso" en la tabla de acciones. Para leer las recomendaciones de una alerta, M15 ofrece `ConsultaRecomendaciones::deAlerta()`.
- **`Cmo/Casos/Show`:** las acciones llevan `id`, `tipo` (para destacar las recomendaciones) y `autor` (no `realizada_por`); el protocolo sale de la copia del caso, que no guarda `validado_por`, y `fuente` puede ser `null` antes de HU-13.04.
- **`Tierra/Alertas/Index`:** el backend define `decision` y los campos de `descartesPorRegla`; la ventana de los descartes es `falsos_positivos_dias` (M16).
- **Fichas de backend y base de datos:** contratos con sus parámetros, `CerrarAlerta`, importadores, migraciones `create_alerta_hallazgos` y `add_caso_fk_to_alertas`, y las columnas `actualizado_en`, `created_at` y `cerrado_en` nula que ya estaban en las historias.

## 2026-10-18 · M12: capas alineadas

- **Solicitudes de recálculo en F3:** el backend envía `solicitudes` y la página muestra su estado (pendiente con la hora estimada de llegada, completada o rechazada con su motivo), pero la ficha de frontend no tenía esa *prop*. Se define con sus campos.
- **Botón "Recalcular":** la página no tenía cómo saber qué líneas base son cognitivas (no se recalculan); se añade `recalculable`, y la `unidad` y el `contexto` que muestran la tabla y el historial.
- **Mínimos estadísticos como parámetros de misión:** `linea_base_min_lecturas` (10), `linea_base_min_noches` (5), `tendencia_min_puntos` (4) y `tendencia_p_max` (0,05, umbral de "tendencia significativa"), que estaban fijos en las historias.

## 2026-10-18 · M11: capas alineadas

- **Tarjeta del cuestionario de mareo en el inicio de F1:** `mareoPendiente` no estaba en las *props* de `Astronauta/Inicio` (ficha de M04) y la tarjeta decía "día 2 de 3" con el 3 fijo; ahora lleva `diasTotal` (`mareo_dias` de M16) y `diaMision` deja de ser `1 | 2 | 3`.
- **Tamaño máximo de la foto:** el frontend comprimía hasta 500 KB fijos; ahora recibe `fotoMaxKb` (`adjunto_foto_max_kb` de M16), el mismo límite que valida el servidor.
- **Vistas de la tripulación:** la ficha de frontend no tenía el `nombre` del síntoma ni el tamaño de la foto (`ancho`, `alto`) que envía el backend, ni el archivo `Tierra/Sintomas/Show.vue`. La ruta de descarga de la foto (`GET /sintomas/fotos/{id}`) se añade a la ficha de backend.

## 2026-10-18 · M10: capas alineadas

- **Líneas de referencia sin números fijos:** la meta semanal de ejercicio (840 min), la línea del −5 % de la masa y las líneas de 70 y 180 mg/dL de la glucosa estaban fijas en la ficha de frontend (`metaSemanal: 840`) y en las historias. Ahora llegan como *props* desde M16 (`ejercicio_meta_semanal_min`, filas `MASA_PERDIDA` y `GLUCOSA`): `metaSemanal`, `umbralMasaPct` y `limitesGlucosa`, también en `Tierra/Fisico/Show`, que dibuja las tres líneas.
- **Ficha de frontend:** `Tierra/Fisico/Show` admite `null` en las series de glucosa antes de HU-10.05, como dice el backend.

## 2026-10-18 · M09: capas alineadas

- **Panel del evento solar terminado:** necesita la hora de fin (`hasta`), que el backend envía pero la ficha de frontend no tenía.
- **Gráfica de 30 días:** marca los días con `minutos_sin_dato`, que el backend envía en `diarias30` y la ficha de frontend no tenía.
- **Sin números fijos en la pantalla:** el límite de carrera (600), la marca del 80 % de la barra y la referencia de 30 días (250) llegan del backend (`limite_carrera_msv`, fila `DOSIS_PROYECCION`, `limite_evento_msv` de M16); la ficha de frontend los tenía como constantes y el backend escribía `referencia_30d: 250`.
- **Proyección:** nombres de campos fijados (`total_msv`, `pct`, `fecha_fin`, `tasa_media_7d`) y `eventoSolar` añadido a las *props* de `Astronauta/Inicio` (ficha de M04).

## 2026-10-18 · M08: capas alineadas

- **Estado de cada módulo en F2:** la tarjeta decide "Normal" o "Sobre el límite" con el promedio de la última hora, y el backend lo envía en `estado` (`promedio_ultima_hora`, `sobre_limite`), pero la ficha de frontend no tenía ese campo: la página solo podía usar la última lectura, que es justo lo que la historia prohíbe.
- **Referencias de pCO₂ sin números fijos:** el límite sale de la fila `CO2_ALTO` de M16 y el objetivo de 7 días del parámetro nuevo `co2_objetivo_7d_mmhg` (2,5, Fase 1 §3.2); la ficha de frontend los tenía como constantes (`3.0`, `2.5`) y la tarjeta decía "(3,0)" fijo.
- **Contrato `ConsultaAmbiente` completo en la ficha de backend** (`promediosHorarios` y `estadoModulo`, que ya estaban en HU-08.01).

## 2026-10-18 · M07: capas alineadas

- **Props de exámenes alineadas con el backend:** `series` usa `interpretacion_vigente` (la ficha de frontend decía `interpretacion`); se añaden `tendencias` (recta de la gráfica) y `fechasMision` (línea de lanzamiento); `rangos` lleva `nombre` y `direccion_desfavorable` (sin ella la marca ↘/↗ de tendencia no se podía elegir); `recordatorios` tiene el estado `hoy` y el `codigo` (para el enlace con la prueba preseleccionada); el formulario recibe `fechaMaxima`; la vista previa del CSV marca las correcciones (`corrige`).
- **Recordatorios en el inicio de F1:** `recordatoriosExamenes` añadido a las *props* de `Astronauta/Inicio` (ficha de M04).
- **Pruebas desactivadas:** el formulario y la API solo admiten pruebas **activas** del catálogo (M16 permite desactivarlas).
- **Tamaño máximo del PDF** enviado al frontend (`pdfMaxKb`, de `adjunto_pdf_max_kb`), en lugar de los 2 MB fijos en la página; los 7 días de "próximo" pasan a ser el parámetro `examen_aviso_dias`.

## 2026-10-18 · M06: capas alineadas

- **Props de sueño completas en la ficha de frontend:** `umbrales` (la línea de "6 h (umbral)" y la marca de noche mala), `horasProgramadas`, `medicacion_nombre` ("Sí: Melatonina") y la eficiencia de `yaExiste` ("6,4 h (90 %)") los usaban las historias y los enviaba el backend, pero la ficha no los tenía. `anoche` admite duración nula (registro manual solo de medicación) y `medicacion` nula ("no indicada").
- **Hora de despertar de la agenda** como parámetro de misión (`sueno_despertar_utc`, 07:00), en lugar de fija en el código.
- **"Es otra noche":** `yaExiste` solo describe la noche sugerida; para otra noche se muestra el formulario completo y el servidor fusiona.

## 2026-10-18 · M05: capas alineadas

- **Escalas semanales repetidas los lunes:** `semanalPendiente` valía `true` "si es lunes o si faltan esa semana", así que un lunes la página volvía a pedir carga de trabajo y cohesión aunque ya estuvieran respondidas. Ahora solo depende de si faltan en la semana ISO actual.
- **Evolución psicológica (F3):** el backend envía `desviacion_efectiva` y la lista `alertas`, pero la ficha de frontend esperaba `desviacion` y no tenía `alertas` (la página las muestra arriba). Alineado.
- **API de sesiones:** el cuerpo de la ficha de backend no tenía `sin_respuesta` en los ítems (sí la historia y la tabla).
- **Parámetros de misión** `test_atraso_min` (60), `contexto_escalas_dias` (3) y `contexto_rmssd_h` (24) en M16.

## 2026-10-18 · M04: capas alineadas

- **La presión arterial siempre habría salido "Sin datos recientes":** el valor actual exigía una lectura de los últimos 10 minutos, pero la PA se toma una vez al día (07:30). Ahora la PA es reciente durante 24 h (`pa_reciente_h`) y la tarjeta muestra "hoy 07:30"; el resto de códigos sigue con 10 minutos (`vital_reciente_min`).
- **La tarjeta de actividad no tenía datos:** `Astronauta/Vitales/Index` muestra 7 tarjetas (con la PA junta y la actividad), pero `ultimos` no traía `AH-ACTIVIDAD`. Se añade.
- **Props que el frontend usaba y el backend no enviaba (o al revés):** `reciente` en cada valor, las *props* de `Astronauta/Inicio`, `umbrales` de la prueba ortostática y de las páginas de ECG, `modalidad` de las pruebas y `contexto`/`duracion_s` de las ventanas, `nivelMaximo` de las alertas de cada tarjeta de F2 y métricas de ECG que pueden ser `null`.
- **Parámetros de misión** `vital_reciente_min` (10), `pa_reciente_h` (24) y `flecha_tendencia_pct` (5) en M16.

## 2026-10-18 · M03: capas alineadas y aviso de "sin datos"

- **"Sin datos" solo en los dispositivos de envío continuo:** el estado se calculaba igual para todos con 10 minutos sin contacto, pero el tensiómetro envía una vez al día, el medidor de masa una vez por semana y el actígrafo no envía lotes (el sueño entra por M06). F1 habría mostrado siempre "3 sensores sin datos". Ahora solo pueden estar `sin_datos` el wearable, el oxímetro, el dosímetro, el CGM y los monitores ambientales.
- **En la Tierra no hay estado de sensores** (`EstadoSensores` devuelve `[]`): el último contacto no se sincroniza y todos habrían salido "sin datos".
- **La ventana de 5 minutos se calcula en PHP y en UTC**, también en MySQL: `NOW()` usa la zona horaria de la sesión de MySQL, que en cPanel puede no ser UTC.
- **Parámetros de misión** `sensor_sin_datos_min` (10), `colocacion_artefactos_min` (3) y `colocacion_ventana_min` (5) en M16. La ficha de frontend ya no dice que `EstadoSensores.vue` pinta la columna de M02 (lo hace `EstadoDispositivo.vue`).

## 2026-10-18 · M02: varias misiones y capas alineadas

- **Empezar otra misión:** con la configuración por misión (M16 §3.6) hacía falta poder crear una segunda misión, pero `POST /admin/mision` la rechazaba siempre que hubiera una activa. Ahora, con `cerrar_actual: true` y un diálogo de confirmación, la actual queda cerrada (solo lectura, con sus datos y su configuración) y la nueva empieza sin tripulantes ni módulos y con la configuración por defecto.
- **El mismo usuario en otra misión:** `tripulantes.user_id` era único en toda la tabla, así que el astronauta de la misión anterior no se podía vincular en la nueva. Ahora es única `(mision_id, user_id)` y `porUsuario()` busca en la misión activa.
- **Dispositivos de la misión anterior:** conservan su asignación y M03 ya rechaza sus lotes (el tripulante no es de la misión activa); la página los marca con "⚠ Misión anterior" (`de_mision_activa`) para reasignarlos.

## 2026-10-18 · M01: backend, frontend y base de datos alineados

- **El token de la PWA se perdía en la redirección:** el inicio de sesión redirige a `/inicio` y de ahí al inicio del rol; el *flash* (`tokenPwa`, `exito`) se consumía en `/inicio` y nunca llegaba a la página. `RedirigirSegunRol` hace `reflash()`.
- **Ajustes del *starter kit*:** traen "Borrar cuenta" y la edición del correo, que chocaban con "ningún usuario se borra" y con la copia del correo en `auditoria.actor`. Se quitan; queda el cambio de contraseña.
- **Auditoría con `detalle`** (JSON nulo) y parámetro `detalle` en `RegistroAuditoria`: M16 exigía guardar el motivo y el antes/después de los cambios de catálogos y parámetros, pero ni la tabla ni el contrato lo admitían. Añadida también la fila del token de la PWA (quién actúa con él), que faltaba.
- **Props alineadas con el backend:** auditoría con `actor` (antes `usuario`), `ip` nula, `detalle` y la lista `tripulantes` para el filtro; usuarios con `filtros`; tipo de `flash.tokenPwa`. La acción "Cambiar contraseña" figura en la ficha de frontend.
- **Tokens de dispositivo solo en la nave** (`SoloEnNodo:nave`): los dispositivos envían a la nave; en la Tierra la página solo lista y revoca. `TokenSimuladorSeeder` añadido a la lista de archivos del backend. La página de auditoría usa el *layout* del rol (Tierra para el médico de vuelo).

## 2026-10-18 · Configuración por misión

- **Cada misión tiene su propia configuración** ([M16 §3.6](modulos/M16-configuracion/backend/README.md#36-configuración-por-misión)): `umbrales` y `parametros_mision` llevan `mision_id`; una misión nueva arranca con los valores por defecto (iguales en los dos nodos, sin enviar nada) y el médico de vuelo cambia lo que necesite. Los catálogos siguen siendo comunes.
- **Nada clínico fijo en el código:** 26 valores que estaban escritos en las reglas de M05, M06, M08, M09, M11, M12, M13, M14 y M15 pasan a ser parámetros de misión con su fuente (por ejemplo `sueno_programado_h` = 8,5 h, `mareo_dias` = 3, `alerta_confirmacion_min` = 30). Cada módulo lo indica en sus reglas comunes.
- **Horas de sueño programadas** (`sueno_programado_h`, 8,5 h según Fase 1 §2.8) como meta en la gráfica de sueño; las noches de `SUENO_MAL` salen de la persistencia de su fila de umbral (hoy 3).
- **Parámetros clínicos y operativos:** los clínicos los edita el médico de vuelo y los operativos el admin. Los de línea base solo se cambian antes del día 1.

## 2026-10-18 · M16: segunda revisión

- **Validación de valores por tipo de regla:** HU-16.01 comprobaba todo valor contra el rango posible de la variable, pero los operadores de porcentaje (`FC_REPOSO_CAMBIO` 20 %, `MASA_PERDIDA` 5 %) y dos reglas especializadas no están en la unidad de su variable: el 85 % de eficiencia de `SUENO_MAL` se habría rechazado contra 0–16 h y el 80 % de `DOSIS_PROYECCION` se habría validado contra mSv/d. Ahora hay rangos por operador y por regla.
- **`UmbralModificado` lleva `variable_codigo`:** M12 invalida la caché `umbrales:<codigo>` con ese evento, que no traía el código. Actualizados el índice de módulos §5 y M12 HU-12.02.
- **HU-16.03 depende de HU-15.02** (no de HU-15.01): las ventanas sin enlace, que definen el apagón, nacen en HU-15.02. Las dos van en el Sprint 4, con HU-15.02 antes.
- **Rutas de la Tierra en la nave:** la ficha de frontend decía que no se registran (`404`) y la de backend usa `SoloEnNodo` (`403`); se unifica en `SoloEnNodo:tierra` (`403` con su mensaje).
- **Revisión de ajustes:** solo se confirma o revierte el ajuste pendiente vigente (uno reemplazado responde `422`); al confirmar, la versión nueva de la Tierra conserva la fuente anterior salvo que el médico la cambie. Ejemplos corregidos (`FC_REPOSO_LIMITES` es crítico; rango posible de la FC 20–250) y M09 nombra la fila `DOSIS_PROYECCION` de M16.
- **`valor2` en la ficha de base de datos:** decía "solo para `fuera_de`", pero `SUENO_MAL` lo usa para su segundo umbral (eficiencia).

## 2026-10-18 · M16: historias por capa y correcciones

- **Historias por capa de M16,** una por archivo: [base de datos](modulos/M16-configuracion/database/HU/README.md), [backend](modulos/M16-configuracion/backend/HU/README.md) y [frontend](modulos/M16-configuracion/frontend/HU/README.md). Con esto, los 16 módulos tienen sus historias por capa.
- **Columna `evaluador` en `umbrales`** (`generico` o `especializada`): sin ella, el motor de M12 habría evaluado `CO2_CEFALEA` como "pCO₂ > 3,0 durante 60 min" sin mirar la cefalea, duplicando `CO2_ALTO`. Actualizado M12 §3.2.
- **Numeración de versiones por origen:** la única `(regla_codigo, version)` impedía guardar un ajuste temporal junto a la versión de la Tierra que modifica; ahora es única `(regla_codigo, version, origen)` y los ajustes se numeran aparte.
- **`modificado_por` nulo en las filas sembradas** (era obligatorio y el *seeder* habría fallado); `UmbralesSeeder` usa `firstOrCreate` para que volver a sembrar no deshaga un cambio del médico.
- **Quién siembra qué:** HU-16.01 crea `umbrales`, `catalogo_variables` y `parametros_mision` (las necesitan M03 y otros en el Sprint 1); HU-16.02, `catalogo_pruebas` y `catalogo_sintomas`, y la edición de variables y parámetros (la ficha la mencionaba, pero ninguna historia la incluía).
- **Códigos de síntoma de hasta 19 caracteres,** para que `AH-SINTOMA-<TIPO>` quepa en 30 (M11). Numeración duplicada de la ficha de backend §3.1 corregida.

## 2026-10-18 · M15: historias por capa y correcciones

- **Historias por capa de M15,** una por archivo: [base de datos](modulos/M15-sincronizacion/database/HU/README.md), [backend](modulos/M15-sincronizacion/backend/HU/README.md) y [frontend](modulos/M15-sincronizacion/frontend/HU/README.md) (HU-15.01 sin pantalla).
- **La marca de importación viaja en el evento** (`$evento->importado`): `ContextoImportacion` era una marca global mientras dura la importación, pero los eventos se publican después de confirmar y sus *listeners* van en cola, en otro proceso, donde la marca ya no existe; lo importado se habría reenviado y habría generado hallazgos dobles. Actualizados el índice de módulos (regla 6), M01, M03, M12 y M15.
- **La Tierra guarda y luego importa:** el `POST /sync/paquetes` solo guarda (estado nuevo `recibido`) y `sync:procesar-recibidos` importa en segundo plano, con estado `error` y reintentos; importar 2.000 paquetes en una petición no cabía en cPanel.
- **`id` deterministas** (UUID v5) para los paquetes de datos concretos: los módulos que publican dos eventos por registro ya no generan dos paquetes.
- **Empaquetador de `detalle`** cada minuto con la tabla `detalle_empaquetado` (los lotes de M03 no se pueden marcar): 60 lotes en vivo de una hora forman un paquete y los lotes tardíos no se pierden ni se duplican.
- **Consulta sin conexión con Inertia** definida (`router.on('success')` guarda, precarga tras el inicio de sesión, `router.on('exception')` + `router.push` muestra sin pedir al servidor); bloqueo entre pestañas (Web Locks); respuestas `403` y `413` en la cola.
- **Recomendaciones con `autor` en texto** (el usuario de la Tierra no existe en la nave). HU-15.01 (2 pts) reúne el encolado de siete módulos: queda marcada para re-estimar.

## 2026-10-18 · M14: historias por capa y correcciones

- **Historias por capa de M14,** una por archivo: [base de datos](modulos/M14-reportes/database/HU/README.md), [backend](modulos/M14-reportes/backend/HU/README.md) y [frontend](modulos/M14-reportes/frontend/HU/README.md).
- **Semáforo "al final del día":** el contrato de M13 solo daba el estado actual; nuevo `EstadoSemaforo::alInstante(tripulanteId, instante)`, y descartar una alerta guarda `cerrada_en` para poder saber si estaba activa ese día.
- **Exportación en cola y por bloques,** como máximo 7 días por exportación: 30 días (~1 millón de lecturas) no cabían en una petición de cPanel. Columnas `estado`, `error` y `bytes` en `reportes_generados`. Sexo y grupo de edad **desactivados por defecto** (con 4 tripulantes casi identifican a cada persona).
- **"Desactualizado" calculado al consultar:** el frontend lo recibía sin columna ni forma de calcularlo; se quita el *listener*.
- **Criterio del plan (Sprint 4) corregido:** decía "el reporte de misión de T2 (tras E4 y E7)"; E7 es de un tripulante hombre y el simulador lo inyecta en T3. Ahora: T2 para E4 (PVT) y T3 para E7 (Hb).
- **La exportación se prueba con datos reales en el Sprint 4:** el investigador solo entra en la Tierra, adonde las lecturas llegan con HU-15.02.

## 2026-10-18 · M13: historias por capa y correcciones

- **Historias por capa de M13,** una por archivo: [base de datos](modulos/M13-alertas/database/HU/README.md), [backend](modulos/M13-alertas/backend/HU/README.md) y [frontend](modulos/M13-alertas/frontend/HU/README.md).
- **Orden de las migraciones:** `alertas` (HU-13.01) tenía una clave foránea a `casos_clinicos`, que nace en HU-13.03; ahora la clave se añade en HU-13.03 con una migración nueva.
- **Tabla `alerta_hallazgos`:** sin ella, una alerta descartada volvía a aparecer con la siguiente ocurrencia del mismo episodio de M12, y un evento reintentado por la cola se contaba dos veces.
- **Copia del protocolo en el caso** (`protocolo_pasos`, `protocolo_validado_en`): editar un protocolo con un caso abierto descuadraba los números de paso de sus acciones.
- **Confirmación automática por familias continuas** (FC, SpO₂, FR, temperatura, glucosa, CO₂ del módulo, `AMB_*`); el evento no decía qué avisos eran continuos.
- **Acciones del caso:** la ruta pasa a `/api/v1/alertas/casos/{caso}/acciones` (antes apuntaba a la alerta); tipos `paso`, `nota` y `recomendacion`, con `autor` (las recomendaciones de la Tierra no tienen usuario en la nave); una recomendación sin caso queda en la alerta y se copia al abrirlo.
- **Cerrar un aviso sin caso** con una nota (antes había que abrir un caso). **Alarma con Web Audio** en lugar de `alarma.mp3` (funciona sin conexión, sin archivo ni licencia; M15 ya no lo precarga).

## 2026-10-18 · M12: historias por capa y correcciones

- **Historias por capa de M12,** una por archivo: [base de datos](modulos/M12-analitica/database/HU/README.md), [backend](modulos/M12-analitica/backend/HU/README.md) y [frontend](modulos/M12-analitica/frontend/HU/README.md) (HU-12.02 sin pantalla).
- **`HallazgoDetectado` con `familia`, `sistema`, `modulo_nave_id` y `ocurrencia`:** M13 agrupa por familia y pinta el semáforo por sistema, y el evento no los llevaba (índice de módulos §5 actualizado).
- **Episodios en `hallazgos`:** columnas `abierto`, `ocurrencias`, `valor_extremo`, `actualizado_en` y `modulo_nave_id` (los episodios ambientales de dos módulos se separaban mal sin ella); regla de cierre del episodio.
- **Persistencia `min` sin ambigüedad:** al menos ⌈0,8 × N⌉ lecturas válidas en la ventana y todas cumplen; los artefactos no cuentan ni a favor ni en contra.
- **Recálculo de la línea base con tabla propia** (`solicitudes_recalculo`): la Tierra necesitaba guardar la solicitud mientras viaja a la nave y mostrar su estado.
- **Valor p en PHP** con la beta incompleta regularizada (PHP no trae la distribución t). **Contexto de cada línea base** fijado (sueño, masa y cognitivas con `NULL`), mínimo de 10 valores y precedencia de la previa al vuelo; el simulador debe enviar los lotes de cada dispositivo en orden de tiempo.
- `SUENO_MAL` se activa solo con `AH-SUENO-DURACION` (coherente con M06).

## 2026-10-18 · M11: historias por capa y correcciones

- **Historias por capa de M11,** una por archivo: [base de datos](modulos/M11-sintomas/database/HU/README.md), [backend](modulos/M11-sintomas/backend/HU/README.md) y [frontend](modulos/M11-sintomas/frontend/HU/README.md).
- **Códigos `AH-SINTOMA-*` fuera de `catalogo_variables`:** algunos superan sus 20 caracteres (`AH-SINTOMA-CONGESTION_NASAL` tiene 27) y sembrarlos rompería la migración; solo viajan en los eventos (nota en M16).
- **Icono de los síntomas en el frontend:** el catálogo de M16 no tiene columna de icono; se fija una tabla de iconos en `contratos/sintomas.ts`.
- **El médico de vuelo no tenía pantalla de síntomas** aunque el alcance lo incluía: nueva `Tierra/Sintomas/Show`.
- **Foto derecha y sin EXIF:** la recodificación del servidor borra también la orientación, así que el cliente endereza la foto al comprimirla (`createImageBitmap` con `imageOrientation: 'from-image'`).
- **Casos definidos:** segunda foto distinta del mismo síntoma → `422`; segundo cuestionario de mareo del mismo día con otro `id` → `422`; el ítem "cefalea" del cuestionario no cuenta para `CO2_CEFALEA`; `respuestas_mareo` con `origen` y `created_at`; intensidad con 11 botones (más fácil que un deslizador).

## 2026-10-18 · M10: historias por capa y correcciones

- **Historias por capa de M10,** una por archivo: [base de datos](modulos/M10-fisico-metabolico/database/HU/README.md), [backend](modulos/M10-fisico-metabolico/backend/HU/README.md) y [frontend](modulos/M10-fisico-metabolico/frontend/HU/README.md).
- **La masa corporal no tenía dispositivo:** es un código de sensor que entra por lote de M03, que exige un dispositivo asignado al tripulante, y en M02 no había ningún tipo para medir la masa; el simulador no podía enviarla y `MASA_PERDIDA` nunca tendría datos. Nuevo tipo `medidor_masa` (`masa-T<n>`): la misión de demostración pasa de 28 a 32 dispositivos (M02, M03 y simulador actualizados).
- **Línea base de la masa alineada con M12** (previa al vuelo o media de la semana 1, `contexto = null`); la ficha decía "la primera medición".
- **FC de la sesión al mostrarla:** calcularla al guardar fallaba si las lecturas de FC llegaban después (relleno histórico o sin conexión).
- **Solapamiento con un índice:** columna `fin` calculada en el servidor; una corrección (`anula_id`) no choca con la sesión que anula. Tablas de solo inserción con `anula_id`.
- **`CHECK` de nutrición** completo (una comida exige calorías; un líquido, volumen).

## 2026-10-18 · M09: historias por capa y correcciones

- **Historias por capa de M09,** una por archivo: [base de datos](modulos/M09-radiacion/database/HU/README.md), [backend](modulos/M09-radiacion/backend/HU/README.md) y [frontend](modulos/M09-radiacion/frontend/HU/README.md).
- **Nota falsa del simulador corregida:** decía que con `--perfil marte` la proyección superaba el 80 % del límite de carrera; con la misión de demostración (60 días), el máximo es ~228 mSv (T1: 120 + 1,8 × 60), lejos de 480. Para enseñar `DOSIS_PROYECCION` hace falta una `fecha_fin` de ~200 días.
- **Dosis minuto a minuto:** resuelve los huecos y el cambio de día (la última lectura del día cubre el primer minuto del siguiente) y no cuenta como "sin dato" los minutos de hoy que aún no han pasado.
- **Recálculo diario con un trabajo único** por tripulante y día (como los agregados de M04): cada lote horario del relleno recalculaba el día completo.
- **Evento solar definido:** mínimo de 60 lecturas en 24 h para tener referencia; evaluación en cada lectura del lote; mediana en caché 10 min; episodio (inicio, activo, fin, dosis del evento) calculado sin tabla; varios tripulantes forman una sola alerta global.
- **`id` UUID v5** en `dosis_diarias`, igual en los dos nodos; la Tierra recalcula la dosis con los lotes importados.

## 2026-10-17 · M08: historias por capa y correcciones

- **Historias por capa de M08,** una por archivo: [base de datos](modulos/M08-ambiente/database/HU/README.md) (sin tablas propias: consultas e índices), [backend](modulos/M08-ambiente/backend/HU/README.md) y [frontend](modulos/M08-ambiente/frontend/HU/README.md) (HU-08.02 sin pantalla).
- **Las reglas `AMB_*` nunca se habrían cumplido:** su persistencia era "60 min", y la unidad `min` del motor de M12 exige al menos 60 lecturas en la ventana, pero VOC, partículas y ruido llegan cada 5 minutos (12 por hora). Ahora: 12 lecturas `consecutivos` (M16).
- **Estado de la tarjeta por el promedio de la última hora:** con la última lectura, el ruido normal (2,0 ± 0,3 mmHg) habría mostrado "sobre el límite" un par de veces al día.
- **`ReglaCo2Cefalea` precisa:** promedio por hora de reloj UTC; se activa con la cefalea o con un lote de pCO₂, y con un lote normal termina sin consultar síntomas; la ventana de 24 h pasa a M16 (`co2_cefalea_ventana_h`).
- **Caché de 5 min** para el promedio de 7 días y las medias de 30 días (el sondeo de 10 s las recalculaba cada vez).

## 2026-10-17 · M07: historias por capa y correcciones

- **Historias por capa de M07,** una por archivo: [base de datos](modulos/M07-examenes/database/HU/README.md), [backend](modulos/M07-examenes/backend/HU/README.md) y [frontend](modulos/M07-examenes/frontend/HU/README.md).
- **Hematocrito y vitamina D en el simulador:** la agenda los incluía pero sus parámetros no estaban definidos. Simulado ([`tendencia_hematocrito.py`](herramientas/montecarlo/tendencia_hematocrito.py)): un Hto = 3 × Hb + N(0; 0,6) habría dado un aviso falso de tendencia en el **8,4 %** de las misiones normales; se fija N(0; 0,1) (Hb o Hto: ~0,6 %). Vitamina D: U(35; 50) + N(0; 3) ng/mL los días 1 y 31.
- **Reparto entre historias:** la interpretación (`bajo`/`normal`/`alto`/`critico`) pasa a HU-07.01, porque la columna es obligatoria y la gráfica del mismo sprint la usa; HU-07.02 añade *delta*, tendencia y la regla `ReglaResultadoExamen` de M12, que antes no estaba asignada a ninguna historia.
- **PDF inmutable:** `anulado_por` (que obligaba a modificar la fila vieja) se sustituye por `anula_id` en la nueva; ruta relativa al disco privado de Laravel 11 (`examenes/<tripulante>/<id>.pdf`); `sha256` para no duplicar el mismo archivo.
- **CSV de un solo uso:** confirmar dos veces la misma vista previa (doble clic) duplicaba la carga; ahora el token se borra al guardar. La vista previa marca las correcciones ("Corrige 13,1 → 13,3") y las filas repetidas dentro del archivo.
- **Concurrencia y orden:** bloqueo con nombre por tripulante, prueba y fecha para que no queden dos resultados vigentes; `proxima_fecha` no retrocede con un resultado viejo cargado tarde; las banderas se calculan con las tomas existentes al cargar.

## 2026-10-17 · M06: historias por capa y correcciones

- **Historias por capa de M06,** una por archivo: [base de datos](modulos/M06-sueno/database/HU/README.md), [backend](modulos/M06-sueno/backend/HU/README.md) y [frontend](modulos/M06-sueno/frontend/HU/README.md) (HU-06.02 sin pantalla).
- **Origen y eficiencia los decide el servidor:** el cuerpo traía `origen` (se podía marcar un registro manual como actigrafía) y una eficiencia que podía contradecir los horarios (y que el formulario pedía "estimar"). Ahora el origen sale del token y la eficiencia = horas dormidas / horas en cama. El simulador envía `acostado_en` acorde.
- **`despertado_en` siempre obligatorio:** define la noche; el registro "solo medicación" tenía una regla ambigua.
- **Un solo vigente por noche de verdad:** el índice propuesto no servía para saber si un registro está anulado, y el bloqueo de fila no evitaba dos registros nuevos vigentes a la vez; ahora `NOT EXISTS` con el índice de `anula_id` y un bloqueo con nombre por tripulante y noche.
- **`ReglaMalSueno`:** se activa una vez por noche (antes con dos eventos) y revisa las tres ventanas que contienen la noche recibida, para que una noche registrada tarde complete la racha.
- **Detalles:** regularidad con desviación poblacional (la prueba de la ficha solo cuadraba así) y mínimo de 3 noches; `medicacion` admite `NULL` ("no indicada") para no afirmar que no se tomó medicación en una noche del actígrafo.

## 2026-10-17 · M05: historias por capa y corrección de `COG_DETERIORO`

- **Historias por capa de M05,** una por archivo: [base de datos](modulos/M05-psicologia/database/HU/README.md), [backend](modulos/M05-psicologia/backend/HU/README.md) y [frontend](modulos/M05-psicologia/frontend/HU/README.md) (HU-05.04 sin pantalla).
- **Regla de deterioro cognitivo corregida con Monte Carlo** ([`cognitivo_todas_las_reglas.py`](herramientas/montecarlo/cognitivo_todas_las_reglas.py)): solo se había simulado la precisión de aritmética. Con las siete métricas y todas las condiciones, la **pendiente de 7 días** como disparador daba un aviso falso en ~97 % de las misiones normales, y las rachas, en el 11,7 %. Ahora: la pendiente solo va en la explicación; σ mínima relativa del 10 % en la mediana del PVT y el tiempo de aritmética (`sigma_min_rel` en M16); E4 a +8 %/día. Resultado: 0,8 % de falsos en E0 y 95 % de detección de E4. Se actualizan 04 §5.2, §5.3 y §5.5, M12 §3.6 y la ficha del simulador, que ahora **fija** el ruido del PVT (σ_ln 0,20) y de la aritmética (σ_ln 0,30), antes sin especificar.
- **Alarma crítica durante un test:** la ficha pedía bloquear las notificaciones durante el PVT, lo que habría tapado, por ejemplo, la orden de ir al refugio; ahora la alarma cancela el test.
- **PVT y memoria terminan siempre:** estímulo sin respuesta en 30 s = lapso (`pvt_espera_max_s`, columna `sin_respuesta`, `tiempo_ms` nulo); tope de 12 dígitos en la memoria (`memoria_longitud_tope`).
- **Validación en el servidor:** recalcula `es_correcta`, las falsas salidas por tiempo y la secuencia de longitudes de la memoria; solo el token de un dispositivo de tipo `simulador` puede enviar sesiones en nombre de un tripulante.
- **Confidencialidad:** el estrés va en un campo `confidencial` de la explicación que el CMO no ve; el astronauta no ve sus métricas tras el test (solo "Hecho").
- **Base de datos:** `programacion_tests.tipo` pasa a `varchar(17)` (`escalas_semanales` no cabía en 15); `origen` en las escalas; recordatorios con agenda por defecto para tripulantes sin programación.

## 2026-10-17 · M04: historias por capa y correcciones

- **Historias por capa de M04,** una por archivo: [base de datos](modulos/M04-signos-vitales/database/HU/README.md), [backend](modulos/M04-signos-vitales/backend/HU/README.md) y [frontend](modulos/M04-signos-vitales/frontend/HU/README.md) (HU-04.05 sin pantalla).
- **Picos R del ECG:** la página los dibuja pero la tabla no los guardaba; la señal y los picos van ahora en un archivo JSON del disco privado. Métricas `NULL` cuando hay pocos latidos; `duracion_s` decimal; columnas `dispositivo_id` y `created_at`.
- **Prueba ortostática con los umbrales de M16:** el resultado usaba 20/10 fijos y podía contradecir el aviso de M12 si el médico cambiaba el umbral; ahora se leen de M16 y se guardan con la prueba.
- **Agregados diarios con un trabajo único** por tripulante y día: cada lote del relleno recalculaba el día completo (decenas de millones de filas leídas en cPanel). Se agregan los 8 códigos de M04 (la actividad faltaba en el volumen). `id` UUID v5, igual en los dos nodos.
- **Inicios de F1 y F2:** HU-04.01 construye `/astronauta` (aviso de sensores, semáforo y tarjetas) y HU-04.02 `/cmo`, que reemplazan las páginas temporales de M01. El semáforo es `null` hasta HU-13.01 (Sprint 3).
- **Unidades por código:** `/min` es "lpm" en la FC y "rpm" en la FR (la convención decía siempre "lpm"). Las tarjetas no dicen "normal": el estado lo da el semáforo.
- **Simulador:** un `404` de un endpoint aún no construido (el ECG antes del Sprint 4) se omite con un aviso en lugar de detener la carga.

## 2026-10-17 · M03: historias por capa y correcciones

- **Historias por capa de M03,** una por archivo: [base de datos](modulos/M03-ingesta/database/HU/README.md), [backend](modulos/M03-ingesta/backend/HU/README.md) y [frontend](modulos/M03-ingesta/frontend/HU/README.md) (solo HU-03.03 tiene pantalla).
- **Origen de las lecturas simuladas:** se decidía con el dispositivo nombrado en el lote, y los datos que el simulador envía en nombre de `astroskin-T2` quedaban como `sensor`. Ahora decide el dueño del token.
- **Consulta del simulador:** usaba `GET /ingesta/estado-sensores` con su token de dispositivo, que ese endpoint no aceptaba. Se sustituye por `GET /ingesta/periodo` (columnas nuevas `medido_desde` y `medido_hasta` en `lotes_ingesta`), y `estado-sensores` no se construye (F1 usa la *prop*).
- **Importación en la Tierra:** el paquete `detalle` lleva también las filas de `lotes_ingesta`; sin ellas, la clave foránea de `observaciones.lote_id` rechazaba la importación.
- **Aviso de colocación solo con datos en vivo:** la consulta une con `lotes_ingesta.modo` (`observaciones` no tiene `modo`); el simulador y las tabletas no tienen estado; se quita el enlace a unas instrucciones que no existían.
- **Validación más estricta:** autorización antes que la idempotencia; el dispositivo nombrado debe estar asignado también con el token del simulador; rechazos `codigo_no_admitido` (códigos derivados) y `ambito_invalido` (ambiental en lote personal o al revés); IDs repetidos en un lote → `422`; `LoteIngerido` solo si se guardó algo.
- **Prueba de carga al cierre del Sprint 1** (plan §3 y §4): en el Sprint 0 el endpoint no existe; allí se verifica solo el cron y la cola. Comandos `ingesta:prueba-carga` e `ingesta:estado-cola`.

## 2026-10-17 · M02: historias por capa y correcciones

- **Historias por capa de M02,** una por archivo: [base de datos](modulos/M02-mision/database/HU/README.md), [backend](modulos/M02-mision/backend/HU/README.md) y [frontend](modulos/M02-mision/frontend/HU/README.md).
- **Tabletas compartidas:** la restricción de `dispositivos` no admitía una tableta sin asignar; ahora la asignación se define por tipo (personal, ambiental, tableta o simulador).
- **Identificadores de los dispositivos fijados** (`astroskin-T<n>`, `oximetro-T<n>`…, `ambiente-<módulo>`, `simulador`): el simulador los necesita y no estaban definidos (antes había un ejemplo `co2-modulo-lab`).
- **Fecha de inicio estable:** el *seeder* ya no mueve la fecha si la misión existe, y fuera de `local` exige `MISION_INICIO` (la misma en los dos nodos); antes, sembrar los nodos en días distintos desalineaba el día de misión.
- **Campos inmutables:** código del tripulante y del módulo, identificador y tipo del dispositivo (los usan los lotes, el simulador y los tokens).
- **Envío a la Tierra en el Sprint 3:** el paquete `mision` y la línea base hacia la nave necesitan la cola de M15 (HU-15.01); quedan como parte B de cada historia. La instantánea no incluye batería ni último contacto.
- **HU-02.03 sin depender del catálogo de M16:** las 8 variables de la línea base previa son una lista fija de M02, con una prueba que la compara con el catálogo al cerrar el Sprint 1. Se crea aquí la versión mínima de `RegistroLineaBase` y `ConsultaLineaBase` (M12).
- **Otros módulos:** M03 ofrece `EstadoSensores::todos()` para la página de dispositivos; el simulador enlaza la convención de identificadores; el texto de F1 "sin tripulante" y el día de misión de la cabecera de M01 usan los de M02; middleware compartido `SoloEnNodo`.

## 2026-10-16 · M01: historias por capa y correcciones

- **Historias por capa de M01:** [base de datos](modulos/M01-identidad/database/HU/README.md), [backend](modulos/M01-identidad/backend/HU/README.md) y [frontend](modulos/M01-identidad/frontend/HU/README.md) (con una guía visual provisional mientras no haya patrón de diseño), con **una historia por archivo** en la carpeta `HU/` de cada capa y un `README.md` de índice con las reglas comunes. Incluyen el trabajo de M01 dentro de HU-15.04 (token de la PWA), que no estaba asignado a ninguna historia.
- **Tokens con rutas web:** emitir y revocar tokens pasan de `/api/v1/identidad/tokens/...` con sesión (que contradecía "las escrituras en la API exigen token") a `/admin/tokens`.
- **`tokenable_id` de 36 caracteres:** la migración de Sanctum usa `bigint` y los dispositivos tienen UUID; sin el cambio, emitir un token de dispositivo fallaba.
- **Auditoría:** *triggers* en lugar de quitar permisos de MySQL (cPanel no lo permite por tabla); columna `actor` (los usuarios no se sincronizan y el `user_id` no basta en la Tierra); acción `login_fallido`; envío a la Tierra por horas con `auditoria_envios`, movido al Sprint 3 (parte B, dentro de HU-15.01) porque necesita la cola de M15.
- **Usuarios:** cambiar el rol revoca los tokens; el admin no puede quitarse el rol ni desactivarse; un usuario vinculado a un tripulante solo puede ser `astronauta` o `cmo`; sin "Recordarme"; `id` fijos en los seeders y `AUTO_INCREMENT` distinto por nodo.
- **Sprint 0:** las *props* `tripulante` y `mision` valen `null` mientras no existe M02 (antes daban error).
- **Otros módulos:** M02 ofrece `ConsultaDispositivos::porId()` y `::todos()`; M15 lista a M01 entre los usuarios de `ContextoImportacion`; M16 no audita los umbrales (lo hace M01, para no duplicar).

## 2026-10-16 · Pasada de coherencia final

- **Propuesta alineada con los módulos:** 01 (referencia de 250 a verificar; aviso de CO₂ sin síntomas), 02 §6 (consumidores de `AlertaCreada` y `UmbralModificado`), 04 §4.1 (el flujo de ingesta publica `LoteIngerido`, no `ObservacionRegistrada`), 04 §7 (E3 y E6), 05 §5 (criterios de HU-05.04: crítico solo en el PVT y E0 sin avisos).
- **Fecha corregida:** la revisión de velocidad es el domingo 1 de noviembre, cierre del Sprint 2 (decía "viernes").
- Plan §6: la regla `CO2_ALTO` figura entre los entregables del Sprint 3.
- README: tres preguntas abiertas nuevas (límite de 250, fuentes de VOC/partículas/ruido, dosis lunar). Glosario: SMAC.

## 2026-10-16 · Revisión del simulador

- **Los escenarios en histórico se perdían como duplicados:** los `id` salen de la semilla y del momento, así que repetir el histórico con un escenario sobre una base que ya tenía E0 hacía que la API descartara todas sus lecturas. Ahora: base limpia y un solo comando con todos los escenarios, o `vivo` para inyectar sobre una misión cargada; el simulador se detiene si detecta el conflicto.
- **Varios escenarios a la vez:** `--escenario EN:tripulante:inicio` es repetible (antes solo uno por comando, y el ensayo general necesita varios).
- **E6 solo en vivo** (el aviso de colocación solo mira datos en vivo); E5 con la receta de M15; E8 con los minutos esperados de aviso, confirmación y crítico.
- Ventanas de ECG diarias en nombre del wearable; aviso sobre T5 y T6 (no existen en la misión de demostración).
- Las simulaciones de Monte Carlo entran en CI con un límite del 1 % de avisos falsos.
- Plan §7: el ensayo general indica qué escenarios van en histórico y cuáles en vivo.

## 2026-10-16 · Revisión del módulo M16 (Configuración)

- **Ajuste temporal acotado:** solo durante un apagón (ventana sin enlace o retardo de Marte) y solo para cambiar valores; nunca puede desactivar una regla ni bajar su nivel.
- **Cambios cruzados:** si la Tierra edita una regla que tiene un ajuste pendiente, el ajuste sigue vigente a bordo hasta que el médico de vuelo lo revise.
- **Dónde se edita:** umbrales, catálogos y parámetros solo en la Tierra (solo lectura en la nave, salvo el ajuste temporal); permisos con los *Gates* de M01.
- **Prueba de los umbrales sembrados:** se reescribió como comparación de valores, porque 04 §8 también lista reglas que viven en otros módulos y la comparación "tabla contra tabla" no era posible.

## 2026-10-16 · Revisión del módulo M15 (Sincronización)

- **Cupo del enlace en bytes:** 20 MB, 5 MB o 1 MB por ciclo según el perfil (antes 500, 200 o 50 paquetes sin importar su tamaño, lo que podía dar peticiones de cientos de MB que cPanel rechaza); peticiones de 4 MB como máximo (M15 y M16).
- **Paquetes `detalle` por dispositivo y hora, comprimidos:** en vivo llega un lote por minuto, y un paquete por lote acumulaba ~300.000 paquetes en 14 días (vaciar la cola tardaba ~10 h en `leo` y ~4 días en `marte`). Ahora son ~10.000 paquetes (~20 MB): unos 5 min en `leo` y unos 50 min en `marte`.
- **Registros sin evento:** escalas, sesiones incompletas, cuestionarios de mareo y registros de sueño solo con medicación se encolan directamente; antes no llegaban a la Tierra.
- **Fallo del servidor de la Tierra:** el paquete se reintenta en el siguiente ciclo (columna `intentos`), sin confundirlo con una ventana sin enlace.
- **Receta para mostrar E5 en la demo** sin esperar 14 días reales.

## 2026-10-16 · Revisión del módulo M14 (Reportes)

- **Confidencialidad en los reportes:** el reporte tiene dos versiones; la del CMO no incluye escalas ni métricas cognitivas (solo las alertas cognitivas), en línea con M05. La completa es la del médico de vuelo.
- **Reporte automático solo en la nave:** la Tierra recibe el de la nave; nuevas columnas `audiencia` y `nodo_origen` para que no choquen con los generados a demanda.
- **Seudónimos estables:** se calculan con `SEUDONIMO_KEY` en lugar de `APP_KEY`, que puede rotar.
- **Límite de la anonimización:** con 4 tripulantes, el sexo y el grupo de edad casi identifican a cada persona; la exportación lo advierte y permite excluir esas columnas.

## 2026-10-16 · Revisión del módulo M13 (Alertas y casos clínicos)

- **Las alertas se gestionan solo en la nave:** antes el médico de vuelo podía confirmar o descartar alertas en la Tierra, pero ese cambio no llegaba a la nave y las copias quedaban distintas. Ahora la Tierra ve las alertas y responde con recomendaciones ligadas a la alerta. Nuevo *Gate* `ver-alertas`; `gestionar-alertas` queda solo para el CMO (M01, M05, M13 y la matriz de 02 §7).
- **Cambios de una alerta hacia la Tierra:** cada cambio envía el estado completo con `actualizada_en`, y la Tierra aplica solo el más reciente (paquetes desordenados no deshacen cambios).
- **Una alerta no se cierra sola** cuando el valor se normaliza; la cierra una persona.
- Se añadió el directorio [`herramientas/`](herramientas/README.md) con los scripts de comprobación de enlaces e historias y las simulaciones de Monte Carlo usadas en la revisión.

## 2026-10-16 · Revisión del módulo M12 (Analítica y línea base)

- **Umbrales que llegan de la Tierra:** M12 procesa `UmbralModificado` también durante las importaciones para invalidar su caché; antes, un umbral nuevo de la Tierra no se aplicaba en la nave (M12, M15 §3.5 y la regla 6 del índice de módulos).
- **Ventana deslizante:** el motor evalúa cada minuto de un lote, no solo el final; antes, un episodio de 5 minutos en mitad de un lote histórico de una hora no generaba alerta. Un solo hallazgo por regla, tripulante y episodio.
- **Línea base por contexto:** los vitales tienen línea base de reposo y de sueño, y `cambio_pct_mayor` compara con la del mismo contexto (la bajada normal de la FC al dormir ya no puede dar avisos falsos con datos reales).
- **Líneas base en la Tierra:** las calculadas en la nave viajan en el paquete `resumen`; el recálculo lo solicita la Tierra y lo ejecuta la nave.
- La línea base inicial se calcula con la primera lectura del día 8. La garantía de E0 queda respaldada por las simulaciones de M05, M06 y M07.

## 2026-10-16 · Revisión del módulo M11 (Síntomas)

- **Tabla de la API rota:** una línea en blanco partía la tabla y la ruta de la foto no se mostraba como parte de ella.
- **Cuestionario de mareo sin conexión:** el servidor calcula el día de misión a partir de `respondida_en` (antes lo enviaba el cliente); una respuesta del día 3 enviada el día 4 queda en el día 3. Nota para la demo: con la misión de demostración el cuestionario ya no aparece.
- **Foto:** solo el dueño del síntoma la puede subir, y el servidor la vuelve a codificar sin metadatos EXIF.
- **Validaciones y autenticación:** `inicio_en` no puede ser futuro; solo token; el simulador debe enviar `tripulante_id`; permisos con los *Gates* de M01.

## 2026-10-16 · Revisión del módulo M10 (Físico y metabólico)

- **Pérdida de masa con una medición semanal:** la regla queda definida para una sola medición por semana; sin medición en los últimos 7 días no se evalúa. Línea base: la previa al vuelo o la primera medición.
- **Simulador:** faltaban los parámetros de la masa corporal (ahora base del perfil, −0,2 % por semana y ruido de 0,3 kg: ~1 % en 30 días) y la coincidencia de las sesiones de ejercicio con las horas en que las lecturas llevan `contexto = ejercicio`.
- **Autenticación:** solo token; con la PWA el tripulante sale del usuario; el simulador debe enviar `tripulante_id`. Permisos con los *Gates* de M01.
- La glucosa simulada de E0 (75–155 mg/dL) queda dentro de 70–180: sin cambios.

## 2026-10-16 · Revisión del módulo M09 (Radiación)

- **La dosis ya no se subestima:** cada lectura cuenta el tiempo hasta la siguiente válida (máximo 5 min), así que los artefactos y las lecturas perdidas ya no restan dosis; los huecos mayores quedan en `minutos_sin_dato` y la página lo avisa.
- **Lotes atrasados:** recalculan el día afectado y todos los siguientes (la dosis acumulada depende de los días previos).
- **Límite de 250:** marcado como **pendiente de verificar** en NASA-STD-3001, donde parece ser un límite de 30 días en mGy-Eq para los órganos hematopoyéticos, no "por evento". La página lo muestra como referencia de 30 días (M09 y M16).
- **Perfil `luna`:** se propone como fuente la medición de Chang'e-4 en la superficie lunar (~1,37 mSv/d, Zhang *et al.*, 2020), a verificar antes de reemplazar el 1,8 provisional.
- Permisos con los *Gates* de M01.

## 2026-10-16 · Revisión del módulo M08 (Ambiente)

- **CO₂ alto sin síntomas:** nueva regla `CO2_ALTO` (aviso global por módulo, pCO₂ > 3,0 mmHg sostenido 60 min). Antes, un módulo a 8 mmHg no generaba ninguna alerta si nadie reportaba una cefalea. Actualizados M08, M16, 04 §8, el criterio de E3 del plan y el escenario E3 del simulador. Sin nivel crítico, porque no hay un valor con fuente.
- **Aviso sobre los límites de VOC:** los SMAC son por compuesto, no para un "VOC total"; la fuente que se cargue debe corresponder a la serie simulada. Para la demo hay que conseguir al menos un límite con fuente antes del evento.
- Permisos de M08 con el *Gate* `ver-ambiente`.

## 2026-10-16 · Revisión del módulo M07 (Exámenes médicos)

- **Falso "descenso progresivo" de Hb corregido:** el cambio mínimo de la tendencia pasa de 5 % a **7,5 %** (M07, M16 y 04 §6.3). En 20.000 misiones normales simuladas, los avisos falsos bajan del 17 % al 0,4 %, y E7 se sigue detectando el 99,8 % de las veces.
- **Un resultado por prueba y fecha de muestra:** un segundo resultado del mismo día es una corrección (`anula_id`), y la tendencia y el *delta* usan solo los vigentes.
- Las pruebas con dirección `ambas` no tienen regla de tendencia; `fecha_muestra` futura → `422`; el simulador debe enviar `tripulante_id`; `cargado_por` admite `null` (simulador).
- Los recordatorios de Hb y Hto empiezan el primer lunes de la misión, igual que la agenda del simulador.

## 2026-10-16 · Revisión del módulo M06 (Sueño)

- **Noches que cruzan la medianoche:** `noche` pasa a ser la fecha UTC de (`despertado_en` − 12 h) y la calcula el servidor. Antes era la fecha en que empezaba el sueño: con el simulador acostándose a las 23:00 ± 30 min, casi una noche de cada dos cambiaba de fecha y el sistema fusionaba noches distintas.
- **Registro manual solo de medicación:** horarios, duración y eficiencia son opcionales en ese caso; no publica `ObservacionRegistrada` hasta que se fusiona con la actigrafía.
- **Racha de mal sueño:** cuenta noches de calendario seguidas con registro vigente; una noche sin registro corta la racha. La regla evalúa solo los registros vigentes, no los anulados por una fusión.
- **Autenticación:** solo token; el actígrafo escribe para su tripulante asignado y el simulador debe enviar `tripulante_id`.
- Riesgo de aviso falso en E0 medido con 20.000 misiones simuladas: 0,2 %.

## 2026-10-16 · Revisión del módulo M05 (Psicología y cognición)

- **Falsas alertas de aritmética corregidas:** la σ mínima de la precisión pasa de 2 a **7,5 puntos** (M05, M16 y 04 §5.2). Con 20.000 misiones normales simuladas, los avisos falsos bajan del 22 % al 0,1 %, y una caída real de 15 puntos se detecta el 95 % de las veces.
- **El simulador puede cargar sesiones y escalas:** los endpoints de M05 aceptan el token del dispositivo `simulador` con `tripulante_id` en el cuerpo (antes solo el token de la PWA, y fallaban la línea base cognitiva y E4).
- **Nivel crítico solo en el PVT:** era el único test con lapsos; en aritmética y memoria, z ≥ 3 en dos sesiones seguidas da un aviso (pseudocódigo de 04 §5.5 actualizado). La "métrica principal" de la pendiente queda definida: mediana en el PVT y tiempo por ítem en aritmética.
- **Confidencialidad:** el CMO es un compañero de tripulación y ya no ve las escalas, la cohesión ni la evolución psicológica de los demás; solo las alertas cognitivas. Lo detallado lo ve el médico de vuelo (M05, M01 y la matriz de 02 §7).
- **Orden de las sesiones:** el número de sesión se calcula por `inicio` al consultar (no se guarda), para que las sesiones hechas sin conexión que llegan tarde queden en su lugar.
- El servidor comprueba el campo `completa` que envía el cliente; el contexto de pCO₂ se toma del contrato de M08.



- **Prueba ortostática posible en el espacio:** en microgravedad no se puede "poner de pie". La prueba tiene ahora una `modalidad`: `de_pie` (en gravedad, antes del vuelo o tras aterrizar) o `lbnp` (en vuelo, con presión negativa en la parte inferior del cuerpo). Columnas renombradas a `basal` y `estimulo`; actualizados 02 §4.2 y 04 §1.
- **Agregados en reposo:** cada periodo se calcula con todas las lecturas y solo con las de reposo (nueva columna `contexto`). Las tendencias de M12 y el reporte de misión de M14 usan los de reposo; antes la media diaria mezclaba ejercicio y sueño.
- **ECG:** el endpoint exige token de dispositivo y la misma autorización que un lote de M03; nuevas páginas `Tierra/Ecg/Index` (lista de ventanas) y `Cmo/Ecg/Show` (el CMO ve la señal desde el caso de arritmia de E1).
- **Prueba ortostática del CMO:** el `tripulante_id` va en el cuerpo (el CMO la registra a otro tripulante); nuevo *Gate* `registrar-ortostatica` en M01.
- **Nodo Tierra:** un recálculo de agregados solo reemplaza al recibido en el paquete `resumen` si usa al menos tantas lecturas.
- **Valor actual:** solo lecturas válidas de los últimos 10 minutos; si no hay, "Sin datos recientes". El sondeo de `Cmo/Tripulante/Show` pide solo la pestaña abierta.

## 2026-10-16 · Revisión del módulo M03 (Ingesta de datos)

- **Solo token:** `POST /api/v1/ingesta/lotes` exige un token de dispositivo; una sesión de navegador recibe `401`.
- **Envíos simultáneos:** la fila del lote se inserta al empezar y las lecturas con `insertOrIgnore`; dos envíos iguales a la vez dan `201` y `200`, nunca `500`.
- **Simulador:** su token puede enviar lotes en nombre de cualquier dispositivo de la misión; los demás tokens, solo los de su propio dispositivo. El simulador envía un lote por dispositivo y hora.
- **Aviso de colocación:** se calcula al consultar, solo con lotes `vivo` (el relleno histórico ya no genera avisos), con el nuevo índice `(dispositivo_id, es_artefacto, medido_en)`; `sin_datos` sale del último contacto de M02.
- **Validaciones nuevas:** `422` para tripulante o módulo desconocido; `contexto` obligatorio en las lecturas personales y `null` en las ambientales; `calidad` ausente no es artefacto.
- **Nodo Tierra:** `ImportacionLotes` guarda lo que validó la nave sin repetir la autorización ni las validaciones (el paquete `detalle` puede llegar antes que el paquete `mision`).
- **Rendimiento:** colas `default` e `historico`, trabajador por cron con `--max-time=55` y prueba de carga en cPanel como criterio de cierre del Sprint 0; plan alternativo si el relleno no cabe en 1 hora.
- **UUID ordenados en el tiempo** (formato v7, deterministas) para los `id` de las lecturas, para que la inserción en MySQL no se degrade con 1 millón de filas.
- En el MVP todas las observaciones llegan por lote (`lote_id` obligatorio; `manual` queda reservado). `ObservacionRegistrada.observacion_id` es el `id` del registro del módulo que publica, no una fila de `observaciones`.

## 2026-10-16 · Revisión del módulo M02 (Misión y tripulación)

- **Los mismos `id` en los dos nodos:** misión, tripulantes, dispositivos y módulos de la nave usan UUID v5 a partir de su código, y los cambios viajan de la nave a la Tierra en el nuevo paquete `mision` de M15 (prioridad 2).
- **Línea base previa al vuelo:** se registra en el nodo Tierra (donde trabaja el médico de vuelo) y viaja a la nave en el paquete `configuracion`; se guarda con `contexto = reposo` para los signos vitales y `null` para la masa corporal; hasta el Sprint 4 el *seeder* la carga en los dos nodos.
- **Fechas de la misión:** el *seeder* empieza la misión 29 días antes de hoy (o en `MISION_INICIO`), y el simulador usa por defecto la misma fecha y nunca genera datos futuros. Antes, el cierre del Sprint 1 fallaba porque M03 rechaza las lecturas futuras. La misión se llama "Misión de demostración AstroHealth" (dura 60 días; no es Artemis III).
- **Usuarios y tripulantes:** cada tripulante de la demostración tiene su usuario (nuevos `astronauta3@…` y `astronauta4@…` en M01); un astronauta sin tripulante ve un aviso en F1.
- **Día de misión de cualquier fecha:** nuevo `ConsultaMision::diaMision(fecha)`, para el histórico, la exportación y el mareo.
- **Batería de los dispositivos:** campo opcional `bateria_pct` en el lote de M03 (antes nadie actualizaba la columna).
- **Permisos:** *Gates* `gestionar-mision` y `registrar-linea-base` añadidos a M01.
- El token del simulador ya no lo crea el *seeder* de M02 (es de M01, HU-01.03).
- Plan §4: la lista de tablas del Sprint 1 incluye `modulos_nave`, `lotes_ingesta`, `catalogo_variables` y `parametros_mision`.

## 2026-10-16 · Revisión del módulo M01 (Identidad y acceso)

- **Dependencia circular resuelta:** HU-01.03 (tokens de dispositivo) depende ahora de HU-02.02, que crea los dispositivos, y va después de ella en el Sprint 1. HU-02.02 ya no depende de HU-01.03.
- **Escrituras en la API solo con token:** nuevo middleware `TokenDeTipo`; con la sesión del navegador, Sanctum aprueba cualquier `tokenCan()`, y un astronauta con sesión podía enviar lotes como un sensor.
- **Tableta compartida:** cada elemento de la cola offline guarda su `user_id` y solo se envía con el token de ese usuario; `credenciales` guarda un token por usuario (M01 y M15).
- **Inicio de sesión por nodo:** cada rol solo entra en el nodo donde trabaja (`RolPermitidoEnNodo`).
- **Auditoría:** el sondeo de Inertia no se audita y hay como máximo una entrada cada 10 minutos por usuario, recurso y tripulante; nuevo paquete `auditoria` (prioridad 3) hacia la Tierra; columna `nodo_origen`; índice por `ocurrido_en`; `auditoria.user_id` pasa a referencia lógica.
- **Usuarios y tokens:** nuevo rol `sistema` para los usuarios de los nodos (plan §9.1); middleware `UsuarioActivo`; un solo token PWA vigente por usuario; `expires_at` por token y `sanctum.expiration = null`; comando `identidad:token-nodo` y variables `NODO_REMOTO_URL` y `NODO_REMOTO_TOKEN`.
- **Permisos:** M01 tiene la lista completa de *Gates* del sistema (21 tras la revisión de M02) y los comparte con el frontend en `auth.permisos`.
- **Despliegue:** la ruta de registro se borra en el backend; contraseñas de los usuarios de prueba desde `SEED_PASSWORD_<ROL>` en los nodos desplegados; páginas temporales de inicio en el Sprint 0.

## 2026-10-15 · Reparto de las historias por roles fijos

- **Roles fijos:** backend, Julieth y Santiago; frontend, Jhon y Gabriela; Miguel mantiene el simulador, los reportes, el QA, la documentación y la demo (y es *Scrum Master*); Santiago sigue como *Product Owner* y responsable del despliegue.
- [Plan de sprints](scrum/plan-de-sprints.md): la columna *Responsable* se divide en **Backend**, **Frontend** y **Otros** en las 71 historias; nueva regla 7 y tabla [Roles del equipo](scrum/plan-de-sprints.md#roles-del-equipo).
- Pantallas que no tenían responsable de frontend ahora lo tienen: HU-02.03, HU-12.01, HU-12.03 y HU-15.02.
- HU-15.04 incluye a Santiago para el backend (token de la PWA). HU-13.04 pasa a Santiago (backend) y Gabriela (frontend), con Miguel en las fuentes de los protocolos.
- [Índice de módulos](modulos/README.md#1-índice-de-módulos) y la línea *Responsables* de cada ficha: reparto por capa, indicando qué historias hace cada persona cuando una capa se comparte.
- [05 §1.2](docs/propuesta/05-estructura-scrum.md#12-responsables-por-épica): épicas por rol fijo; Santiago lidera el backend de EP-10.

## 2026-10-15 · Módulos divididos por capa

- Cada módulo M01–M16 tiene ahora `README.md` (ficha, propósito, alcance, submódulos, capas e historias) y tres carpetas:
  - `backend/` — API, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest;
  - `frontend/` — páginas Inertia, *props* tipadas de cada página, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest;
  - `database/` — tablas, relaciones, consultas e índices, volumen, migraciones y seeders.
- Nuevas convenciones comunes en [módulos §3](modulos/README.md#convenciones-del-frontend) (*layouts*, componentes y *props* compartidas, refresco, accesibilidad) y §7 (claves foráneas solo dentro del módulo, migraciones, archivos, motor).
- El simulador mantiene una sola ficha: no tiene páginas ni tablas propias.
- Plan §10, regla 2: la solicitud a la IA incluye la ficha del módulo y el documento de la capa.

## 2026-10-15 · Historias para las brechas

- **9 historias nuevas** que cubren las 8 brechas de [módulos §9](modulos/README.md#9-brechas-resueltas): HU-05.07 (test de memoria), HU-07.05 (PDF del laboratorio), HU-08.03 (VOC, partículas y ruido), HU-10.03 (nutrición e hidratación), HU-10.04 (fuerza de prensión), HU-10.05 (glucosa), HU-11.03 (foto del síntoma), HU-13.04 (protocolos con fuente y validación) y HU-14.03 (reporte de misión).
- Plan de sprints: **71 historias y 251 puntos** (S2: 24/74, S3: 19/71, S4: 11/51). Las nuevas *Could* encabezan el plan de contingencia.
- Las reglas `AMB_*` nacen **inactivas y sin valor**: un umbral solo se activa con valor y fuente (nueva columna `fuente` en `umbrales`). La regla `GLUCOSA` se activa con la fuente de la Fase 1.
- Nuevo tipo de paquete `adjunto` (prioridad 3) para fotos y PDF. Actualizados 02, 04, 05, el plan y las fichas.

## 2026-10-15 · Fichas técnicas de los módulos

- Nueva carpeta [`modulos/`](modulos/README.md): índice con convenciones, propiedad de tablas, eventos y catálogo de códigos, y una ficha por módulo (M01–M16 y simulador).
- **Nuevo evento `LoteIngerido`** (M03): las lecturas de sensores se procesan por lote; un evento por lectura (~1 millón en el relleno histórico) saturaría la cola de cPanel. `ObservacionRegistrada` queda para los registros individuales. Actualizados `02 §6` y el plan (§9.3).
- **Correcciones para que la misión normal (E0) no genere falsos avisos** (actualizados `04` y `05`):
  - deterioro cognitivo: las rachas de z se cuentan en la **misma métrica** (antes, la peor de seis métricas daba ~15 % de sesiones con z ≥ 2 por puro ruido);
  - sueño simulado: ruido de 0,3 h (antes 0,5) y eficiencia Normal(91 %; 2,5 %);
  - tendencia de exámenes: además de 3 tomas que empeoran, el cambio total debe superar el 5 %.
- Regla de sueño unificada como `SUENO_MAL` ("< 6 h **o** eficiencia < 85 %" durante 3 noches), tal como dice 04 §8.
- Familias de reglas para que el aviso y el crítico de una misma variable sean **una sola alerta que escala** (E8).
- Brechas sin historia documentadas en [módulos §9](modulos/README.md#9-brechas-resueltas) para decisión del equipo (resueltas después con 9 historias).

## 2026-10-15 · Plan de sprints detallado

- Nuevo [`scrum/plan-de-sprints.md`](scrum/plan-de-sprints.md): las 62 historias asignadas a un único sprint, en orden de dependencias, con responsables, puntos, entregables, criterios de cierre, contratos compartidos y reglas para desarrollar con IA. Validado con un script (cobertura, orden, totales y contingencia).
- Correcciones de orden encontradas al planificar historia por historia:
  - la cola offline (HU-15.04) pasa al Sprint 2, porque HU-05.01 y HU-07.01 la necesitan;
  - HU-08.02 y HU-06.02 pasan al Sprint 3, porque necesitan el motor de reglas;
  - exámenes (HU-07.01, 07.03 y 07.04) pasan al Sprint 2 y los escenarios (HU-17.03) al Sprint 3, para poder probar las alertas.
- Dependencias añadidas en `05`: HU-05.01 y HU-07.01 → HU-15.04; HU-05.04 y HU-07.02 → HU-13.01 (gestor de alertas).

## 2026-10-15 · Todo el producto entra al MVP

- **Decisión del equipo:** construir el producto completo antes del evento (14 de noviembre): los 16 módulos, las 4 aplicaciones y los 9 escenarios de demo.
- M08 Ambiente pasa a ser parte del núcleo (épica *Must*); M09 Radiación, M10 Físico y metabólico, M14 Reportes y F4 Administración también entran al MVP.
- Los escenarios E1 (arritmia), E2 (evento solar) y E3 (CO₂ + cefaleas) pasan al MVP; se añadió la detección de irregularidades del ritmo a M04 y la historia HU-17.04 (series de ambiente y radiación).
- Plan de sprints redistribuido y **orden de recorte** definido como contingencia (primero *Could*, después *Should*; el núcleo no se recorta).
- Responsables reasignados: no queda ninguna épica sin dueño; se señaló la carga del backend clínico.
- Nueva dependencia técnica: `barryvdh/laravel-dompdf` para los reportes PDF.

## 2026-10-15 · Revisión integral y decisión de stack

### Decisiones
- **Stack oficial:** Laravel (PHP) + MySQL en el backend; Inertia.js + Vue 3 + Tailwind CSS v4 (PWA) en el frontend; Python para el simulador.
- **Calendario:** desarrollo del 16 de octubre al 13 de noviembre de 2026 (Sprint 0 corto + 4 sprints semanales); evento el 14 y 15 de noviembre.

### Estructura
- Los documentos se reorganizaron en `docs/investigacion/` y `docs/propuesta/`. El índice pasa al `README.md` de la raíz.
- Archivos renombrados:

  | Antes | Ahora |
  |---|---|
  | `proyecto-artemis.md` | `docs/investigacion/01-proyecto-space-apps.md` |
  | `h.md` | `docs/investigacion/02-monitorizacion-salud-fase1.md` |
  | `arquitectura-software-salud-astronautas.md` | `docs/investigacion/03-arquitectura-vision-fase2.md` |
  | `enfermedades_astronautas.md` | `docs/investigacion/04-enfermedades-resumen.md` |
  | `Informe Analítico sobre la Fisiopatología Espacial….MD` | `docs/investigacion/05-informe-fisiopatologia-hrp.md` |
  | `propuesta/0N-*.md` | `docs/propuesta/0N-*.md` |

- El prompt de trabajo y el índice anterior se movieron a `interno/` (no forman parte de la entrega).
- Nuevos: [glosario de siglas](docs/glosario.md) y este registro, que reúne las antiguas secciones "Notas de revisión".

### Correcciones de lógica
- **Umbrales según el contexto de actividad:** los umbrales de FC, FR y temperatura en reposo ya no se aplican durante el ejercicio, que antes disparaba alertas en la misión normal (E0).
- **Simulación del sueño** ajustada (duración base de 6,5–7,0 h y eficiencia de 90 % de media) para que E0 no genere avisos espurios; el escenario E4 sigue inyectando el mal sueño.
- **Deterioro cognitivo:** el pseudocódigo ahora exige z ≥ 3 en dos sesiones consecutivas (antes solo en la última) y aplica una σ mínima por métrica.
- **Política de línea base única:** línea base de referencia fija para las alertas y estadística móvil solo para tendencias y visualización.
- **Umbrales durante un apagón:** el CMO puede aplicar ajustes temporales a bordo, auditados y revisados después por el médico de vuelo.
- **Enlace simulado:** se aclara que, con cron por minutos, los retardos de la ISS y de la Luna se tratan como inmediatos.

### Coherencia entre documentos
- Retardo a Marte unificado en **~3–22 min por sentido** (antes convivían "4–24 min" y "22 min").
- La priorización ya no afirma coincidir con la Fase 1 en dosimetría y CO₂: se explica por qué quedan en la segunda entrega.
- Los escenarios E1, E2 y E3 se marcan como de segunda entrega; se añadió una historia para la detección de arritmias.
- Todas las épicas tienen responsable, incluido EP-16.
- El semáforo se activa en el sprint de analítica, no antes.
- Caso del trombo yugular: se detectó en una ecografía de investigación, no rutinaria.
- LOINC de la vitamina D: 62292-8 (25-OH total) en lugar de 1989-3 (solo D3).

### Contenido
- Contexto de Artemis III verificado (reestructuración de febrero de 2026, tripulación anunciada el 9 de junio de 2026).
- CHAPEA actualizado: la segunda misión comenzó el 19 de octubre de 2025 y termina el 31 de octubre de 2026.
- Mars500: se corrigió el recuento de tripulantes y se citó a Basner *et al.* (2013).
- Se eliminó la comparación de la osteopenia espacial con la hipofosfatasia.
- La referencia "Mehta *et al.*, 2019" se corrigió a **Rooney *et al.*, 2019** (primer autor real).
- Se añadieron DOI verificados a las referencias de la Fase 1 y una sección de referencias a `01-proyecto-space-apps.md`.

### Presentación
- Se eliminaron las referencias al prompt interno ("Entregable 6.1", "Req. 5", "como pide el reto").
- Historias de usuario: todas las desarrolladas tienen tareas; las que usaban "Como sistema" o "Como dispositivo" ahora tienen un rol humano.
- Terminología actualizada a la Guía Scrum 2020 y fechas en formato ISO.
- Se añadieron la Ley 1581 de 2012 (protección de datos), el uso de datos de la NASA y bocetos de pantallas.

### Pendientes del equipo
- Confirmar la escritura de los nombres del equipo.
- Completar con su fuente original estas afirmaciones, hoy sin referencia: la recuperación ósea "34,0 % / 46,8 %" (cohorte de 94), el metaanálisis de *Acta Astronautica* (2026) sobre intolerancia ortostática, el estudio de 2025 con 43 tripulantes (SANS), el artículo de *Sleep* (2022), los análisis de litiasis renal (*npj Microgravity*, 2022; OCHMO, 2025), Telsy (FCV) y MEDEA.
- Verificar con la fuente original del informe HRP: el rango de incertidumbre de carcinogénesis (400–1.500 %), las tasas de eventos psiquiátricos, los niveles 4 y 5 de ExMC y la LBNP de 20 mmHg.
- Revisar las tablas reconstruidas de la Fase 1 contra el documento original.

---

## 2026-10-12 · Primera organización de los documentos

Resumen de las antiguas secciones "Notas de revisión" de cada documento:

- **Formato:** los textos originales venían en bloques sin saltos de línea o con restos de la conversión desde LaTeX ("0G0G", "CO2CO2", "600600 mSv"…). Se convirtieron a Markdown con títulos numerados, índices, listas y tablas; las 11 tablas de la Fase 1 y la tabla del equipo se reconstruyeron.
- **Citas:** se eliminaron del texto los restos de nombres de sitios (nih, nature, nasa, "+2"…) y se crearon secciones de referencias.
- **Correcciones:** "Objetivos generales" → "Objetivo general"; "tres perdieron 2030 %" → "entre 20 % y 30 %"; "600 libras pie" → "600 libras-fuerza"; las cataratas afectan al cristalino, no a la retina; se unificó "microgravedad"; se moderó el lenguaje exagerado del informe HRP.
- **Redacción:** se quitaron las frases dirigidas a una sola persona y el voseo de la Fase 2.
- **Advertencia:** el estudio de Delp *et al.* (2016) se basa en solo 7 fallecimientos de astronautas del Apolo; sus razones de mortalidad deben interpretarse con cautela.
