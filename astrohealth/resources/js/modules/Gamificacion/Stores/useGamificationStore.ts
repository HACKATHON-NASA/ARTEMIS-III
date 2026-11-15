import { defineStore } from 'pinia';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { api } from '@/lib/api';
import { launchConfetti } from '../Composables/useConfetti';
import type {
    AwardResult,
    ClaimableReason,
    GamificationProfile,
    LeaderRow,
} from '../Types/gamification';

/**
 * Puntos, nivel, racha, insignias y ranking (M7).
 *
 * Los puntos los suma el backend: los del registro (día completo, sueño,
 * ejercicio, racha e insignias) al guardar las mediciones; los de un
 * recordatorio o un test, con POST /gamification/claims (temporal hasta
 * que M8 y los tests tengan backend). El store solo vuelve a pedir
 * GET /astronauts/{id}/gamification, compara el antes y el después y, si hay
 * insignia nueva o sube de nivel, lanza el confeti y el aviso.
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
            toast.success(`¡Nueva insignia: ${badge.name}! ${badge.icon}`, {
                description: `+${badge.bonus} puntos extra`,
            }),
        );

        if (result.levelUp) {
            toast.success(`¡Subiste al nivel ${result.levelUp}!`);
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

    /** Reclama los puntos de un recordatorio cumplido o de un test hecho. */
    function award(
        astronautId: number,
        reason: ClaimableReason,
        reference: string,
    ): Promise<AwardResult> {
        return refresh(astronautId, () =>
            api('/gamification/claims', {
                method: 'POST',
                body: { reason, reference },
            }),
        );
    }

    return { profile, leaderboard, load, loadLeaderboard, refresh, award };
});
