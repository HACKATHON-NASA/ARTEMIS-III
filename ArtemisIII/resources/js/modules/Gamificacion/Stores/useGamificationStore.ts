import { defineStore } from 'pinia';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { api } from '@/lib/api';
import { launchConfetti } from '../Composables/useConfetti';
import { badgeName } from '../Utils/rules';
import type {
    AwardResult,
    GamificationProfile,
    LeaderRow,
} from '../Types/gamification';
import { t } from '@/lib/i18n';

/**
 * Puntos, nivel, racha, insignias y ranking (M7).
 *
 * Los puntos los suma SOLO el backend, al escuchar los eventos: registrar
 * mediciones (M1), cumplir un recordatorio (M8) y hacer un test (M2). Nadie
 * puede pedir puntos. El store vuelve a pedir
 * GET /astronauts/{id}/gamification (refresh), compara el antes y el
 * después y, si hay insignia nueva o sube de nivel, lanza el confeti y el aviso.
 */
type LeaderApiRow = Omit<LeaderRow, 'name'>;

export const useGamificationStore = defineStore('gamification', () => {
    const profile = ref<GamificationProfile | null>(null);
    const leaderboard = ref<LeaderRow[]>([]);

    function fetchProfile(astronautId: number): Promise<GamificationProfile> {
        return api<GamificationProfile>(
            `/astronauts/${astronautId}/gamification`,
        );
    }

    async function load(astronautId: number): Promise<void> {
        profile.value = await fetchProfile(astronautId);
    }

    async function loadLeaderboard(
        members: { id: number; name: string }[],
    ): Promise<void> {
        if (members.length === 0) {
            leaderboard.value = [];

            return;
        }

        const rows = await api<LeaderApiRow[]>(
            `/gamification/leaderboard?astronauts=${members.map((m) => m.id).join(',')}`,
        );
        leaderboard.value = rows.map((row) => ({
            ...row,
            name: members.find((m) => m.id === row.astronaut_id)?.name ?? '—',
        }));
    }

    function celebrate(result: AwardResult): void {
        if (result.newBadges.length || result.levelUp) {
            launchConfetti();
        }

        result.newBadges.forEach((badge) =>
            toast.success(
                t('Gamificacion.newBadge', {
                    name: badgeName(badge),
                    icon: badge.icon,
                }),
                {
                    description: t('Gamificacion.bonus', { n: badge.bonus }),
                },
            ),
        );

        if (result.levelUp) {
            toast.success(t('Gamificacion.levelUp', { n: result.levelUp }));
        }
    }

    /**
     * Vuelve a pedir el perfil después de algo que pudo sumar puntos y
     * celebra una sola vez lo nuevo. `action` es lo que suma (opcional).
     */
    async function refresh(
        astronautId: number,
        action?: () => Promise<unknown>,
    ): Promise<AwardResult> {
        const before =
            profile.value?.astronaut_id === astronautId
                ? profile.value
                : await fetchProfile(astronautId).catch(() => null);

        try {
            await action?.();
            profile.value = await fetchProfile(astronautId);
        } catch {
            return { gained: 0, levelUp: null, newBadges: [] };
        }

        const after = profile.value;
        const result: AwardResult = {
            gained: before ? after.points - before.points : 0,
            levelUp: before && after.level > before.level ? after.level : null,
            newBadges: before
                ? after.badges.filter(
                      (badge) =>
                          badge.earned_day !== null &&
                          before.badges.find((old) => old.code === badge.code)
                              ?.earned_day === null,
                  )
                : [],
        };
        celebrate(result);

        return result;
    }

    return { profile, leaderboard, load, loadLeaderboard, refresh };
});
