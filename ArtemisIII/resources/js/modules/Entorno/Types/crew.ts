/**
 * Un tripulante, como GET /api/v1/astronauts (M6).
 */
export type CrewMember = {
    id: number;
    name: string;
    crew_role: string;
};
