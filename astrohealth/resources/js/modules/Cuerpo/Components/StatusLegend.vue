<script setup lang="ts">
import { statusMeta } from '@/lib/status';
import type { MaybeStatus } from '@/types/health';

/**
 * Qué significa cada color del semáforo médico: el punto y su nombre. Los
 * colores salen de statusMeta (las variables --status-*).
 */
const STATUSES: MaybeStatus[] = ['green', 'yellow', 'red', null];
</script>

<template>
    <ul
        class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm"
        aria-label="Qué significa cada color"
    >
        <li
            v-for="status in STATUSES"
            :key="status ?? 'none'"
            class="flex items-center gap-2"
        >
            <span
                class="size-2.5 rounded-full"
                :style="{ background: statusMeta(status).color }"
            />
            {{ statusMeta(status).label }}
        </li>
    </ul>
</template>
