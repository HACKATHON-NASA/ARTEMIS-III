<script setup lang="ts">
import { computed, ref } from 'vue';
import { statusMeta } from '@/lib/status';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus, ZoneValue } from '../Types/body';
import { VIEWBOX, ZONES } from '../Utils/zones';
import BodyFigure from './BodyFigure.vue';
import BodyTooltip from './BodyTooltip.vue';
import BodyZone from './BodyZone.vue';

/**
 * El cuerpo interactivo (M3 ⭐): la figura, sus 9 zonas con su color y el
 * tooltip. Clic en una zona → `select` (la pantalla abre el panel).
 */
const props = defineProps<{
    parts: BodyPart[];
    status: BodyStatus;
    selected: BodyZoneCode | null;
}>();

const emit = defineEmits<{ select: [code: BodyZoneCode] }>();

const hovered = ref<BodyZoneCode | null>(null);

const ORDER = { red: 0, yellow: 1, green: 2 } as const;

function nameOf(code: BodyZoneCode): string {
    return props.parts.find((part) => part.code === code)?.name ?? code;
}

/** El valor que explica el color de la zona: el peor de sus indicadores. */
function worstValue(code: BodyZoneCode): ZoneValue | null {
    const values = [...props.status[code].values];

    return values.sort((a, b) => ORDER[a.status] - ORDER[b.status])[0] ?? null;
}

const tooltip = computed(() => {
    const zone = ZONES.find((item) => item.code === hovered.value);

    return zone
        ? {
              name: nameOf(zone.code),
              status: props.status[zone.code].status,
              value: worstValue(zone.code),
              ...zone.tip,
          }
        : null;
});

const legend = (['red', 'yellow', 'green', null] as const).map((status) =>
    statusMeta(status),
);
</script>

<template>
    <div class="flex flex-col items-center gap-4">
        <div class="relative w-full max-w-[300px]">
            <svg
                :viewBox="`0 0 ${VIEWBOX.width} ${VIEWBOX.height}`"
                class="w-full overflow-visible"
                role="group"
                aria-label="Cuerpo del astronauta: elige una zona para ver su detalle"
            >
                <BodyFigure />
                <BodyZone
                    v-for="zone in ZONES"
                    :key="zone.code"
                    :zone="zone"
                    :name="nameOf(zone.code)"
                    :status="status[zone.code].status"
                    :selected="selected === zone.code"
                    @select="emit('select', zone.code)"
                    @hover="hovered = $event ? zone.code : null"
                />
            </svg>

            <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="translate-y-1 opacity-0"
                leave-active-class="transition duration-100"
                leave-to-class="opacity-0"
            >
                <BodyTooltip
                    v-if="tooltip"
                    :key="tooltip.name"
                    v-bind="tooltip"
                />
            </Transition>
        </div>

        <ul
            class="flex flex-wrap justify-center gap-x-4 gap-y-1 text-xs text-muted-foreground"
        >
            <li
                v-for="item in legend"
                :key="item.label"
                class="flex items-center gap-1.5"
            >
                <span
                    class="size-2 rounded-full"
                    :style="{ backgroundColor: item.color }"
                />
                {{ item.label }}
            </li>
        </ul>
    </div>
</template>
