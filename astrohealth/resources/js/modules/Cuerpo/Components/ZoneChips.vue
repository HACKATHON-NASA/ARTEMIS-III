<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { statusMeta } from '@/lib/status';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus } from '../Types/body';

const { t } = useI18n();

/**
 * Las 9 zonas como botones con su color. Sirven para elegir con el
 * teclado o el lector de pantalla, y para las zonas que no tienen un órgano
 * que tocar en el 3D (oído interno, todo el cuerpo). Van en columna, a la
 * derecha del cuerpo.
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
        class="flex flex-col gap-1.5"
        :aria-label="t('Cuerpo.body3d.zonesAria')"
    >
        <li v-for="part in parts" :key="part.code">
            <button
                type="button"
                class="flex w-full items-center gap-2 rounded-full border px-2.5 py-1.5 text-left text-[11px] font-medium transition hover:border-sky-500/50 hover:bg-accent focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none sm:px-3 sm:text-xs"
                :class="{ 'border-sky-500 bg-accent': selected === part.code }"
                :aria-label="`${part.name}: ${statusMeta(status[part.code].status).label}`"
                :aria-pressed="selected === part.code"
                @click="$emit('select', part.code)"
            >
                <span
                    class="size-2 shrink-0 rounded-full"
                    :style="{
                        backgroundColor: statusMeta(status[part.code].status)
                            .color,
                        boxShadow: `0 0 8px ${statusMeta(status[part.code].status).color}`,
                    }"
                />
                <span class="max-w-[7.5rem] truncate sm:max-w-none">{{
                    part.name
                }}</span>
            </button>
        </li>
    </ul>
</template>
