/**
 * Tipos de M7 · Gamificación, con la forma de la API:
 * GET /api/v1/astronauts/{id}/gamification y GET /api/v1/leaderboard.
 */
export type PointReason =
    | 'daily_complete'
    | 'streak_7'
    | 'sleep_7h'
    | 'exercise'
    | 'reminder'
    | 'psych_test'
    | 'badge';

/** Una fila de points_log. La llave (astronaut_id, reason, reference) no se repite. */
export type PointEntry = {
    astronaut_id: number;
    points: number;
    reason: PointReason;
    reference: string;
    /** Día de misión. */
    day: number;
    created_at: string;
};

export type BadgeCode =
    | 'first_log'
    | 'orbit_7'
    | 'steel_heart'
    | 'space_sleeper'
    | 'on_time'
    | 'mind_check'
    | 'iron_body'
    | 'swiss_clock'
    | 'mission_30';

export type Badge = {
    code: BadgeCode;
    name: string;
    icon: string;
    /** Cómo se gana (se muestra en las bloqueadas). */
    condition: string;
    bonus: number;
    /** Día de misión en que se ganó; null si sigue bloqueada. */
    earned_day: number | null;
    progress: { current: number; target: number };
};

export type GamificationProfile = {
    astronaut_id: number;
    points: number;
    level: number;
    /** Puntos dentro del nivel actual (0–99). */
    level_progress: number;
    streak_days: number;
    badges: Badge[];
    recent: PointEntry[];
};

export type LeaderRow = {
    astronaut_id: number;
    name: string;
    points: number;
    level: number;
    streak_days: number;
    badges: number;
};

/** Lo que pasó al ganar puntos (para la celebración). */
export type AwardResult = {
    gained: number;
    levelUp: number | null;
    newBadges: Badge[];
};
