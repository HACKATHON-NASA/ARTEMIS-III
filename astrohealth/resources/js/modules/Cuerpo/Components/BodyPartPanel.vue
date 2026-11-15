<script setup lang="ts">
import StatusBadge from '@/components/StatusBadge.vue';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import type { BodyPart, ZoneStatus } from '../Types/body';
import ZoneDetail from './ZoneDetail.vue';

/**
 * El panel de una zona que entra desde la derecha (Inicio del astronauta):
 * nombre, estado, función y el detalle (ZoneDetail).
 */
defineProps<{ part: BodyPart | null; zone: ZoneStatus | null }>();

const open = defineModel<boolean>('open', { required: true });
</script>

<template>
    <Sheet v-model:open="open">
        <SheetContent class="w-full gap-0 overflow-y-auto sm:max-w-md">
            <template v-if="part && zone">
                <SheetHeader class="border-b pb-5">
                    <div class="flex items-center gap-3">
                        <SheetTitle class="text-2xl">{{
                            part.name
                        }}</SheetTitle>
                        <StatusBadge :status="zone.status" size="md" />
                    </div>
                    <SheetDescription>{{
                        part.function_text
                    }}</SheetDescription>
                </SheetHeader>

                <div class="p-4">
                    <ZoneDetail :part="part" :zone="zone">
                        <template v-if="$slots.recommendation" #recommendation>
                            <slot name="recommendation" />
                        </template>
                    </ZoneDetail>
                </div>
            </template>
        </SheetContent>
    </Sheet>
</template>
