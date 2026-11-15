<?php

/*
|--------------------------------------------------------------------------
| Permisos por rol · M0 Identidad
|--------------------------------------------------------------------------
|
| La ÚNICA fuente de permisos. De aquí salen los Gates (can:<permiso> en las
| rutas) y la lista "permissions" de GET /api/v1/auth/me.
|
| Roles: astronaut y crew_medical_officer (nave) · flight_surgeon y admin
| (Tierra) · device (el simulador / los sensores).
|
*/

return [
    'crew.read' => ['admin', 'flight_surgeon', 'crew_medical_officer', 'astronaut'],
    'health.read.any' => ['admin', 'flight_surgeon', 'crew_medical_officer'],
    'health.read.own' => ['crew_medical_officer', 'astronaut'],
    'measurements.write.own' => ['crew_medical_officer', 'astronaut'],
    'measurements.write.crew' => ['crew_medical_officer'],
    'measurements.ingest' => ['device'],
    'alerts.read' => ['admin', 'flight_surgeon', 'crew_medical_officer', 'astronaut'],
    'alerts.send' => ['crew_medical_officer', 'astronaut'],
    'alerts.acknowledge' => ['admin', 'flight_surgeon'],
    'mission.update' => ['admin', 'flight_surgeon'],
    'missions.manage' => ['admin'],
    'reminders.manage.own' => ['crew_medical_officer', 'astronaut'],
    'gamification.read' => ['admin', 'flight_surgeon', 'crew_medical_officer', 'astronaut'],
    'users.manage' => ['admin'],
    'devices.manage' => ['admin'],
];
