# 🧍 M3 · Cuerpo · Backend

> [← Backend](../README.md) · Código: `astrohealth/app/Modules/Cuerpo/` · Qué construir: [ficha de M3](../../../../documentacion/modulos/M3-cuerpo/README.md) · Plan: F4 de `documentacion/PLAN_BACKEND.md`

| Estado   | Fase | Responsable |
| -------- | :--: | ----------- |
| ✅ Hecho |  F4  | Backend     |

**Qué hace:** dice **cómo está cada zona del cuerpo** de un tripulante (con el último valor de cada indicador) y guarda las **notas clínicas**, el expediente privado del equipo médico.

---

## 1. Endpoints

| Método | Ruta                                     | Permiso                                                | Caso de uso           | Respuesta                                                           |
| ------ | ---------------------------------------- | ------------------------------------------------------ | --------------------- | ------------------------------------------------------------------- |
| GET    | `/api/v1/body-parts`                     | autenticado                                            | `ListBodyParts`       | 200 las 9 zonas con función, riesgo (NASA HRP) e indicadores        |
| GET    | `/api/v1/astronauts/{id}/body-status`    | Gate `health.read`                                     | `GetBodyStatus`       | 200 `{ heart: { status, values: [...] }, … }` (sin datos → `null`)  |
| GET    | `/api/v1/astronauts/{id}/clinical-notes` | `clinical_notes.manage` + Gate `health.read`           | `ListClinicalNotes`   | 200 de la más nueva a la más antigua                                |
| POST   | `/api/v1/astronauts/{id}/clinical-notes` | `clinical_notes.manage` + Gate `health.read`           | `AddClinicalNote`     | 201 · el autor sale de la sesión                                    |
| PATCH  | `/api/v1/clinical-notes/{id}/resolve`    | `clinical_notes.manage` (la misión, en el caso de uso) | `ResolveClinicalNote` | 200 · 409 si no tiene seguimiento pendiente · 403 otra misión · 404 |

## 2. Cómo decide el estado de cada zona

1. `MeasurementQuery::latestPerIndicator()` (M1) trae el último valor de cada indicador, con su color y su zona.
2. `Evaluator::bodyStatus()` (M2) decide el estado de cada zona: **el peor** de sus lecturas. M3 no calcula colores: el semáforo sigue en un solo lugar (P4, P5).
3. Las zonas sin indicadores (ojos, oído interno) o sin datos van con `status: null`.

## 3. Estructura de archivos

```
app/Modules/Cuerpo/
├── CuerpoServiceProvider.php · routes.php
├── Domain/Entidades/BodyPart · ClinicalNote   ·   Enums/NoteKind · ErrorCode   ·   Excepciones/NoteProblem
├── Application/ListBodyParts · GetBodyStatus · ListClinicalNotes · AddClinicalNote · ResolveClinicalNote · DTOs/BodyPartResult · ZoneResult
├── Infrastructure/Models/BodyPart · ClinicalNote
│                 Repositorios/BodyPart/{Find,Upsert}BodyPartRepository · ClinicalNote/{Find,Insert,Update}ClinicalNoteRepository · ClinicalNoteMapper
├── Http/Controllers/Api/ListBodyParts · ShowBodyStatus · ListClinicalNotes · StoreClinicalNote · ResolveClinicalNote
│        Requests/StoreClinicalNoteRequest · Resources/BodyPartResource · ClinicalNoteResource
├── Database/Migrations/ (body_parts · clinical_notes) · Seeders/BodyPartSeeder · DemoClinicalNotesSeeder
└── Tests/Feature/BodyApiTest · ClinicalNotesApiTest
```

## 4. Contratos y eventos

| Tipo | Nombre                              | Para qué                          |
| ---- | ----------------------------------- | --------------------------------- |
| Usa  | `IndicatorCatalog` (M1)             | Los indicadores de cada zona      |
| Usa  | `MeasurementQuery` (M1)             | El último valor de cada indicador |
| Usa  | `Evaluator::bodyStatus()` (M2)      | El estado de cada zona            |
| Usa  | `CurrentActor` (M0)                 | El autor de la nota               |
| Usa  | Gate `health.read` (M0, por nombre) | Resolver solo notas de su misión  |

## 5. Códigos de error

| Código                  | HTTP | Cuándo                                        |
| ----------------------- | :--: | --------------------------------------------- |
| `BODY_NOTE_NOT_FOUND`   | 404  | La nota no existe                             |
| `BODY_NOT_YOUR_PATIENT` | 403  | La nota es de un tripulante de otra misión    |
| `BODY_NOTE_NOT_PENDING` | 409  | La nota no tiene seguimiento o ya se resolvió |

## 6. Pruebas

| Prueba                 | Verifica                                                                                                                                                                                            |
| ---------------------- | --------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `BodyApiTest`          | 9 zonas con sus indicadores · cada zona toma el peor valor · zonas sin datos en `null` · permisos                                                                                                   |
| `ClinicalNotesApiTest` | El médico de la misión escribe y lee (el autor sale de la sesión) · la tripulación y el admin no ven las notas · un médico de otra misión no ve ni resuelve · se resuelve una sola vez · validación |

## 7. Cómo quedó hecho

- **Las notas guardan el nombre** de quien la escribió y de quien la resolvió (como una historia clínica en papel): si el usuario cambia o se borra, la nota no cambia.
- **Las notas no se editan ni se borran:** lo único que cambia es marcar el seguimiento como resuelto.
- **Datos de la demo** (solo fuera de producción): `DemoReadingsSeeder` (M1) guarda los últimos valores de Ares-1 pasando por el motor de M2, y `DemoClinicalNotesSeeder` carga las 4 notas de la historia. Los puntajes que calcula el backend coinciden con la demo (Elena 74, Kenji 90, Amara 87, Lucas 100).

> 📝 **Documentar siempre:** este documento se actualiza en el mismo _pull request_ que cambia el código.
