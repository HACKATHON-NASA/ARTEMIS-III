<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Minus, Plus, RotateCcw } from '@lucide/vue';
import { computed, ref, watch } from 'vue';
import { Spinner } from '@/components/ui/spinner';
import type { BodyZoneCode } from '@/types/health';
import { useBodyScene } from '../Composables/useBodyScene';
import type { BodyPart, BodyStatus } from '../Types/body';
import { MODEL_CREDIT_URL } from '../Utils/bodyModel';
import { worstValue, zoneName } from '../Utils/zoneValues';
import BodyTooltip from './BodyTooltip.vue';

const { t } = useI18n();

/**
 * El cuerpo en 3D (M3 ⭐): modelo anatómico real con piel translúcida y los
 * órganos de cada zona pintados con su estado. Se gira arrastrando y se
 * acerca con la rueda. Si el equipo no soporta WebGL o el modelo no carga,
 * avisa con `unavailable` (la pantalla vuelve al cuerpo en SVG).
 */
const props = defineProps<{
    parts: BodyPart[];
    status: BodyStatus;
    selected: BodyZoneCode | null;
    /** Versión pequeña (Tripulación): sin botones de zoom ni crédito. */
    compact?: boolean;
}>();

const emit = defineEmits<{ select: [code: BodyZoneCode]; unavailable: [] }>();

const host = ref<HTMLElement | null>(null);
const canvas = ref<HTMLCanvasElement | null>(null);

const { state, resetView, zoom } = useBodyScene({
    host,
    canvas,
    status: () => props.status,
    selected: () => props.selected,
    onSelect: (code) => emit('select', code),
});

watch(
    () => state.failed,
    (failed) => failed && emit('unavailable'),
);

const tooltip = computed(() =>
    state.hovered
        ? {
              name: zoneName(props.parts, state.hovered.zone),
              status: props.status[state.hovered.zone].status,
              value: worstValue(props.status, state.hovered.zone),
              x: state.hovered.x,
              y: state.hovered.y,
          }
        : null,
);
</script>

<template>
    <div
        ref="host"
        class="relative w-full overflow-hidden rounded-2xl bg-[radial-gradient(ellipse_at_50%_45%,rgb(14_165_233/0.18),transparent_65%)]"
        :class="compact ? 'h-[380px]' : 'h-[520px] sm:h-[600px]'"
    >
        <canvas
            ref="canvas"
            class="absolute inset-0 size-full cursor-grab touch-none active:cursor-grabbing"
            role="img"
            :aria-label="t('Cuerpo.body3d.aria')"
        />

        <div
            v-if="state.loading && !state.failed"
            class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-sm text-muted-foreground"
        >
            <Spinner class="size-6" />
            {{ t('Cuerpo.body3d.loading') }}
        </div>

        <BodyTooltip v-if="tooltip" v-bind="tooltip" />

        <div
            v-if="!state.loading && !compact"
            class="absolute bottom-10 left-1/2 flex -translate-x-1/2 gap-1 rounded-full border bg-background/70 p-1 backdrop-blur"
        >
            <button
                v-for="action in [
                    {
                        label: t('Cuerpo.body3d.zoomIn'),
                        icon: Plus,
                        run: () => zoom(0.8),
                    },
                    {
                        label: t('Cuerpo.body3d.zoomOut'),
                        icon: Minus,
                        run: () => zoom(1.25),
                    },
                    {
                        label: t('Cuerpo.body3d.reset'),
                        icon: RotateCcw,
                        run: resetView,
                    },
                ]"
                :key="action.label"
                type="button"
                class="flex size-8 items-center justify-center rounded-full text-muted-foreground transition hover:bg-accent hover:text-accent-foreground focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
                :aria-label="action.label"
                :title="action.label"
                @click="action.run"
            >
                <component :is="action.icon" class="size-4" />
            </button>
        </div>

        <a
            v-if="!compact"
            :href="MODEL_CREDIT_URL"
            target="_blank"
            rel="noopener"
            class="absolute inset-x-0 bottom-2 px-4 text-center text-[10px] text-muted-foreground/80 hover:underline"
        >
            {{ t('Cuerpo.body3d.credit') }}
        </a>
    </div>
</template>
