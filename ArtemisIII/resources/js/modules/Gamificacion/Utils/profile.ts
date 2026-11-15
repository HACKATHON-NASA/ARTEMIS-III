import { mockSeries } from '@/mocks/crewHistory';
import type {
    Badge,
    BadgeCode,
    GamificationProfile,
    PointEntry,
} from '../Types/gamification';
import { entry } from './gamificationMock';
import { BADGES, levelOf, streakOf } from './rules';

/**
 * Arma el perfil de M7 a partir del points_log: insignias (con su avance y
 * el día en que se ganaron, que suman su bono), nivel y racha.
 * ⚠️ Con el backend llega hecho en GET /astronauts/{id}/gamification.
 */
function dayReaching(
    perDay: number[],
    target: number,
): { day: number | null; best: number } {
    let total = 0;

    for (let i = 0; i < perDay.length; i++) {
        total += perDay[i];

        if (total >= target) {
            return { day: i + 1, best: target };
        }
    }

    return { day: null, best: total };
}

/** El primer día en que una racha de días que cumplen llega a `target`, o la racha más larga. */
function runReaching(
    ok: (day: number) => boolean,
    today: number,
    target: number,
) {
    let run = 0;
    let best = 0;

    for (let day = 1; day <= today; day++) {
        run = ok(day) ? run + 1 : 0;
        best = Math.max(best, run);

        if (run >= target) {
            return { day, best: target };
        }
    }

    return { day: null, best };
}

export function buildProfile(
    astronautId: number,
    today: number,
    log: PointEntry[],
): GamificationProfile {
    const count = (reason: PointEntry['reason']) => {
        const perDay = Array.from({ length: today }, () => 0);
        log.filter((item) => item.reason === reason).forEach(
            (item) => perDay[item.day - 1]++,
        );

        return perDay;
    };

    const complete = new Set(
        log
            .filter((item) => item.reason === 'daily_complete')
            .map((item) => item.day),
    );
    const heart = ['hr', 'bp_sys', 'bp_dia'].map((code) =>
        mockSeries(astronautId, code as 'hr', today),
    );
    const sleep = mockSeries(astronautId, 'sleep', today);
    const psychWeeks = new Map<number, number>();
    log.filter((item) => item.reason === 'psych_test').forEach((item) => {
        const week = Math.floor((item.day - 1) / 7);
        psychWeeks.set(week, (psychWeeks.get(week) ?? 0) + 1);
    });
    const fullWeek = [...psychWeeks.entries()].find(([, total]) => total >= 4);

    const reached: Record<BadgeCode, { day: number | null; best: number }> = {
        first_log: complete.size
            ? { day: Math.min(...complete), best: 1 }
            : { day: null, best: 0 },
        orbit_7: runReaching((day) => complete.has(day), today, 7),
        steel_heart: runReaching(
            (day) =>
                heart.every((series) => series[day - 1]?.status === 'green'),
            today,
            7,
        ),
        space_sleeper: runReaching(
            (day) => (sleep[day - 1]?.value ?? 0) >= 7,
            today,
            7,
        ),
        on_time: dayReaching(count('reminder'), 10),
        iron_body: dayReaching(count('exercise'), 14),
        swiss_clock: dayReaching(count('reminder'), 100),
        mission_30: runReaching((day) => complete.has(day), today, 30),
        mind_check: fullWeek
            ? {
                  day:
                      fullWeek[0] * 7 + 7 > today ? today : fullWeek[0] * 7 + 3,
                  best: 4,
              }
            : { day: null, best: Math.max(0, ...psychWeeks.values()) },
    };

    const badges: Badge[] = BADGES.map((badge) => ({
        code: badge.code,
        name: badge.name,
        icon: badge.icon,
        condition: badge.condition,
        bonus: badge.bonus,
        earned_day: reached[badge.code].day,
        progress: { current: reached[badge.code].best, target: badge.target },
    }));

    const withBonus = [
        ...log,
        ...badges
            .filter((badge) => badge.earned_day !== null)
            .map((badge) =>
                entry(
                    astronautId,
                    'badge',
                    badge.code,
                    badge.earned_day!,
                    today,
                    badge.bonus,
                ),
            ),
    ];
    const points = withBonus.reduce((sum, item) => sum + item.points, 0);
    const { level, progress } = levelOf(points);

    return {
        astronaut_id: astronautId,
        points,
        level,
        level_progress: progress,
        streak_days: streakOf(complete, today),
        badges,
        recent: [...withBonus]
            .sort(
                (a, b) =>
                    b.day - a.day || b.created_at.localeCompare(a.created_at),
            )
            .slice(0, 8),
    };
}
