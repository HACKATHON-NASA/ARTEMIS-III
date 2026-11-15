<?php

/*
|--------------------------------------------------------------------------
| Motor de evaluación · M2 Evaluación
|--------------------------------------------------------------------------
|
| Los números del puntaje y de las tendencias (D14: nunca en el código).
| Los rangos de cada indicador viven en la tabla `thresholds` (los cambian
| los médicos de vuelo desde la Guía de salud).
|
| Referencia: plan §2. Herramienta de apoyo, no reemplaza el criterio médico.
|
*/

return [
    // Puntos de cada color para el puntaje 0–100.
    'points' => [
        'green' => 100,
        'yellow' => 60,
        'red' => 20,
    ],

    // Peso de cada indicador en el promedio (los que no están pesan 1).
    'weights' => [
        'hr' => 1.5,
        'bp_sys' => 1.5,
        'bp_dia' => 1.5,
        'spo2' => 1.5,
        'radiation' => 1.5,
    ],

    // Puntaje → estado general: desde 80 verde, desde 50 amarillo, si no rojo.
    'overall' => [
        'green' => 80,
        'yellow' => 50,
    ],

    // Tendencia: cuántos valores mira y cuántas veces seguidas debe empeorar
    // (alejarse del centro del verde) para avisar.
    'trend' => [
        'window' => 7,
        'worsening_steps' => 3,
        // Cambio por medición, en % del ancho del verde, que ya cuenta como subida o bajada.
        'stable_band_pct' => 2.0,
    ],

    // Nombre de cada zona del cuerpo para el resumen ("Corazón en precaución").
    'zones' => [
        'heart' => 'Corazón',
        'brain' => 'Mente y sueño',
        'eyes' => 'Ojos',
        'lungs' => 'Pulmones',
        'bones' => 'Huesos y músculos',
        'ear' => 'Oído interno',
        'immune' => 'Sistema inmune',
        'metabolism' => 'Metabolismo',
        'whole_body' => 'Todo el cuerpo',
    ],
];
