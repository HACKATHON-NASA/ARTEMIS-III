<script setup lang="ts">
import { defineAsyncComponent, ref } from 'vue';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus } from '../Types/body';
import BodyMap from './BodyMap.vue';
import ZoneChips from './ZoneChips.vue';

/**
 * El cuerpo del Inicio: intenta el 3D (modelo anatómico real) y, si el
 * equipo no soporta WebGL o el modelo no carga, muestra el cuerpo en SVG.
 * Three.js y el modelo se descargan aparte, solo cuando hacen falta.
 * A la derecha, las 9 zonas en columna como botones (teclado y zonas sin
 * órgano en el 3D).
 */
defineProps<{
    parts: BodyPart[];
    status: BodyStatus;
    selected: BodyZoneCode | null;
    /** Versión pequeña: cuerpo más bajo y sin la fila de zonas. */
    compact?: boolean;
}>();

defineEmits<{ select: [code: BodyZoneCode] }>();

const Body3D = defineAsyncComponent(() => import('./Body3D.vue'));
const use3d = ref(true);
</script>

<template>
    <div
        class="grid items-center gap-3"
        :class="use3d && !compact ? 'grid-cols-[minmax(0,1fr)_auto]' : ''"
    >
        <Body3D
            v-if="use3d"
            :parts="parts"
            :status="status"
            :selected="selected"
            :compact="compact"
            @select="$emit('select', $event)"
            @unavailable="use3d = false"
        />
        <BodyMap
            v-else
            :parts="parts"
            :status="status"
            :selected="selected"
            @select="$emit('select', $event)"
        />

        <ZoneChips
            v-if="use3d && !compact"
            class="self-center"
            :parts="parts"
            :status="status"
            :selected="selected"
            @select="$emit('select', $event)"
        />
    </div>
</template>
