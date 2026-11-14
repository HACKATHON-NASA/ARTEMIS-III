import type { BodyZoneCode, MaybeStatus, Status } from '@/types/health';

/**
 * La última evaluación, como GET /api/v1/astronauts/{id}/assessment (M2).
 */
export type Assessment = {
    astronaut_id: number;
    score: number;
    status: Status;
    body_status: Record<BodyZoneCode, MaybeStatus>;
    summary: string;
    evaluated_at: string;
};
