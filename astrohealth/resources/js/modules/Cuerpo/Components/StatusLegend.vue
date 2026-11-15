<script setup lang="ts">
import { statusMeta } from '@/lib/status';
import type { MaybeStatus } from '@/types/health';

/**
 * Qué significa cada color del semáforo médico en el cuerpo. Los colores
 * salen de statusMeta (las variables --status-*), nunca se escriben aquí.
 */
const ITEMS: { status: MaybeStatus; meaning: string }[] = [
    { status: 'green', meaning: 'Rango sano' },
    { status: 'yellow', meaning: 'Vigilar de cerca' },
    { status: 'red', meaning: 'Actuar y avisar a Tierra' },
    { status: null, meaning: 'Aún sin medir' },
];
</script>

<template>
    <ul
        class="grid grid-cols-2 gap-2 sm:grid-cols-4"
        aria-label="Qué significa cada color"
    >
        <li
            v-for="item in ITEMS"
            :key="item.status ?? 'none'"
            class="flex items-start gap-2.5 rounded-xl border px-3 py-2"
            :class="statusMeta(item.status).soft"
        >
            <span
                class="mt-1 size-2.5 shrink-0 rounded-full"
                :style="{
                    background: statusMeta(item.status).color,
                    boxShadow: `0 0 8px ${statusMeta(item.status).color}`,
                }"
            />
            <span class="min-w-0">
                <span class="block text-sm font-semibold">
                    {{ statusMeta(item.status).label }}
                </span>
                <span class="block text-xs leading-snug opacity-80">
                    {{ item.meaning }}
                </span>
            </span>
        </li>
    </ul>
</template>
