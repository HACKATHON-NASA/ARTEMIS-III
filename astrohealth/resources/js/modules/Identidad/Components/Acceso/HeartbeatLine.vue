<script setup lang="ts">
import gsap from 'gsap';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { prefersReducedMotion } from '../../Composables/motionPreferences';

/**
 * El electrocardiograma de la esquina: una línea tenue y un pulso de luz
 * que la recorre una y otra vez, como un monitor de signos vitales.
 */
const pulse = ref<SVGPathElement | null>(null);

const PATH =
    'M0 34 H170 L180 34 L188 12 L198 58 L208 4 L218 44 L226 34 H270 L278 34 L286 16 L294 52 L302 8 L312 40 L320 34 H480';

let tween: gsap.core.Tween | null = null;

onMounted(() => {
    const el = pulse.value;

    if (el === null || prefersReducedMotion()) {
        return;
    }

    const length = el.getTotalLength();
    const segment = 140;

    gsap.set(el, { strokeDasharray: `${segment} ${length}` });
    tween = gsap.fromTo(
        el,
        { strokeDashoffset: segment },
        { strokeDashoffset: -length, duration: 3.2, ease: 'none', repeat: -1 },
    );
});

onBeforeUnmount(() => tween?.kill());
</script>

<template>
    <svg
        viewBox="0 0 480 64"
        fill="none"
        class="pointer-events-none overflow-visible"
        aria-hidden="true"
        data-intro="pulse"
    >
        <path
            :d="PATH"
            stroke="#3b82f6"
            stroke-opacity="0.35"
            stroke-width="2"
            stroke-linejoin="round"
        />
        <path
            ref="pulse"
            :d="PATH"
            stroke="#7cc4ff"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
            class="drop-shadow-[0_0_6px_rgba(96,170,255,0.9)]"
        />
    </svg>
</template>
