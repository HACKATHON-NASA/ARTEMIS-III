import { computed } from 'vue';
import type { Ref } from 'vue';
import type { Bands, CrewSeries } from '../Types/crewTrends';
import { linear, niceTicks } from '../Utils/chartScale';

/**
 * La geometría de la gráfica de líneas: escalas, marcas de los ejes, la
 * banda del rango sano, el trazo de cada tripulante y dónde va su etiqueta
 * final (separadas para que no se encimen).
 */
export const PAD = { top: 16, right: 132, bottom: 30, left: 44 };
export const HEIGHT = 300;
const LABEL_GAP = 18;

export function useTrendGeometry(
    series: Ref<CrewSeries[]>,
    bands: Ref<Bands | null>,
    width: Ref<number>,
) {
    const withData = computed(() =>
        series.value.filter((item) => item.points.length > 0),
    );

    const days = computed(() => {
        const all = withData.value.flatMap((item) =>
            item.points.map((p) => p.day),
        );

        return all.length ? [Math.min(...all), Math.max(...all)] : [1, 1];
    });

    const yTicks = computed(() => {
        const values = withData.value.flatMap((item) =>
            item.points.map((p) => p.value),
        );
        const band = bands.value;

        if (band) {
            values.push(
                band.green_min,
                Math.min(band.green_max, Math.max(...values) * 1.2),
            );
        }

        const min = Math.min(...values);
        const max = Math.max(...values);
        const pad = (max - min || 1) * 0.12;

        return niceTicks(min - pad, max + pad, 5);
    });

    /** En pantallas angostas las etiquetas finales van sin unidad: menos margen. */
    const compact = computed(() => width.value < 520);
    const plotRight = computed(() =>
        Math.max(PAD.left + 60, width.value - (compact.value ? 84 : PAD.right)),
    );
    const x = computed(() =>
        linear(days.value as [number, number], [PAD.left, plotRight.value]),
    );
    const y = computed(() =>
        linear(
            [yTicks.value[0], yTicks.value[yTicks.value.length - 1]],
            [HEIGHT - PAD.bottom, PAD.top],
        ),
    );

    const xTicks = computed(() => {
        const [first, last] = days.value;
        const step = Math.max(
            1,
            Math.ceil(
                (last - first + 1) /
                    Math.max(2, Math.floor((plotRight.value - PAD.left) / 70)),
            ),
        );
        const ticks: number[] = [];

        for (let day = last; day >= first; day -= step) {
            ticks.unshift(day);
        }

        return ticks;
    });

    /** La banda del rango sano, recortada al área visible. */
    const band = computed(() => {
        if (!bands.value) {
            return null;
        }

        const top = Math.max(PAD.top, y.value(bands.value.green_max));
        const bottom = Math.min(
            HEIGHT - PAD.bottom,
            y.value(bands.value.green_min),
        );

        return bottom > top ? { top, height: bottom - top } : null;
    });

    const lines = computed(() =>
        withData.value.map((item) => ({
            ...item,
            path: item.points
                .map(
                    (p, i) =>
                        `${i ? 'L' : 'M'}${x.value(p.day).toFixed(1)},${y.value(p.value).toFixed(1)}`,
                )
                .join(''),
            last: item.points[item.points.length - 1],
        })),
    );

    /** Las etiquetas finales, ordenadas por altura y separadas como mínimo LABEL_GAP. */
    const labels = computed(() => {
        const placed = lines.value
            .map((line) => ({ line, y: y.value(line.last.value) }))
            .sort((a, b) => a.y - b.y);

        for (let i = 1; i < placed.length; i++) {
            placed[i].y = Math.max(placed[i].y, placed[i - 1].y + LABEL_GAP);
        }

        return placed;
    });

    return {
        compact,
        withData,
        days,
        x,
        y,
        xTicks,
        yTicks,
        band,
        lines,
        labels,
        plotRight,
    };
}
