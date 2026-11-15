<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { formatNumber } from '@/lib/i18n';
import gsap from 'gsap';
import { computed, nextTick, ref, toRef, watch } from 'vue';
import { statusMeta } from '@/lib/status';
import { useElementWidth } from '../../Composables/useElementWidth';
import {
    HEIGHT,
    PAD,
    useTrendGeometry,
} from '../../Composables/useTrendGeometry';
import type { Bands, CrewSeries } from '../../Types/crewTrends';
import type { ChartMeta } from '../../Types/crewTrends';

const { t } = useI18n();

/**
 * Un indicador, una línea por tripulante, día a día. La franja gris es el
 * rango sano. Al pasar el mouse (o con las flechas del teclado) una guía
 * muestra el valor de todos ese día. Las líneas se dibujan al cambiar de
 * indicador (sin animación con "reducir movimiento").
 */
const props = defineProps<{
    series: CrewSeries[];
    bands: Bands | null;
    /** Qué se grafica: nombre, unidad y decimales (un indicador o un test). */
    meta: ChartMeta;
}>();

const host = ref<HTMLElement | null>(null);
const svg = ref<SVGSVGElement | null>(null);
const width = useElementWidth(host);
const geo = useTrendGeometry(
    toRef(props, 'series'),
    toRef(props, 'bands'),
    width,
);
const fmt = (value: number) =>
    formatNumber(value, {
        minimumFractionDigits: props.meta.decimals,
        maximumFractionDigits: props.meta.decimals,
    });
const hoverDay = ref<number | null>(null);

const hovered = computed(() =>
    hoverDay.value === null
        ? []
        : geo.lines.value.flatMap((line) => {
              const point = line.points.find((p) => p.day === hoverDay.value);

              return point ? [{ line, point }] : [];
          }),
);

const tooltipLeft = computed(() => {
    if (hoverDay.value === null) {
        return 0;
    }

    const at = geo.x.value(hoverDay.value);

    return at > width.value - 280 ? at - 270 : at + 14;
});

function onMove(event: PointerEvent): void {
    const box = svg.value?.getBoundingClientRect();

    if (!box) {
        return;
    }

    const [first, last] = geo.days.value;
    const ratio =
        (event.clientX - box.left - PAD.left) /
        (geo.plotRight.value - PAD.left);
    hoverDay.value = Math.min(
        last,
        Math.max(first, Math.round(first + ratio * (last - first))),
    );
}

function onKey(step: number): void {
    const [first, last] = geo.days.value;
    hoverDay.value = Math.min(
        last,
        Math.max(first, (hoverDay.value ?? last) + step),
    );
}

// Dibuja las líneas cada vez que cambian los datos (con su largo real).
watch(
    () => [props.series, width.value > 0] as const,
    async () => {
        await nextTick();
        const paths = [
            ...(svg.value?.querySelectorAll<SVGPathElement>('[data-line]') ??
                []),
        ];

        if (
            paths.length === 0 ||
            window.matchMedia('(prefers-reduced-motion: reduce)').matches
        ) {
            return;
        }

        paths.forEach((path, index) => {
            const length = path.getTotalLength();
            gsap.fromTo(
                path,
                { strokeDasharray: length, strokeDashoffset: length },
                {
                    strokeDashoffset: 0,
                    duration: 1.1,
                    delay: index * 0.08,
                    ease: 'power2.out',
                    clearProps: 'strokeDasharray,strokeDashoffset',
                },
            );
        });
    },
    { immediate: true },
);
</script>

<template>
    <div ref="host" class="relative">
        <p
            v-if="geo.withData.value.length === 0"
            class="flex h-[300px] items-center justify-center text-sm text-muted-foreground"
        >
            {{ t('Dashboard.crew.notEnough') }}
        </p>

        <svg
            v-else-if="width > 0"
            ref="svg"
            :width="width"
            :height="HEIGHT"
            class="block touch-none outline-none focus-visible:ring-2 focus-visible:ring-ring/50"
            role="img"
            :aria-label="t('Dashboard.crew.chartAria', { name: meta.label })"
            tabindex="0"
            @pointermove="onMove"
            @pointerleave="hoverDay = null"
            @keydown.left.prevent="onKey(-1)"
            @keydown.right.prevent="onKey(1)"
            @blur="hoverDay = null"
        >
            <g class="text-[11px]" fill="currentColor">
                <g v-for="tick in geo.yTicks.value" :key="`y${tick}`">
                    <line
                        :x1="PAD.left"
                        :x2="geo.plotRight.value"
                        :y1="geo.y.value(tick)"
                        :y2="geo.y.value(tick)"
                        class="stroke-border"
                        stroke-width="1"
                    />
                    <text
                        :x="PAD.left - 8"
                        :y="geo.y.value(tick) + 4"
                        text-anchor="end"
                        class="fill-muted-foreground"
                    >
                        {{ fmt(tick) }}
                    </text>
                </g>
                <text
                    v-for="day in geo.xTicks.value"
                    :key="`x${day}`"
                    :x="geo.x.value(day)"
                    :y="HEIGHT - 8"
                    text-anchor="middle"
                    class="fill-muted-foreground"
                >
                    {{ t('Dashboard.crew.day', { n: day }) }}
                </text>
            </g>

            <g v-if="geo.band.value">
                <rect
                    :x="PAD.left"
                    :y="geo.band.value.top"
                    :width="geo.plotRight.value - PAD.left"
                    :height="geo.band.value.height"
                    class="fill-foreground/[0.06]"
                />
                <text
                    :x="PAD.left + 8"
                    :y="geo.band.value.top + 14"
                    class="fill-muted-foreground text-[11px]"
                >
                    {{ t('Dashboard.crew.healthyRange') }}
                </text>
            </g>

            <path
                v-for="line in geo.lines.value"
                :key="line.astronaut_id"
                data-line
                :d="line.path"
                fill="none"
                :stroke="line.color"
                stroke-width="2"
                stroke-linejoin="round"
                stroke-linecap="round"
            />

            <g
                v-for="{ line, y } in geo.labels.value"
                :key="`l${line.astronaut_id}`"
            >
                <circle
                    :cx="geo.x.value(line.last.day)"
                    :cy="geo.y.value(line.last.value)"
                    r="4.5"
                    :fill="line.color"
                    class="stroke-card"
                    stroke-width="2"
                />
                <text
                    :x="geo.plotRight.value + 12"
                    :y="y + 4"
                    class="fill-foreground text-xs font-medium"
                >
                    {{ line.name.split(' ')[0] }}
                    <tspan class="fill-muted-foreground font-normal">
                        {{ fmt(line.last.value) }}
                        <template v-if="!geo.compact.value">
                            {{ meta.unit }}
                        </template>
                    </tspan>
                </text>
            </g>

            <g v-if="hoverDay !== null">
                <line
                    :x1="geo.x.value(hoverDay)"
                    :x2="geo.x.value(hoverDay)"
                    :y1="PAD.top"
                    :y2="HEIGHT - PAD.bottom"
                    class="stroke-muted-foreground/50"
                    stroke-dasharray="3 3"
                />
                <circle
                    v-for="{ line, point } in hovered"
                    :key="`h${line.astronaut_id}`"
                    :cx="geo.x.value(point.day)"
                    :cy="geo.y.value(point.value)"
                    r="5"
                    :fill="line.color"
                    class="stroke-card"
                    stroke-width="2"
                />
            </g>
        </svg>

        <div
            v-if="hoverDay !== null && hovered.length"
            class="pointer-events-none absolute top-2 z-10 w-64 rounded-xl border bg-popover/95 p-3 text-sm shadow-lg backdrop-blur"
            :style="{ left: `${tooltipLeft}px` }"
            role="status"
        >
            <p class="mb-2 text-xs font-semibold text-muted-foreground">
                {{ t('Dashboard.crew.day', { n: hoverDay }) }}
            </p>
            <p
                v-for="{ line, point } in hovered"
                :key="line.astronaut_id"
                class="flex items-center gap-2 py-0.5"
            >
                <span
                    class="h-0.5 w-3 rounded-full"
                    :style="{ background: line.color }"
                />
                <span class="min-w-0 flex-1 truncate">{{
                    line.name.split(' ')[0]
                }}</span>
                <span class="font-semibold tabular-nums">{{
                    fmt(point.value)
                }}</span>
                <span
                    class="flex w-[4.5rem] items-center gap-1 text-xs text-muted-foreground"
                >
                    <span
                        class="size-2 shrink-0 rounded-full"
                        :style="{ background: statusMeta(point.status).color }"
                    />
                    {{ statusMeta(point.status).label }}
                </span>
            </p>
        </div>
    </div>
</template>
