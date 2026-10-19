# Herramientas de revisión

> [← Volver al inicio](../README.md)

Scripts en Python que se usaron para revisar la documentación. No forman parte del producto; sirven para volver a comprobar la documentación después de cada cambio. Solo usan la biblioteca estándar y NumPy.

## Comprobaciones de la documentación

| Script | Qué comprueba | Uso |
|---|---|---|
| [`revisar_enlaces.py`](revisar_enlaces.py) | Que todos los enlaces internos y anclas de los `.md` existan | `python revisar_enlaces.py ..` |
| [`revisar_historias.py`](revisar_historias.py) | Que las 71 historias coincidan entre el backlog (05) y el plan de sprints, que ninguna dependa de otra posterior, que los sprints de las fichas coincidan con el plan y que la contingencia recorte las prioridades correctas | `python revisar_historias.py ..` |

Se ejecutan desde esta carpeta y reciben como argumento la raíz del proyecto (`..`). Los únicos enlaces rotos esperados son los de `interno/README-propuesta-anterior.md`, que es el índice antiguo.

## Simulaciones de Monte Carlo (falsas alarmas en E0)

Cada script simula miles de misiones normales (E0) con los parámetros del [simulador](../modulos/simulador/README.md) y mide con qué frecuencia una regla da un aviso falso.

| Script | Pregunta | Resultado que justificó el cambio |
|---|---|---|
| [`montecarlo/e0_falsos_avisos.py`](montecarlo/e0_falsos_avisos.py) | Con las reglas **originales**, ¿cuántas misiones normales dan un aviso falso? | Aritmética 22–38 %, Hb 16 %, sueño 0,2 % |
| [`montecarlo/sigma_minima_aritmetica.py`](montecarlo/sigma_minima_aritmetica.py) | ¿Qué σ mínima de la precisión elimina los falsos sin perder detección? | 7,5 puntos: 0,1 % de falsos y 95 % de detección ([M05](../modulos/M05-psicologia/backend/README.md#32-detección-de-deterioro-regladeteriorocognitivo)) |
| [`montecarlo/tendencia_hemoglobina.py`](montecarlo/tendencia_hemoglobina.py) | ¿Qué regla de tendencia de Hb evita los falsos y detecta E7? | Cambio > 7,5 %: 0,4 % de falsos y 99,8 % de detección ([M07](../modulos/M07-examenes/backend/README.md#3-reglas-de-negocio)) |
| [`montecarlo/cognitivo_todas_las_reglas.py`](montecarlo/cognitivo_todas_las_reglas.py) | ¿`COG_DETERIORO` con **todas** sus reglas y las siete métricas da falsos en E0 y detecta E4? | Regla original ~97 % de falsos (por la pendiente); corregida (sin pendiente como disparador, σ mínima relativa del 10 %, E4 +8 %/día): 0,8 % de falsos y 95 % de detección ([M05](../modulos/M05-psicologia/backend/HU/HU-05.04-detectar-un-deterioro-cognitivo-sostenido.md)) |
| [`montecarlo/tendencia_hematocrito.py`](montecarlo/tendencia_hematocrito.py) | Si el Hto se deriva de la Hb, ¿cuánto ruido propio admite sin falsas tendencias? | Ruido 0,6 → 8,4 % de falsos; 0,1 → Hb o Hto ~0,6 % ([M07](../modulos/M07-examenes/backend/HU/HU-07.02-marcar-valores-fuera-de-rango.md)) |
| [`montecarlo/fc_durante_el_sueno.py`](montecarlo/fc_durante_el_sueno.py) | ¿La regla de cambio de FC da falsos durante el sueño con el simulador actual? | 0 %, pero la regla se corrigió igual para datos reales ([M12](../modulos/M12-analitica/backend/README.md#31-política-de-línea-base)) |

**Regla del equipo:** si se cambia un parámetro del simulador o un umbral clínico, se vuelve a correr la simulación que corresponda antes de fusionar el cambio. Cuando exista el repositorio del simulador, estos scripts pasan a `simulator/tests/montecarlo/` y se ejecutan en CI.
