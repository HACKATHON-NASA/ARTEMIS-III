<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { formatNumber } from '@/lib/i18n';
import gsap from 'gsap';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import type { DoseRow } from '../../Types/crewTrends';

const { t } = useI18n();

/**
 * La radiación acumulada en la misión: una barra por tripulante (con su
 * color de identidad) y una marca con lo esperado para el entorno (dosis
 * base × días). Cada barra lleva su número escrito.
 */
const props = defineProps<{
    rows: DoseRow[];
    /** Dosis base del entorno en mSv/día (ficha M6). */
    baseDose: number;
    environmentName: string;
}>();

const root = ref<HTMLElement | null>(null);

const days = computed(() => Math.max(0, ...props.rows.map((row) => row.days)));
const expected = computed(() => props.baseDose * days.value);
const max = computed(
    () =>
        Math.max(expected.value, ...props.rows.map((row) => row.total)) *
            1.08 || 1,
);
const pct = (value: number) => `${(value / max.value) * 100}%`;
const mSv = (value: number) =>
    formatNumber(value, {
        minimumFractionDigits: 1,
        maximumFractionDigits: 1,
    });

async function grow(): Promise<void> {
    await nextTick();

    if (
        !root.value ||
        window.matchMedia('(prefers-reduced-motion: reduce)').matches
    ) {
        return;
    }

    const bars = root.value.querySelectorAll('[data-bar]');

    if (bars.length === 0) {
        return;
    }

    gsap.from(bars, {
        scaleX: 0,
        transformOrigin: 'left center',
        duration: 0.9,
        ease: 'power3.out',
        stagger: 0.08,
    });
}

onMounted(grow);
watch(() => props.rows, grow);
</script>

<template>
    <div ref="root" class="flex flex-col gap-3">
        <div
            v-for="row in rows"
            :key="row.astronaut_id"
            class="grid grid-cols-[7rem_minmax(0,1fr)_5.5rem] items-center gap-3"
        >
            <span class="truncate text-sm font-medium">{{
                row.name.split(' ')[0]
            }}</span>
            <div class="relative h-7 rounded-md bg-muted/60">
                <div
                    data-bar
                    class="absolute inset-y-1 left-0 rounded-r-[4px]"
                    :style="{ width: pct(row.total), background: row.color }"
                />
                <div
                    class="absolute -inset-y-1 w-0 border-l-2 border-dashed border-foreground/60"
                    :style="{ left: pct(expected) }"
                    aria-hidden="true"
                />
            </div>
            <span class="text-right text-sm tabular-nums">
                <strong>{{ mSv(row.total) }}</strong>
                <span class="text-muted-foreground"> mSv</span>
            </span>
        </div>

        <p class="flex items-center gap-2 text-xs text-muted-foreground">
            <span
                class="inline-block h-3 w-0 border-l-2 border-dashed border-foreground/60"
            />
            {{
                t('Dashboard.crew.expected', {
                    env: environmentName,
                    total: mSv(expected),
                    days,
                    base: formatNumber(baseDose),
                })
            }}
        </p>
    </div>
</template>
