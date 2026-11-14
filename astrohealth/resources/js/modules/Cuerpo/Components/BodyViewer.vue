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
 * Debajo, las 9 zonas como botones (teclado y zonas sin órgano en el 3D).
 */
defineProps<{
    parts: BodyPart[];
    status: BodyStatus;
    selected: BodyZoneCode | null;
}>();

defineEmits<{ select: [code: BodyZoneCode] }>();

const Body3D = defineAsyncComponent(() => import('./Body3D.vue'));
const use3d = ref(true);
</script>

<template>
    <div class="flex flex-col gap-4">
        <Body3D
            v-if="use3d"
            :parts="parts"
            :status="status"
            :selected="selected"
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
            v-if="use3d"
            :parts="parts"
            :status="status"
            :selected="selected"
            @select="$emit('select', $event)"
        />
    </div>
</template>
