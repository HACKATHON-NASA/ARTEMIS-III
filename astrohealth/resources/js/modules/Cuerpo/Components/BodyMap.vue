<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { computed, ref } from 'vue';
import { statusMeta } from '@/lib/status';
import type { BodyZoneCode } from '@/types/health';
import type { BodyPart, BodyStatus } from '../Types/body';
import { worstValue, zoneName } from '../Utils/zoneValues';
import { VIEWBOX, ZONES } from '../Utils/zones';
import BodyFigure from './BodyFigure.vue';
import BodyTooltip from './BodyTooltip.vue';
import BodyZone from './BodyZone.vue';

const { t } = useI18n();

/**
 * El cuerpo en SVG (respaldo del 3D): la figura, sus 9 zonas con su color y
 * el tooltip. Se usa si el equipo no soporta WebGL o el modelo no carga.
 * Clic en una zona → `select` (la pantalla abre el panel).
 */
const props = defineProps<{
    parts: BodyPart[];
    status: BodyStatus;
    selected: BodyZoneCode | null;
}>();

const emit = defineEmits<{ select: [code: BodyZoneCode] }>();

const hovered = ref<BodyZoneCode | null>(null);

function nameOf(code: BodyZoneCode): string {
    return zoneName(props.parts, code);
}

const tooltip = computed(() => {
    const zone = ZONES.find((item) => item.code === hovered.value);

    return zone
        ? {
              name: nameOf(zone.code),
              status: props.status[zone.code].status,
              value: worstValue(props.status, zone.code),
              ...zone.tip,
          }
        : null;
});

const legend = computed(() =>
    (['red', 'yellow', 'green', null] as const).map((status) =>
        statusMeta(status),
    ),
);
</script>

<template>
    <div class="flex flex-col items-center gap-4">
        <div class="relative w-full max-w-[300px]">
            <svg
                :viewBox="`0 0 ${VIEWBOX.width} ${VIEWBOX.height}`"
                class="w-full overflow-visible"
                role="group"
                :aria-label="t('Cuerpo.body3d.mapAria')"
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
