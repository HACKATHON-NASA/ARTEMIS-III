/** M7 · Gamificación (puntos, rachas, insignias y ranking). */
export default {
    pageTitle: 'Logros',
    mission: 'Misión',
    title: 'Logros',
    subtitle:
        'Cuidarte suma: registrar tus datos, dormir bien, hacer ejercicio y cumplir tu rutina te dan puntos, rachas e insignias.',
    badgesCount: 'insignias',
    badges: {
        first_log: {
            name: 'Primer registro',
            condition: 'Registra tu primera medición',
        },
        orbit_7: {
            name: '7 días en órbita',
            condition: 'Racha de 7 días con el registro completo',
        },
        steel_heart: {
            name: 'Corazón de acero',
            condition: '7 días seguidos con pulso y presión en verde',
        },
        space_sleeper: {
            name: 'Dormilón espacial',
            condition: '7 noches seguidas de 7 h o más',
        },
        on_time: { name: 'Puntual', condition: '10 recordatorios cumplidos' },
        mind_check: {
            name: 'Mente al día',
            condition: 'Haz los 4 tests psicológicos de una semana',
        },
        iron_body: {
            name: 'Cuerpo de hierro',
            condition: '14 días con 90 min de ejercicio o más',
        },
        swiss_clock: {
            name: 'Reloj suizo',
            condition: '100 recordatorios cumplidos',
        },
        mission_30: {
            name: 'Misión completa',
            condition: 'Racha de 30 días con el registro completo',
        },
    },
    badgesTitle: 'Insignias',
    locked: 'bloqueada',
    earnedOn: 'Ganada el día {day} · +{bonus} pts',
    ranking: 'Ranking de la tripulación',
    recentTitle: 'Tus últimos puntos',
    place: 'Puesto {n}',
    rowDetail: 'Nivel {level} · 🔥 {streak} · {badges} insignias',
    reasons: {
        daily_complete: 'Registro completo del día',
        streak_7: 'Racha de 7 días',
        sleep_7h: 'Dormiste 7 h o más',
        exercise: 'Ejercicio de 90 min o más',
        reminder: 'Recordatorio cumplido',
        psych_test: 'Test psicológico',
        badge: 'Insignia nueva',
    },
    how: {
        title: 'Cómo se ganan',
        daily_complete: 'Registrar los 10 indicadores del día',
        streak_7: 'Racha de 7 días seguidos',
        sleep_7h: 'Dormir 7 h o más',
        exercise: 'Hacer 90 min de ejercicio',
        reminder: 'Cumplir un recordatorio',
        psych_test: 'Hacer un test psicológico',
    },
    streakDays: 'día | días',
    streakHint: 'de racha con el registro completo',
    sidebarStreak: '🔥 {n} día de racha | 🔥 {n} días de racha',
    seeMine: 'Ver mis logros',
    level: 'Nivel {n}',
    levelProgress: '{progress}/{size} · {points} pts',
    levelAria: 'Nivel {level}: {progress} de {size} puntos para el siguiente',
    newBadge: '¡Nueva insignia: {name}! {icon}',
    bonus: '+{n} puntos extra',
    levelUp: '¡Subiste al nivel {n}!',
};
