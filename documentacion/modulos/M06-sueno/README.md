# M06 · Sueño

> [← Índice de módulos](../README.md) · [Inicio](../../README.md)

## 1. Ficha

| | |
|---|---|
| **Código** | M06 |
| **Grupo** | Clínico |
| **Carpeta Laravel** | `astrohealth/app/Modules/Sueno/` |
| **Épica** | EP-06 |
| **Responsables** | **Backend:** Julieth · **Frontend:** Jhon |
| **Sprints** | 2 (HU-06.01, HU-06.03) · 3 (HU-06.02) |
| **Escenarios** | E4 (fatiga acumulada: 5 noches de mal sueño); E0 (sin avisos espurios) |
| **Afecciones** | Privación de sueño y desajuste circadiano ([01 §3.7](../../docs/propuesta/01-analisis-enfermedades.md#37-sueño-y-ritmos-circadianos)) |

## 2. Propósito

Registrar **cuánto, qué tan bien y a qué hora duerme** cada tripulante, con el uso de medicación, y entregar a M12 la duración y la eficiencia de cada noche para detectar la privación de sueño acumulada.

## 3. Alcance

| Hace | No hace |
|---|---|
| Un registro por noche (actigrafía simulada o entrada manual); la noche la define la hora de despertar, aunque el sueño empiece después de medianoche | Estadios de sueño (REM, profundo): evolución con ML |
| Medicación para dormir, luz y ruido percibidos | Recomendar fármacos |
| Regularidad de horarios (variación de la hora de acostarse) | Evaluar el umbral de mal sueño (M12) |

## 4. Submódulos

| Submódulo | Responsabilidad |
|---|---|
| Registro de sueño | Duración, eficiencia y horarios por noche |
| Higiene del sueño | Medicación, luz y ruido percibidos |
| Regularidad circadiana | Desviación estándar de la hora de acostarse en 7 noches |

## 5. Documentación por capa

| Capa | Documento | Contenido |
|---|---|---|
| Backend (Laravel) | [backend/README.md](backend/README.md) | API REST, eventos, reglas de negocio, contratos, permisos, archivos PHP y pruebas Pest |
| Frontend (Inertia + Vue) | [frontend/README.md](frontend/README.md) | Páginas, *props*, componentes, comportamiento sin conexión, accesibilidad y pruebas Vitest |
| Base de datos (MySQL) | [database/README.md](database/README.md) | Tablas, columnas, índices, relaciones, migraciones y seeders |

**Historias por capa** (una historia por archivo): [base de datos](database/HU/README.md) · [backend](backend/HU/README.md) · [frontend](frontend/HU/README.md) (HU-06.02 no tiene pantalla: el aviso lo muestra M13). HU-06.01 recibe las noches del actígrafo y del simulador; HU-06.03 añade el registro manual y la fusión.

## 6. Historias

| Historia | Sprint | Resultado esperado |
|---|:---:|---|
| HU-06.01 Ver cuánto y qué tan bien dormí | 2 | F1 muestra la noche anterior, la tendencia y la regularidad |
| HU-06.03 Registrar la medicación para dormir | 2 | El registro manual guarda la medicación y se combina con la actigrafía |
| HU-06.02 Aviso por noches de mal sueño | 3 | 3 noches seguidas por debajo del umbral → aviso al CMO; E0 sin avisos |
