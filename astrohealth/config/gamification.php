<?php

/*
|--------------------------------------------------------------------------
| Gamificación · M7
|--------------------------------------------------------------------------
|
| Cuántos puntos da cada acción y cuántos puntos tiene un nivel (D14: los
| números en la configuración, no en el código). Las insignias (con su bono
| y su meta) están en la tabla `achievements`.
|
*/

return [
    'points' => [
        'daily_complete' => 10, // registrar los 10 indicadores del día
        'streak_7' => 50,       // cada 7 días seguidos con el registro completo
        'sleep_7h' => 5,        // dormir 7 h o más
        'exercise' => 15,       // 90 min de ejercicio o más
        'reminder' => 5,        // cumplir un recordatorio
        'psych_test' => 10,     // hacer un test psicológico
    ],

    // Lo que tiene que medir cada cosa para dar puntos.
    'sleep_hours' => 7,
    'exercise_minutes' => 90,

    // Nivel = puntos / tamaño + 1; la barra muestra el resto.
    'level_size' => 100,
];
