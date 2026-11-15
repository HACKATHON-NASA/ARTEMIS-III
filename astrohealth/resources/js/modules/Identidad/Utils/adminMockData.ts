import type {
    ManagedDevice,
    ManagedUser,
    MissionSummary,
} from '../Types/admin';
import { minutesAgo } from './time';

/**
 * ⚠️ DATOS DE PRUEBA de la vista de administración.
 *
 * Imitan lo que dejan los seeders (Ares-1, 4 tripulantes, un usuario por rol
 * y el simulador) más algunos casos para ver todos los estados en pantalla.
 * Se borran cuando existan GET /api/v1/users y GET /api/v1/devices.
 */

export const MOCK_MISSION: MissionSummary = {
    id: 1,
    name: 'Ares-1',
    environment: 'mars',
    environment_label: 'Rumbo a Marte',
    day: 24,
    day_count: 30,
};

export function mockUsers(): ManagedUser[] {
    return [
        {
            id: 1,
            name: 'Administración AstroHealth',
            email: 'admin@astrohealth.test',
            role: 'admin',
            astronaut_id: null,
            astronaut_name: null,
            mission_id: null,
            mission_name: null,
            crew_role: null,
            active: true,
            last_login_at: minutesAgo(1),
        },
        {
            id: 2,
            name: 'Dra. Sofía Ramírez',
            email: 'tierra@astrohealth.test',
            role: 'flight_surgeon',
            astronaut_id: null,
            astronaut_name: null,
            mission_id: 1,
            mission_name: 'Ares-1',
            crew_role: null,
            active: true,
            last_login_at: minutesAgo(42),
        },
        {
            id: 3,
            name: 'Elena Vargas',
            email: 'astro1@astrohealth.test',
            role: 'astronaut',
            astronaut_id: 1,
            astronaut_name: 'Elena Vargas',
            mission_id: 1,
            mission_name: 'Ares-1',
            crew_role: 'Comandante',
            active: true,
            last_login_at: minutesAgo(15),
        },
        {
            id: 4,
            name: 'Kenji Mori',
            email: 'astro2@astrohealth.test',
            role: 'astronaut',
            astronaut_id: 2,
            astronaut_name: 'Kenji Mori',
            mission_id: 1,
            mission_name: 'Ares-1',
            crew_role: 'Ingeniero de vuelo',
            active: true,
            last_login_at: minutesAgo(180),
        },
        {
            id: 5,
            name: 'Amara Okafor',
            email: 'astro3@astrohealth.test',
            role: 'astronaut',
            astronaut_id: 3,
            astronaut_name: 'Amara Okafor',
            mission_id: 1,
            mission_name: 'Ares-1',
            crew_role: 'Especialista de misión',
            active: true,
            last_login_at: minutesAgo(60 * 26),
        },
        {
            id: 6,
            name: 'Lucas Ferreira',
            email: 'cmo@astrohealth.test',
            role: 'crew_medical_officer',
            astronaut_id: 4,
            astronaut_name: 'Lucas Ferreira',
            mission_id: 1,
            mission_name: 'Ares-1',
            crew_role: 'Médico de la tripulación',
            active: true,
            last_login_at: minutesAgo(8),
        },
        {
            id: 7,
            name: 'Dr. Tomás Rivas',
            email: 'trivas@astrohealth.test',
            role: 'flight_surgeon',
            astronaut_id: null,
            astronaut_name: null,
            mission_id: 3,
            mission_name: 'Órbita-7',
            crew_role: null,
            active: false,
            last_login_at: minutesAgo(60 * 24 * 9),
        },
        {
            id: 8,
            name: 'Valentina Cruz',
            email: 'vcruz@astrohealth.test',
            role: null,
            astronaut_id: null,
            astronaut_name: null,
            mission_id: null,
            mission_name: null,
            crew_role: null,
            active: true,
            last_login_at: null,
        },
    ];
}

export function mockDevices(): ManagedDevice[] {
    return [
        {
            id: 1,
            name: 'Simulador Ares-1',
            client_id: 'sim-ares-1',
            mission_id: 1,
            astronaut_id: null,
            astronaut_name: null,
            active: true,
            last_seen_at: minutesAgo(0.5),
        },
        {
            id: 2,
            name: 'Pulsera biométrica',
            client_id: 'dev-k7m2xq9rwa1c',
            mission_id: 1,
            astronaut_id: 2,
            astronaut_name: 'Kenji Mori',
            active: true,
            last_seen_at: minutesAgo(2),
        },
        {
            id: 3,
            name: 'Dosímetro personal',
            client_id: 'dev-p3n8zt5yhd0e',
            mission_id: 1,
            astronaut_id: 3,
            astronaut_name: 'Amara Okafor',
            active: true,
            last_seen_at: minutesAgo(190),
        },
        {
            id: 4,
            name: 'Oxímetro de reserva',
            client_id: 'dev-b1v6cf4uql2s',
            mission_id: 1,
            astronaut_id: null,
            astronaut_name: null,
            active: false,
            last_seen_at: minutesAgo(60 * 24 * 4),
        },
    ];
}

/** Alertas que esperan confirmación desde Tierra (M5, aún sin API). */
export const MOCK_PENDING_ALERTS = 2;
