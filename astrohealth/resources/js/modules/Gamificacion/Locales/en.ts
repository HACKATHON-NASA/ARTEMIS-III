/** M7 · Gamification (points, streaks, badges and ranking). */
export default {
    pageTitle: 'Achievements',
    mission: 'Mission',
    title: 'Achievements',
    subtitle:
        'Taking care of yourself pays off: logging your data, sleeping well, exercising and keeping your routine earn you points, streaks and badges.',
    badgesCount: 'badges',
    badges: {
        first_log: {
            name: 'First log',
            condition: 'Log your first measurement',
        },
        orbit_7: {
            name: '7 days in orbit',
            condition: '7-day streak with a complete log',
        },
        steel_heart: {
            name: 'Heart of steel',
            condition:
                '7 days in a row with heart rate and blood pressure in green',
        },
        space_sleeper: {
            name: 'Space sleeper',
            condition: '7 nights in a row of 7 h or more',
        },
        on_time: { name: 'On time', condition: '10 reminders done' },
        mind_check: {
            name: 'Mind check',
            condition: 'Take the 4 psychological tests in one week',
        },
        iron_body: {
            name: 'Iron body',
            condition: '14 days with 90 min of exercise or more',
        },
        swiss_clock: { name: 'Swiss clock', condition: '100 reminders done' },
        mission_30: {
            name: 'Mission complete',
            condition: '30-day streak with a complete log',
        },
    },
    badgesTitle: 'Badges',
    locked: 'locked',
    earnedOn: 'Earned on day {day} · +{bonus} pts',
    ranking: 'Crew ranking',
    recentTitle: 'Your latest points',
    place: 'Place {n}',
    rowDetail: 'Level {level} · 🔥 {streak} · {badges} badges',
    reasons: {
        daily_complete: 'Complete daily log',
        streak_7: '7-day streak',
        sleep_7h: 'You slept 7 h or more',
        exercise: 'Exercise of 90 min or more',
        reminder: 'Reminder done',
        psych_test: 'Psychological test',
        badge: 'New badge',
    },
    how: {
        title: 'How to earn them',
        daily_complete: 'Log the 10 indicators of the day',
        streak_7: '7 days in a row',
        sleep_7h: 'Sleep 7 h or more',
        exercise: 'Do 90 min of exercise',
        reminder: 'Complete a reminder',
        psych_test: 'Take a psychological test',
    },
    streakDays: 'day | days',
    streakHint: 'streak with a complete log',
    sidebarStreak: '🔥 {n} day streak | 🔥 {n} days streak',
    seeMine: 'See my achievements',
    level: 'Level {n}',
    levelProgress: '{progress}/100 · {points} pts',
    levelAria: 'Level {level}: {progress} of 100 points to the next one',
    newBadge: 'New badge: {name}! {icon}',
    bonus: '+{n} extra points',
    levelUp: 'You reached level {n}!',
};
