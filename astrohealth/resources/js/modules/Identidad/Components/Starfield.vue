<script setup lang="ts">
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { prefersReducedMotion } from '../Composables/motionPreferences';
import { usePointerParallax } from '../Composables/usePointerParallax';

/**
 * Estrellas que titilan, dibujadas en un <canvas>. Es la capa más cercana
 * del paralaje: se mueve más que el fondo y da la profundidad.
 */
type Star = { x: number; y: number; r: number; phase: number; speed: number };

const layer = ref<HTMLElement | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);

usePointerParallax(layer, 46);

let stars: Star[] = [];
let frame = 0;

function resize(context: CanvasRenderingContext2D): void {
    const el = context.canvas;
    const ratio = Math.min(window.devicePixelRatio || 1, 2);

    el.width = el.clientWidth * ratio;
    el.height = el.clientHeight * ratio;
    context.setTransform(ratio, 0, 0, ratio, 0, 0);

    const count = Math.min(
        220,
        Math.round((el.clientWidth * el.clientHeight) / 9000),
    );
    stars = Array.from({ length: count }, () => ({
        x: Math.random() * el.clientWidth,
        y: Math.random() * el.clientHeight,
        r: Math.random() * 1.1 + 0.25,
        phase: Math.random() * Math.PI * 2,
        speed: 0.5 + Math.random() * 1.4,
    }));
}

function draw(context: CanvasRenderingContext2D, time: number): void {
    const { clientWidth, clientHeight } = context.canvas;
    context.clearRect(0, 0, clientWidth, clientHeight);

    for (const star of stars) {
        const twinkle =
            0.5 + 0.5 * Math.sin((time / 1000) * star.speed + star.phase);
        context.globalAlpha = 0.2 + 0.8 * twinkle;
        context.beginPath();
        context.arc(star.x, star.y, star.r, 0, Math.PI * 2);
        context.fill();
    }
}

let onResize: (() => void) | null = null;

onMounted(() => {
    const context = canvas.value?.getContext('2d');

    if (!context) {
        return;
    }

    context.fillStyle = '#cfe4ff';
    resize(context);
    onResize = () => {
        resize(context);
        context.fillStyle = '#cfe4ff';
    };
    window.addEventListener('resize', onResize);

    if (prefersReducedMotion()) {
        draw(context, 0);

        return;
    }

    const loop = (time: number): void => {
        draw(context, time);
        frame = requestAnimationFrame(loop);
    };
    frame = requestAnimationFrame(loop);
});

onBeforeUnmount(() => {
    cancelAnimationFrame(frame);

    if (onResize) {
        window.removeEventListener('resize', onResize);
    }
});
</script>

<template>
    <div
        ref="layer"
        class="pointer-events-none absolute -inset-12 mix-blend-screen"
        data-intro="stars"
    >
        <canvas ref="canvas" class="size-full" aria-hidden="true" />
    </div>
</template>
