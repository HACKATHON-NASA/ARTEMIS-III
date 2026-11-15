<script setup lang="ts">
import type { EnvironmentCode } from '../Types/mission';

/**
 * La ilustración del entorno (SVG propio, quieto: nada de fondos animados):
 * Marte, la Luna, la Estación sobre la Tierra o la nave en tránsito.
 */
defineProps<{ environment: EnvironmentCode }>();

const STARS = [
    [18, 22],
    [44, 160],
    [170, 30],
    [150, 180],
    [30, 100],
    [185, 110],
    [96, 14],
    [120, 190],
    [8, 60],
    [190, 70],
];
</script>

<template>
    <svg
        viewBox="0 0 200 200"
        class="size-full"
        role="img"
        :aria-label="`Ilustración: ${environment}`"
    >
        <defs>
            <radialGradient id="art-mars" cx="35%" cy="30%" r="75%">
                <stop offset="0%" stop-color="#fdba74" />
                <stop offset="55%" stop-color="#ea580c" />
                <stop offset="100%" stop-color="#7c2d12" />
            </radialGradient>
            <radialGradient id="art-moon" cx="35%" cy="30%" r="75%">
                <stop offset="0%" stop-color="#f1f5f9" />
                <stop offset="60%" stop-color="#94a3b8" />
                <stop offset="100%" stop-color="#334155" />
            </radialGradient>
            <radialGradient id="art-earth" cx="50%" cy="0%" r="90%">
                <stop offset="0%" stop-color="#7dd3fc" />
                <stop offset="45%" stop-color="#2563eb" />
                <stop offset="100%" stop-color="#1e3a8a" />
            </radialGradient>
        </defs>

        <circle
            v-for="([x, y], i) in STARS"
            :key="i"
            :cx="x"
            :cy="y"
            :r="i % 3 ? 0.8 : 1.3"
            fill="#e0f2fe"
            opacity="0.7"
        />

        <g v-if="environment === 'mars'">
            <circle cx="100" cy="104" r="62" fill="url(#art-mars)" />
            <ellipse
                cx="82"
                cy="92"
                rx="16"
                ry="7"
                fill="#9a3412"
                opacity="0.45"
            />
            <ellipse
                cx="118"
                cy="124"
                rx="22"
                ry="8"
                fill="#9a3412"
                opacity="0.35"
            />
            <path
                d="M58 78 Q100 70 142 82"
                stroke="#fed7aa"
                stroke-width="1.2"
                fill="none"
                opacity="0.5"
            />
            <circle cx="166" cy="48" r="7" fill="#a8a29e" />
        </g>

        <g v-else-if="environment === 'moon'">
            <circle cx="100" cy="104" r="62" fill="url(#art-moon)" />
            <circle cx="80" cy="88" r="11" fill="#64748b" opacity="0.45" />
            <circle cx="122" cy="118" r="15" fill="#64748b" opacity="0.4" />
            <circle cx="104" cy="74" r="6" fill="#64748b" opacity="0.4" />
            <circle cx="76" cy="130" r="7" fill="#64748b" opacity="0.35" />
        </g>

        <g v-else-if="environment === 'iss'">
            <circle cx="100" cy="128" r="58" fill="url(#art-earth)" />
            <path
                d="M70 112 Q86 100 98 114 T126 120"
                stroke="#86efac"
                stroke-width="7"
                stroke-linecap="round"
                fill="none"
                opacity="0.45"
            />
            <ellipse
                cx="100"
                cy="128"
                rx="78"
                ry="16"
                stroke="#bae6fd"
                stroke-width="1"
                stroke-dasharray="2 4"
                fill="none"
                opacity="0.6"
            />
            <g
                transform="translate(100 46) rotate(-8) scale(0.85)"
                fill="#cbd5e1"
            >
                <rect x="-6" y="-5" width="12" height="10" rx="2" />
                <rect x="-36" y="-1.5" width="72" height="3" />
                <rect
                    x="-46"
                    y="-14"
                    width="10"
                    height="28"
                    fill="#38bdf8"
                    opacity="0.85"
                />
                <rect
                    x="-60"
                    y="-14"
                    width="10"
                    height="28"
                    fill="#38bdf8"
                    opacity="0.85"
                />
                <rect
                    x="36"
                    y="-14"
                    width="10"
                    height="28"
                    fill="#38bdf8"
                    opacity="0.85"
                />
                <rect
                    x="50"
                    y="-14"
                    width="10"
                    height="28"
                    fill="#38bdf8"
                    opacity="0.85"
                />
            </g>
        </g>

        <g v-else>
            <circle
                cx="164"
                cy="44"
                r="14"
                fill="url(#art-mars)"
                opacity="0.9"
            />
            <circle
                cx="30"
                cy="172"
                r="26"
                fill="url(#art-earth)"
                opacity="0.9"
            />
            <path
                d="M48 154 Q100 110 150 58"
                stroke="#7dd3fc"
                stroke-width="1.2"
                stroke-dasharray="3 5"
                fill="none"
                opacity="0.7"
            />
            <g transform="translate(104 104) rotate(-42)">
                <path d="M0 -16 L7 6 L0 3 L-7 6 Z" fill="#e2e8f0" />
                <path d="M-3 6 L0 16 L3 6 Z" fill="#38bdf8" />
            </g>
        </g>
    </svg>
</template>
