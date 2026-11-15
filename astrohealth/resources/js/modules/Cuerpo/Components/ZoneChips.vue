<script setup lang="ts">
import { statusMeta } from '@/lib/status';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus } from '../Types/body';

/**
 * Las 9 zonas como botones con su color. Sirven para elegir con el
 * teclado o el lector de pantalla, y para las zonas que no tienen un órgano
 * que tocar en el 3D (oído interno, todo el cuerpo).
 */
defineProps<{
    parts: BodyPart[];
    status: BodyStatus;
    selected: BodyZoneCode | null;
}>();

defineEmits<{ select: [code: BodyZoneCode] }>();
</script>

<template>
    <ul
        class="flex flex-wrap justify-center gap-2"
        aria-label="Zonas del cuerpo"
    >
        <li v-for="part in parts" :key="part.code">
            <button
                type="button"
                class="inline-flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-medium transition hover:border-sky-500/50 hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
                :class="{ 'border-sky-500 bg-accent': selected === part.code }"
                :aria-label="`${part.name}: ${statusMeta(status[part.code].status).label}`"
                :aria-pressed="selected === part.code"
                @click="$emit('select', part.code)"
            >
                <span
                    class="size-2 rounded-full"
                    :style="{
                        backgroundColor: statusMeta(status[part.code].status)
                            .color,
                        boxShadow: `0 0 8px ${statusMeta(status[part.code].status).color}`,
                    }"
                />
                {{ part.name }}
            </button>
        </li>
    </ul>
</template>
