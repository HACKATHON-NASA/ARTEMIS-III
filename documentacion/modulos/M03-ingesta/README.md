# M03 · Ingesta de datos

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M03 |
| **Grupo** | Núcleo |
| **Carpeta Laravel** | `astrohealth/app/Modules/Ingesta/` |
| **Épica** | EP-03 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon (HU-03.03) |
| **Sprints** | 1 (HU-03.01, HU-03.02) · 2 (HU-03.03) |
| **Escenarios** | E6 (sensor suelto); alimenta a todos los demás |

## 2. Propósito

Ser **la única puerta de entrada de las lecturas de sensores** (reales o simuladas). Recibe lotes, los valida, descarta lo imposible, marca lo dudoso, normaliza unidades y códigos, guarda las observaciones sin duplicarlas y avisa al resto del sistema con un evento por lote.

## 3. Alcance

| Hace | No hace |
|---|---|
| Recibir lotes de lecturas con token de dispositivo (nunca con sesión) | Recibir registros de formularios (tests, síntomas, exámenes: los recibe cada módulo) |
| Idempotencia por lote y por lectura | Evaluar reglas clínicas (M12) |
| Validar esquema, unidad, rango posible y marca de tiempo | Calcular agregados (M04) |
| Marcar artefactos por calidad < 0,5 | Borrar lecturas |
| Avisar al astronauta si un sensor parece mal colocado (solo con datos en vivo) | Crear alertas clínicas (M13) |
| Importar en el nodo Tierra los lotes ya validados por la nave | Volver a validar en la Tierra lo que validó la nave |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Recepción | `POST /api/v1/ingesta/lotes` con clave de idempotencia |
| Validación | Formato, unidad, rango físicamente posible, marca de tiempo |
| Calidad de señal | Marca artefactos y detecta sensores mal colocados |
| Normalización | Conversión de unidades a UCUM y verificación del código en el catálogo |
| Enrutamiento | Publica `LoteIngerido` con los códigos del lote, para que cada módulo tome lo suyo |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md) (solo HU-03.03 tiene pantalla). La importación en la Tierra (parte B de HU-03.01) se construye dentro de HU-15.02, en el Sprint 4.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-03.01 Envío de lecturas por lotes | 1 | 30 días del simulador cargados; repetir el envío (también a la vez) no duplica ni da error; prueba de carga en cPanel hecha al cierre del Sprint 1 |
| HU-03.02 Rechazar imposibles y marcar baja calidad | 1 | FC de 400 rechazada; calidad 0,3 guardada como artefacto |
| HU-03.03 Aviso de sensor mal colocado | 2 | **E6 pasa:** aviso en F1 y ninguna alerta clínica; el relleno histórico no genera avisos |
