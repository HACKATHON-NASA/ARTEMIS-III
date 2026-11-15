<?php

/*
| M8 · Recordatorios: la rutina con la que empieza cada tripulante
| (ReminderSeeder). Cada uno puede cambiarla después.
*/

return [
    'defaults' => [
        ['type' => 'measure_bp', 'title' => 'Hora de medir tu presión 🩺', 'time' => '08:00'],
        ['type' => 'exercise', 'title' => 'Ejercicio de resistencia: 2 h 🏋️', 'time' => '10:00'],
        ['type' => 'dosimeter', 'title' => 'Lee tu dosímetro de radiación ☢️', 'time' => '12:00'],
        ['type' => 'mood_check', 'title' => '¿Cómo te sientes hoy? 🙂', 'time' => '18:00'],
        ['type' => 'log_sleep', 'title' => 'Registra cómo dormiste 😴', 'time' => '21:00'],
    ],
];
