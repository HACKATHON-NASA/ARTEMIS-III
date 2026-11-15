<?php

/*
|--------------------------------------------------------------------------
| Alertas · M5
|--------------------------------------------------------------------------
|
| Una medición en amarillo o en rojo crea su alerta solo si es reciente:
| así, cuando el simulador carga los 30 días de una misión, la historia no
| llena el centro de alertas de avisos viejos.
|
*/

return [
    'vital_max_age_hours' => 24,
];
