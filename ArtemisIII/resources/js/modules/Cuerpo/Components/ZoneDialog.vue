<script setup lang="ts">
import StatusBadge from '@/components/StatusBadge.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import type { BodyPart, ZoneStatus } from '../Types/body';
import ZoneDetail from './ZoneDetail.vue';

/**
 * La ventana del centro al tocar un órgano (Tripulación): de quién es, el
 * órgano con su estado, sus últimas mediciones, el riesgo en el espacio y
 * qué hacer (slot, lo pone la pantalla con las recomendaciones de M5).
 */
defineProps<{
    astronautName: string;
    part: BodyPart | null;
    zone: ZoneStatus | null;
}>();

const open = defineModel<boolean>('open', { required: true });
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            v-if="part && zone"
            class="max-h-[90vh] overflow-y-auto sm:max-w-xl"
        >
            <DialogHeader>
                <p
                    class="text-xs font-semibold tracking-[0.2em] text-sky-600 uppercase dark:text-sky-400"
                >
                    {{ astronautName }}
                </p>
                <div class="flex items-center gap-3">
                    <DialogTitle class="text-2xl">{{ part.name }}</DialogTitle>
                    <StatusBadge :status="zone.status" size="md" />
                </div>
                <DialogDescription>{{ part.function_text }}</DialogDescription>
            </DialogHeader>

            <ZoneDetail :part="part" :zone="zone">
                <template v-if="$slots.recommendation" #recommendation>
                    <slot name="recommendation" />
                </template>
            </ZoneDetail>
        </DialogContent>
    </Dialog>
</template>
