import type { Astronaut, Mission } from '../Types/missions';
import { shiftDate, today } from './missionDates';

/**
 * ⚠️ DATOS DE PRUEBA de las misiones (M6). Ares-1 es la del MissionSeeder
 * (Marte, 30 días, hoy es el día 24) con sus 4 astronautas; se suman una
 * misión terminada y una planificada para ver los tres estados. Se borran
 * cuando existan GET/POST /api/v1/missions y /api/v1/astronauts.
 */
export function mockMissions(): Mission[] {
    return [
        {
            id: 1,
            name: 'Ares-1',
            environment: 'mars',
            start_date: shiftDate(today(), -23),
            day_count: 30,
            objective:
                'Primera estancia larga en Marte: adaptación a 0,38 g y radiación.',
        },
        {
            id: 2,
            name: 'Selene-2',
            environment: 'moon',
            start_date: shiftDate(today(), 21),
            day_count: 14,
            objective: 'Base en el polo sur lunar: polvo, frío y radiación.',
        },
        {
            id: 3,
            name: 'Órbita-7',
            environment: 'iss',
            start_date: shiftDate(today(), -150),
            day_count: 90,
            objective: 'Rotación de la Estación Espacial: microgravedad.',
        },
    ];
}

export function mockAstronauts(): Astronaut[] {
    return [
        { id: 1, name: 'Elena Vargas', crew_role: 'Comandante', mission_id: 1 },
        {
            id: 2,
            name: 'Kenji Mori',
            crew_role: 'Ingeniero de vuelo',
            mission_id: 1,
        },
        {
            id: 3,
            name: 'Amara Okafor',
            crew_role: 'Especialista de misión',
            mission_id: 1,
        },
        {
            id: 4,
            name: 'Lucas Ferreira',
            crew_role: 'Médico de la tripulación',
            mission_id: 1,
        },
        {
            id: 5,
            name: 'Marco Bellini',
            crew_role: 'Comandante',
            mission_id: 3,
        },
        {
            id: 6,
            name: 'Ana Sato',
            crew_role: 'Ingeniero de vuelo',
            mission_id: 3,
        },
    ];
}
