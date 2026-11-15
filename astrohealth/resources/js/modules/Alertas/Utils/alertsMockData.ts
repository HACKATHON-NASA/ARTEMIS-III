import type { Alert, Recommendation } from '../Types/alerts';

/**
 * ⚠️ DATOS DE PRUEBA de M5 · Alertas: el historial de la tripulación de
 * Ares-1, coherente con @/mocks/crewHealth (la presión de Elena es la alerta
 * roja de la ficha). Se guardan en memoria para que enviar o confirmar se
 * mantenga mientras no se recargue la página. Se borran cuando existan las
 * rutas de M5.
 */
const MINUTE = 60_000;
const DELAY_SECONDS = 1200;

function at(minutesAgo: number): string {
    return new Date(Date.now() - minutesAgo * MINUTE).toISOString();
}

type Seed = {
    id: number;
    astronaut_id: number;
    indicator: Alert['indicator'];
    level: Alert['level'];
    message: string;
    created: number;
    sent?: number;
    acknowledged?: number;
};

const SEEDS: Seed[] = [
    {
        id: 57,
        astronaut_id: 1,
        indicator: 'bp_sys',
        level: 'red',
        message: 'Presión sistólica 148 mmHg (crítico)',
        created: 6,
    },
    {
        id: 55,
        astronaut_id: 3,
        indicator: 'spo2',
        level: 'yellow',
        message: 'Saturación de oxígeno 93 % (precaución)',
        created: 70,
        sent: 65,
    },
    {
        id: 52,
        astronaut_id: 1,
        indicator: 'sleep',
        level: 'yellow',
        message: 'Sueño 6,2 h (precaución)',
        created: 60 * 9,
        sent: 60 * 9 - 4,
        acknowledged: 60 * 8,
    },
    {
        id: 49,
        astronaut_id: 2,
        indicator: 'mood',
        level: 'yellow',
        message: 'Estado de ánimo 3 de 5 (precaución)',
        created: 60 * 30,
        sent: 60 * 30 - 10,
        acknowledged: 60 * 29,
    },
    {
        id: 44,
        astronaut_id: 3,
        indicator: 'radiation',
        level: 'yellow',
        message: 'Radiación diaria 0,94 mSv (precaución)',
        created: 60 * 50,
        sent: 60 * 50 - 3,
        acknowledged: 60 * 49,
    },
    {
        id: 38,
        astronaut_id: 1,
        indicator: 'exercise_min',
        level: 'yellow',
        message: 'Ejercicio 70 min (precaución)',
        created: 60 * 98,
        sent: 60 * 98 - 6,
        acknowledged: 60 * 97,
    },
];

function build(seed: Seed): Alert {
    const sentAt = seed.sent === undefined ? null : at(seed.sent);

    return {
        id: seed.id,
        astronaut_id: seed.astronaut_id,
        indicator: seed.indicator,
        level: seed.level,
        message: seed.message,
        sent_to_ground: sentAt !== null,
        sent_at: sentAt,
        ground_eta_at:
            sentAt === null
                ? null
                : new Date(
                      new Date(sentAt).getTime() + DELAY_SECONDS * 1000,
                  ).toISOString(),
        delay_seconds: DELAY_SECONDS,
        acknowledged_at:
            seed.acknowledged === undefined ? null : at(seed.acknowledged),
        created_at: at(seed.created),
    };
}

/** La "base de datos" en memoria (se crea una vez por carga de la página). */
let database: Alert[] | null = null;

function all(): Alert[] {
    database ??= SEEDS.map(build);

    return database;
}

/** Las alertas de un astronauta, o de toda la tripulación con `null`. Más reciente primero. */
export function mockAlerts(astronautId: number | null): Alert[] {
    return all()
        .filter(
            (alert) =>
                astronautId === null || alert.astronaut_id === astronautId,
        )
        .sort((a, b) => b.created_at.localeCompare(a.created_at));
}

/** Busca una alerta en la base en memoria (para enviar o confirmar). */
export function mockFindAlert(id: number): Alert | undefined {
    return all().find((alert) => alert.id === id);
}

export const MOCK_RECOMMENDATIONS: Recommendation[] = [
    {
        indicator: 'bp_sys',
        status: 'red',
        title: 'Presión alta',
        action_text:
            'Reposa 10 minutos, sentado y sin hablar\nHidrátate: un vaso de agua\nVuelve a medir la presión\nSi sigue en rojo, envía la alerta a Tierra',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'bp_dia',
        status: 'red',
        title: 'Presión alta',
        action_text:
            'Reposa 10 minutos, sentado y sin hablar\nHidrátate: un vaso de agua\nVuelve a medir la presión',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'sleep',
        status: 'yellow',
        title: 'Poco sueño',
        action_text:
            'Apaga pantallas 1 hora antes de dormir\nUsa el antifaz y los tapones\nMantén el mismo horario de descanso',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'exercise_min',
        status: 'yellow',
        title: 'Falta ejercicio',
        action_text:
            'Completa la sesión de resistencia de hoy\nNo bajes de 90 minutos diarios\nRegistra el tiempo al terminar',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'spo2',
        status: 'yellow',
        title: 'Oxígeno algo bajo',
        action_text:
            'Siéntate y respira despacio por la nariz 2 minutos\nRevisa el sensor del oxímetro y vuelve a medir\nSi baja de 90 %, avisa al médico de la tripulación',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'radiation',
        status: 'yellow',
        title: 'Dosis de radiación alta',
        action_text:
            'Reduce el tiempo en los módulos menos blindados\nRevisa el pronóstico de clima espacial\nLee el dosímetro al final del día',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
    {
        indicator: 'glucose',
        status: 'yellow',
        title: 'Glucosa en precaución',
        action_text:
            'Mide de nuevo en ayunas mañana\nRevisa la ración de carbohidratos del día\nMantén el ejercicio de resistencia',
        risk_ref: 'https://www.nasa.gov/hrp/risks/',
    },
    {
        indicator: 'mood',
        status: 'yellow',
        title: 'Ánimo bajo',
        action_text:
            'Habla con alguien de la tripulación o de Tierra\nToma un descanso fuera de tu estación de trabajo\nRegistra cómo te sientes al final del día',
        risk_ref: 'https://www.nasa.gov/hrp/hazards/',
    },
];

/** Alertas sin confirmar de un astronauta, o de toda la tripulación con `null`. */
export function mockPendingTotal(astronautId: number | null): number {
    return mockAlerts(astronautId).filter(
        (alert) => alert.acknowledged_at === null,
    ).length;
}

const NAMES: Partial<Record<Alert['indicator'], string>> = {
    hr: 'Frecuencia cardíaca',
    bp_sys: 'Presión sistólica',
    bp_dia: 'Presión diastólica',
    glucose: 'Glucosa',
    temp: 'Temperatura',
    spo2: 'Saturación de oxígeno',
    sleep: 'Sueño',
    mood: 'Estado de ánimo',
    radiation: 'Radiación diaria',
    exercise_min: 'Ejercicio',
};

/**
 * Lo que hará el backend al escuchar MeasurementsRecorded (M1 → M5): cada
 * valor en amarillo o rojo crea su alerta. Devuelve las creadas.
 */
export function mockAlertsFromMeasurements(
    astronautId: number,
    measurements: {
        indicator: Alert['indicator'];
        value: number;
        unit: string;
        status: 'green' | 'yellow' | 'red';
    }[],
): Alert[] {
    const created: Alert[] = [];

    for (const item of measurements) {
        if (item.status === 'green') {
            continue;
        }

        const value = item.value.toLocaleString('es');
        const unit = item.unit === '1-5' ? 'de 5' : item.unit;
        const label = item.status === 'red' ? 'crítico' : 'precaución';
        const alert = build({
            id: Math.max(...all().map((other) => other.id)) + 1,
            astronaut_id: astronautId,
            indicator: item.indicator,
            level: item.status,
            message: `${NAMES[item.indicator]} ${value} ${unit} (${label})`,
            created: 0,
        });

        all().push(alert);
        created.push(alert);
    }

    return created;
}
