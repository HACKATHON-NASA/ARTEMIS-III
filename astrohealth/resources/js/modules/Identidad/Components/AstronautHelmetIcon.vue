<script setup lang="ts">
/**
 * El casco de astronauta de la tarjeta, en línea fina, con dos destellos.
 * Flota despacio y los destellos titilan (sin movimiento si el sistema lo pide).
 */
</script>

<template>
    <svg
        viewBox="0 0 160 120"
        fill="none"
        class="helmet h-24 w-32 overflow-visible"
        aria-hidden="true"
    >
        <g stroke="white" stroke-width="3.5" stroke-linecap="round">
            <circle cx="80" cy="56" r="38" />
            <path d="M58 46 a24 24 0 0 1 44 0 v14 a22 22 0 0 1 -44 0 z" />
            <path d="M64 46 a17 17 0 0 1 14 -10" stroke-opacity="0.7" />
            <rect x="35" y="44" width="9" height="24" rx="4" />
            <rect x="116" y="44" width="9" height="24" rx="4" />
            <path d="M50 92 q30 14 60 0" />
            <path d="M54 100 l-4 12 h60 l-4 -12" />
        </g>
        <g fill="#7cc4ff" class="sparkles">
            <path d="M138 26 l3 8 8 3 -8 3 -3 8 -3 -8 -8 -3 8 -3 z" />
            <path
                d="M24 70 l2 5 5 2 -5 2 -2 5 -2 -5 -5 -2 5 -2 z"
                class="sparkle-late"
            />
        </g>
    </svg>
</template>

<style scoped>
.helmet {
    animation: helmet-float 7s ease-in-out infinite;
}

.sparkles path {
    transform-box: fill-box;
    transform-origin: center;
    animation: sparkle 2.8s ease-in-out infinite;
}

.sparkles .sparkle-late {
    animation-delay: 1.4s;
}

@keyframes helmet-float {
    50% {
        transform: translateY(-6px) rotate(1.5deg);
    }
}

@keyframes sparkle {
    0%,
    100% {
        opacity: 0.35;
        transform: scale(0.7) rotate(0deg);
    }
    50% {
        opacity: 1;
        transform: scale(1.15) rotate(45deg);
    }
}

@media (prefers-reduced-motion: reduce) {
    .helmet,
    .sparkles path {
        animation: none;
    }
}
</style>
