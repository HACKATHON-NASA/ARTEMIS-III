<?php

/*
|--------------------------------------------------------------------------
| Entornos de misión · M6 Entorno
|--------------------------------------------------------------------------
|
| La ÚNICA fuente de los números de cada entorno (D14): gravedad, radiación
| diaria de referencia y retraso de la comunicación con Tierra (un sentido).
| Los mismos que usa el simulador. Los riesgos son códigos; el frontend pone
| el texto y el ícono de cada uno (basados en la NASA HRP).
|
| Riesgos: microgravity · fluids · radiation · isolation · distance · dust · stress
|
*/

return [
    'iss' => [
        'name' => 'Estación Espacial',
        'label' => 'En órbita terrestre',
        'gravity_g' => 0.0,
        'radiation_msv_day' => 0.5,
        'ground_delay_seconds' => 1.0,
        'risks' => ['microgravity', 'stress', 'isolation'],
    ],
    'transit' => [
        'name' => 'Tránsito',
        'label' => 'En tránsito',
        'gravity_g' => 0.0,
        'radiation_msv_day' => 1.8,
        'ground_delay_seconds' => 300.0,
        'risks' => ['radiation', 'microgravity', 'isolation', 'distance'],
    ],
    'moon' => [
        'name' => 'Luna',
        'label' => 'En la Luna',
        'gravity_g' => 0.166,
        'radiation_msv_day' => 1.4,
        'ground_delay_seconds' => 1.3,
        'risks' => ['radiation', 'dust', 'fluids'],
    ],
    'mars' => [
        'name' => 'Marte',
        'label' => 'Rumbo a Marte',
        'gravity_g' => 0.38,
        'radiation_msv_day' => 0.6,
        'ground_delay_seconds' => 1200.0,
        'risks' => ['distance', 'radiation', 'isolation', 'fluids'],
    ],
];
