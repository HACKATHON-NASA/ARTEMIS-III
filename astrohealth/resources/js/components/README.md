# resources/js/components · Componentes globales

| Qué | Dónde |
|---|---|
| Botones, campos, diálogos, menús, tablas, pestañas… (shadcn-vue) | `ui/` (del *kit*; los que falten se añaden con su herramienta) |
| Los 6 componentes de dominio compartidos | Aquí: `EstadoVacio`, `Cargando`, `Semaforo`, `TarjetaSigno`, `InsigniaAlerta`, `GraficaSerie` ([HU-00.03](../../../../documentacion/scrum/fundaciones/HU-00.03-libreria-de-componentes.md)) |
| Componentes que usa **más de un** módulo | Aquí (`DiaMision`, `EstadoSensores`, `IndicadorSincronizacion`…) |
| Componentes del *kit* (`AppSidebar`, `NavUser`…) | Aquí; se adaptan en HU-01.01 |

Un componente de **un solo** módulo va en `resources/js/modules/<Modulo>/Components/`.

> 📝 **Documentar siempre:** si cambia lo que hace esta carpeta o añade un tipo de archivo nuevo, actualice este README y el CHANGELOG en el mismo *pull request*.
