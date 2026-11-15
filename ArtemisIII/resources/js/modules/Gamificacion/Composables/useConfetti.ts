import gsap from 'gsap';

/**
 * Confeti para celebrar (insignia nueva o subir de nivel), hecho con GSAP:
 * papelitos que salen del centro de arriba, giran y caen. No hace nada con
 * "reducir movimiento" (la celebración queda en el aviso).
 */
const COLORS = [
    '#38bdf8',
    '#3b82f6',
    '#22c55e',
    '#eab308',
    '#f472b6',
    '#a78bfa',
];

export function launchConfetti(pieces = 90): void {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    const layer = document.createElement('div');
    layer.setAttribute('aria-hidden', 'true');
    layer.style.cssText =
        'position:fixed;inset:0;pointer-events:none;z-index:60;overflow:hidden';
    document.body.appendChild(layer);

    const width = window.innerWidth;
    const height = window.innerHeight;
    const timeline = gsap.timeline({ onComplete: () => layer.remove() });

    for (let i = 0; i < pieces; i++) {
        const piece = document.createElement('span');
        const size = 6 + Math.random() * 6;
        piece.style.cssText = `position:absolute;left:${width / 2}px;top:${height * 0.18}px;width:${size}px;height:${size * 0.45}px;border-radius:2px;background:${COLORS[i % COLORS.length]}`;
        layer.appendChild(piece);

        const angle = Math.random() * Math.PI * 2;
        const force = 120 + Math.random() * 260;

        timeline.to(
            piece,
            {
                keyframes: [
                    {
                        x: Math.cos(angle) * force,
                        y: Math.sin(angle) * force * 0.6 - 80,
                        duration: 0.6,
                        ease: 'power3.out',
                    },
                    {
                        y: height * 0.9,
                        x: `+=${(Math.random() - 0.5) * 160}`,
                        duration: 1.8 + Math.random(),
                        ease: 'power1.in',
                    },
                ],
                rotation: Math.random() * 720 - 360,
                opacity: 0,
                duration: 2.6,
            },
            Math.random() * 0.15,
        );
    }
}
