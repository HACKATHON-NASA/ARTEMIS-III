<script setup lang="ts">
import StatusBadge from '@/components/StatusBadge.vue';
import type { BodyPart, ZoneStatus } from '../Types/body';
import ZoneDetail from './ZoneDetail.vue';

/**
 * El detalle de la zona elegida, fijo al lado del cuerpo (Panel de salud).
 * Cambia con una transición corta al elegir otra zona.
 */
defineProps<{ part: BodyPart; zone: ZoneStatus }>();
</script>

<template>
    <Transition
        mode="out-in"
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-2 opacity-0"
        leave-active-class="transition duration-150"
        leave-to-class="opacity-0"
    >
        <div :key="part.code" class="flex flex-col gap-5">
            <header>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-semibold tracking-tight">
                        {{ part.name }}
                    </h3>
                    <StatusBadge :status="zone.status" size="md" />
                </div>
                <p class="mt-1 text-sm text-muted-foreground">
                    {{ part.function_text }}
                </p>
            </header>

            <ZoneDetail :part="part" :zone="zone">
                <template v-if="$slots.recommendation" #recommendation>
                    <slot name="recommendation" />
                </template>
            </ZoneDetail>
        </div>
    </Transition>
</template>
