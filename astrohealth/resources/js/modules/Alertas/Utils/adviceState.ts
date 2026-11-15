import type { Advice } from '../Types/advice';

/**
 * En qué va una indicación: viajando a la nave, en la nave sin leer, o
 * entendida por el tripulante.
 */
export type AdviceState = 'traveling' | 'unread' | 'read';

export type AdviceFilter = 'all' | AdviceState;

export function adviceState(advice: Advice, now: number): AdviceState {
    if (advice.read_at) {
        return 'read';
    }

    return new Date(advice.arrives_at).getTime() > now ? 'traveling' : 'unread';
}
