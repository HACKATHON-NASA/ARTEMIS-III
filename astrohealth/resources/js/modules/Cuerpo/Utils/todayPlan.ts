import type { InertiaLinkProps } from '@inertiajs/vue3';
import { Brain, ClipboardList, ListChecks, Trophy } from '@lucide/vue';
import type { Component } from 'vue';
import type { IndicatorCode } from '@/types/health';
import { t } from '@/lib/i18n';
import { indicatorLabel } from './indicators';

/**
 * "Tu día" en el Inicio: qué le falta hoy al astronauta, en cuatro tarjetas
 * (medir, rutina, tests y la próxima insignia). Recibe datos ya resueltos
 * (el Inicio los saca de cada módulo) y arma lo que se muestra.
 */
export type PlanItem = {
    key: string;
    icon: Component;
    title: string;
    detail: string;
    progress: { done: number; total: number };
    /** Ya está todo hecho (se muestra en verde). */
    complete: boolean;
    action: string;
    href: NonNullable<InertiaLinkProps['href']>;
};

const DAY_MS = 24 * 60 * 60 * 1000;

type Input = {
    /** Los últimos valores del cuerpo (M3): lo que no se midió en 24 h falta. */
    values: { indicator: IndicatorCode; measured_at: string }[];
    routine: {
        done: number;
        total: number;
        next: { title: string; time: string } | null;
    };
    tests: { pending: number; total: number };
    /** Las insignias (M7): la bloqueada con más avance es la próxima. */
    badges: {
        icon: string;
        name: string;
        earned_day: number | null;
        progress: { current: number; target: number };
    }[];
    links: {
        record: PlanItem['href'];
        routine: PlanItem['href'];
        tests: PlanItem['href'];
        badges: PlanItem['href'];
    };
};

function list(names: string[]): string {
    if (names.length <= 1) {
        return names.join('');
    }

    return names.length === 2
        ? t('Cuerpo.plan.and', { a: names[0], b: names[1] })
        : t('Cuerpo.plan.andMore', {
              list: names.slice(0, 2).join(', '),
              n: names.length - 2,
          });
}

export function buildTodayPlan(input: Input): PlanItem[] {
    const { routine, tests, links } = input;
    const missing = input.values
        .filter(
            (value) =>
                Date.now() - new Date(value.measured_at).getTime() >= DAY_MS,
        )
        .map((value) => indicatorLabel(value.indicator));
    const measured = {
        done: input.values.length - missing.length,
        total: input.values.length,
        missing,
    };
    const ratio = (badge: Input['badges'][number]) =>
        badge.progress.current / badge.progress.target;
    const next = input.badges
        .filter((item) => item.earned_day === null)
        .sort((a, b) => ratio(b) - ratio(a))[0];
    const badge = next
        ? { icon: next.icon, name: next.name, ...next.progress }
        : null;

    return [
        {
            key: 'measure',
            icon: ClipboardList,
            title: t('Cuerpo.plan.measure'),
            detail: measured.missing.length
                ? t('Cuerpo.plan.missing', { list: list(measured.missing) })
                : t('Cuerpo.plan.allLogged'),
            progress: { done: measured.done, total: measured.total },
            complete: measured.missing.length === 0,
            action: t('Cuerpo.plan.record'),
            href: links.record,
        },
        {
            key: 'routine',
            icon: ListChecks,
            title: t('Cuerpo.plan.routine'),
            detail: routine.next
                ? t('Cuerpo.plan.next', {
                      title: routine.next.title.replace(
                          /\s*\p{Extended_Pictographic}/gu,
                          '',
                      ),
                      time: routine.next.time,
                  })
                : t('Cuerpo.plan.routineDone'),
            progress: routine,
            complete: routine.next === null,
            action: t('Cuerpo.plan.seeRoutine'),
            href: links.routine,
        },
        {
            key: 'tests',
            icon: Brain,
            title: t('Cuerpo.plan.tests'),
            detail: tests.pending
                ? t('Cuerpo.plan.testsDue', { n: tests.pending }, tests.pending)
                : t('Cuerpo.plan.upToDate'),
            progress: { done: tests.total - tests.pending, total: tests.total },
            complete: tests.pending === 0,
            action: t('Cuerpo.plan.answer'),
            href: links.tests,
        },
        {
            key: 'badge',
            icon: Trophy,
            title: t('Cuerpo.plan.badge'),
            detail: badge
                ? `${badge.icon} ${badge.name}`
                : t('Cuerpo.plan.allBadges'),
            progress: badge
                ? { done: badge.current, total: badge.target }
                : { done: 1, total: 1 },
            complete: badge === null,
            action: t('Cuerpo.plan.seeBadges'),
            href: links.badges,
        },
    ];
}
