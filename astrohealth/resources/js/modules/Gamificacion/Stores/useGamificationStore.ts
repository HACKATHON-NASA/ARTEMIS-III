import { defineStore } from 'pinia';
import { ref } from 'vue';
import { toast } from 'vue-sonner';
import { launchConfetti } from '../Composables/useConfetti';
import type {
    AwardResult,
    GamificationProfile,
    LeaderRow,
    PointReason,
} from '../Types/gamification';
import { entry, mockAward, mockPointsLog } from '../Utils/gamificationMock';
import { buildProfile } from '../Utils/profile';
import { POINTS } from '../Utils/rules';

/**
 * Puntos, nivel, racha, insignias y ranking (M7). award() suma puntos (una
 * sola vez por motivo y referencia), compara el antes y el después y, si hay
 * insignia nueva o sube de nivel, lanza el confeti y el aviso.
 *
 * ⚠️ HOY con datos de prueba (Utils/gamificationMock.ts). Con el backend los
 * puntos los suma M7 al escuchar los eventos de M1, M8 y M2; el store solo
 * vuelve a pedir GET /astronauts/{id}/gamification y compara.
 */
const wait = (ms = 200) => new Promise((resolve) => setTimeout(resolve, ms));

export const useGamificationStore = defineStore('gamification', () => {
    const profile = ref<GamificationProfile | null>(null);
    const leaderboard = ref<LeaderRow[]>([]);
    let today = 1;

    function build(astronautId: number): GamificationProfile {
        return buildProfile(
            astronautId,
            today,
            mockPointsLog(astronautId, today),
        );
    }

    async function load(astronautId: number, day: number): Promise<void> {
        today = day;
        await wait();
        profile.value = build(astronautId);
    }

    async function loadLeaderboard(
        members: { id: number; name: string }[],
        day: number,
    ): Promise<void> {
        today = day;
        await wait();
        leaderboard.value = members
            .map((member) => {
                const item = build(member.id);

                return {
                    astronaut_id: member.id,
                    name: member.name,
                    points: item.points,
                    level: item.level,
                    streak_days: item.streak_days,
                    badges: item.badges.filter(
                        (badge) => badge.earned_day !== null,
                    ).length,
                };
            })
            .sort((a, b) => b.points - a.points);
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
     * Suma los puntos de una o varias acciones (las que no se habían sumado)
     * y celebra una sola vez lo nuevo: insignias y subida de nivel.
     */
    async function awardMany(
        astronautId: number,
        items: [Exclude<PointReason, 'badge'>, string][],
        day: number,
    ): Promise<AwardResult> {
        today = day;
        const before = build(astronautId);
        items.forEach(([reason, reference]) =>
            mockAward(
                entry(
                    astronautId,
                    reason,
                    reference,
                    today,
                    today,
                    POINTS[reason],
                ),
                today,
            ),
        );
        const after = build(astronautId);
        profile.value = after;

        const result: AwardResult = {
            gained: after.points - before.points,
            levelUp: after.level > before.level ? after.level : null,
            newBadges: after.badges.filter(
                (badge) =>
                    badge.earned_day !== null &&
                    before.badges.find((old) => old.code === badge.code)
                        ?.earned_day === null,
            ),
        };
        celebrate(result);

        return result;
    }

    function award(
        astronautId: number,
        reason: Exclude<PointReason, 'badge'>,
        reference: string,
        day: number,
    ): Promise<AwardResult> {
        return awardMany(astronautId, [[reason, reference]], day);
    }

    return { profile, leaderboard, load, loadLeaderboard, award, awardMany };
});
