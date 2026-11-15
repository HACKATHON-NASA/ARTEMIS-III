import type { BodyZoneCode } from '@/types/health';

/**
 * Dónde va cada zona en la figura (viewBox 0 0 320 640) y dónde sale su
 * tooltip (en % del dibujo). Las zonas pequeñas tienen un área de clic
 * (`hit`) más grande que su dibujo.
 */
export type ZoneShape =
    | { kind: 'ellipse'; cx: number; cy: number; rx: number; ry: number }
    | { kind: 'path'; d: string };

export type ZoneGeometry = {
    code: BodyZoneCode;
    shapes: ZoneShape[];
    hit: { cx: number; cy: number; r: number };
    tip: { x: number; y: number };
};

export const VIEWBOX = { width: 320, height: 640 };

const HEART =
    'M170 200 c-6 -8 -18 -6 -18 5 c0 10 12 16 18 22 c6 -6 18 -12 18 -22 c0 -11 -12 -13 -18 -5 z';

export const ZONES: ZoneGeometry[] = [
    {
        code: 'whole_body',
        shapes: [{ kind: 'ellipse', cx: 286, cy: 116, rx: 11, ry: 11 }],
        hit: { cx: 286, cy: 116, r: 18 },
        tip: { x: 89, y: 18 },
    },
    {
        code: 'brain',
        shapes: [{ kind: 'ellipse', cx: 160, cy: 60, rx: 27, ry: 20 }],
        hit: { cx: 160, cy: 60, r: 24 },
        tip: { x: 50, y: 9 },
    },
    {
        code: 'eyes',
        shapes: [
            { kind: 'ellipse', cx: 146, cy: 88, rx: 7, ry: 4 },
            { kind: 'ellipse', cx: 174, cy: 88, rx: 7, ry: 4 },
        ],
        hit: { cx: 160, cy: 88, r: 17 },
        tip: { x: 50, y: 14 },
    },
    {
        code: 'ear',
        shapes: [
            { kind: 'ellipse', cx: 119, cy: 84, rx: 5, ry: 10 },
            { kind: 'ellipse', cx: 201, cy: 84, rx: 5, ry: 10 },
        ],
        hit: { cx: 119, cy: 84, r: 14 },
        tip: { x: 37, y: 13 },
    },
    {
        code: 'immune',
        shapes: [{ kind: 'ellipse', cx: 160, cy: 150, rx: 9, ry: 7 }],
        hit: { cx: 160, cy: 150, r: 14 },
        tip: { x: 50, y: 23 },
    },
    {
        code: 'lungs',
        shapes: [
            { kind: 'ellipse', cx: 135, cy: 205, rx: 20, ry: 33 },
            { kind: 'ellipse', cx: 185, cy: 205, rx: 20, ry: 33 },
        ],
        hit: { cx: 135, cy: 205, r: 28 },
        tip: { x: 42, y: 32 },
    },
    {
        code: 'heart',
        shapes: [{ kind: 'path', d: HEART }],
        hit: { cx: 170, cy: 212, r: 16 },
        tip: { x: 53, y: 33 },
    },
    {
        code: 'metabolism',
        shapes: [{ kind: 'ellipse', cx: 160, cy: 280, rx: 30, ry: 21 }],
        hit: { cx: 160, cy: 280, r: 26 },
        tip: { x: 50, y: 44 },
    },
    {
        code: 'bones',
        shapes: [
            { kind: 'ellipse', cx: 133, cy: 470, rx: 9, ry: 62 },
            { kind: 'ellipse', cx: 187, cy: 470, rx: 9, ry: 62 },
        ],
        hit: { cx: 133, cy: 470, r: 30 },
        tip: { x: 50, y: 73 },
    },
];
