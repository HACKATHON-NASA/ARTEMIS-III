# resources/js/modules · Frontend por módulo

Cada módulo del backend tiene aquí su parte visual, con el **mismo nombre de carpeta** ([ARCHITECTURE §16](../../../../documentacion/modulos/00-arquitectura/ARCHITECTURE.md#16-modularización-del-frontend)).

| Código | Carpeta | Módulo |
|---|---|---|
| M01 | [`Identidad/`](Identidad/README.md) | Identidad y acceso |
| M02 | [`Mision/`](Mision/README.md) | Misión y tripulación |
| M03 | [`Ingesta/`](Ingesta/README.md) | Ingesta de datos |
| M04 | [`SignosVitales/`](SignosVitales/README.md) | Signos vitales |
| M05 | [`Psicologia/`](Psicologia/README.md) | Psicología y cognición |
| M06 | [`Sueno/`](Sueno/README.md) | Sueño |
| M07 | [`Examenes/`](Examenes/README.md) | Exámenes médicos |
| M08 | [`Ambiente/`](Ambiente/README.md) | Ambiente |
| M09 | [`Radiacion/`](Radiacion/README.md) | Radiación |
| M10 | [`FisicoMetabolico/`](FisicoMetabolico/README.md) | Físico y metabólico |
| M11 | [`Sintomas/`](Sintomas/README.md) | Síntomas |
| M12 | [`Analitica/`](Analitica/README.md) | Analítica y línea base |
| M13 | [`Alertas/`](Alertas/README.md) | Alertas y casos clínicos |
| M14 | [`Reportes/`](Reportes/README.md) | Reportes |
| M15 | [`Sincronizacion/`](Sincronizacion/README.md) | Sincronización |
| M16 | [`Configuracion/`](Configuracion/README.md) | Configuración |

```
<Modulo>/
├── Pages/        # Páginas Inertia con el nombre de la ficha ('Admin/Usuarios/Index')
├── Components/   # Componentes solo de este módulo
├── Composables/  # use<Algo>.ts del módulo
├── Types/        # Tipos de las props de sus páginas
└── Utils/        # Funciones puras del módulo
```

**Reglas:** un módulo **no importa** nada de otro módulo; lo compartido va en las carpetas globales de `resources/js/`. Ejemplo completo: [M01 · Identidad](Identidad/README.md).

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
