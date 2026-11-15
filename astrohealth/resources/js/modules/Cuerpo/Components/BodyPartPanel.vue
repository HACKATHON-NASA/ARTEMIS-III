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
 * La ventana del centro al tocar una zona (Inicio del astronauta): nombre,
 * estado, función y el detalle (ZoneDetail).
 */
defineProps<{ part: BodyPart | null; zone: ZoneStatus | null }>();

const open = defineModel<boolean>('open', { required: true });
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent
            v-if="part && zone"
            class="max-h-[90vh] overflow-y-auto sm:max-w-xl"
        >
            <DialogHeader>
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
