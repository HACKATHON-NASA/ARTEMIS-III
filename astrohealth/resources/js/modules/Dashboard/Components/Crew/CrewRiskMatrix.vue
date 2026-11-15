<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { statusMeta } from '@/lib/status';
import type { IndicatorCode } from '@/types/health';
import type { CrewSnapshotRow } from '../../Types/crewTrends';
import { CHART_INDICATORS, formatValue } from '../../Utils/indicatorMeta';

const { t } = useI18n();

/**
 * El mapa de riesgos: una fila por tripulante y una columna por indicador,
 * cada celda con el último valor y su color del semáforo (con el número
 * escrito: el color nunca va solo). Tocar una columna la abre en la gráfica.
 */
defineProps<{
    rows: CrewSnapshotRow[];
    selected: IndicatorCode;
}>();

defineEmits<{ pick: [code: IndicatorCode] }>();
</script>

<template>
    <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        <table
            class="w-full min-w-[760px] border-separate border-spacing-1 text-sm"
        >
            <caption class="sr-only">
                Último valor de cada indicador por tripulante
            </caption>
            <thead>
                <tr>
                    <th
                        scope="col"
                        class="w-40 text-left text-xs font-medium text-muted-foreground"
                    >
                        {{ t('Dashboard.crew.member') }}
                    </th>
                    <th
                        v-for="item in CHART_INDICATORS"
                        :key="item.code"
                        scope="col"
                        class="p-0 text-xs font-medium"
                    >
                        <button
                            type="button"
                            class="w-full rounded-md px-1 py-1.5 transition hover:bg-accent hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
                            :class="
                                selected === item.code
                                    ? 'bg-sky-500/15 text-sky-700 dark:text-sky-300'
                                    : 'text-muted-foreground'
                            "
                            :aria-pressed="selected === item.code"
                            :title="
                                t('Dashboard.crew.seeInChart', {
                                    name: item.label,
                                })
                            "
                            @click="$emit('pick', item.code)"
                        >
                            {{ item.short }}
                        </button>
                    </th>
                    <th
                        scope="col"
                        class="text-xs font-medium text-muted-foreground"
                    >
                        {{ t('Dashboard.crew.watch') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="row in rows" :key="row.astronaut_id">
                    <th scope="row" class="pr-2 text-left font-medium">
                        <span class="flex items-center gap-2">
                            <span
                                class="h-0.5 w-3 shrink-0 rounded-full"
                                :style="{ background: row.color }"
                            />
                            <span class="truncate">{{ row.name }}</span>
                        </span>
                    </th>
                    <td
                        v-for="item in CHART_INDICATORS"
                        :key="item.code"
                        class="cursor-pointer rounded-lg border text-center transition hover:brightness-110"
                        :class="[
                            statusMeta(row.values[item.code]?.status ?? null)
                                .soft,
                            {
                                'ring-2 ring-sky-500/60':
                                    selected === item.code,
                            },
                        ]"
                        :title="
                            row.values[item.code]
                                ? t('Dashboard.crew.cellTitle', {
                                      who: row.name,
                                      name: item.label,
                                      value: formatValue(
                                          row.values[item.code]!.value,
                                          item.code,
                                      ),
                                      unit: item.unit,
                                      status: statusMeta(
                                          row.values[item.code]!.status,
                                      ).label,
                                  })
                                : t('Dashboard.crew.cellNoData', {
                                      who: row.name,
                                      name: item.label,
                                  })
                        "
                        @click="$emit('pick', item.code)"
                    >
                        <span class="block py-2 font-semibold tabular-nums">
                            {{
                                row.values[item.code]
                                    ? formatValue(
                                          row.values[item.code]!.value,
                                          item.code,
                                      )
                                    : '—'
                            }}
                        </span>
                    </td>
                    <td class="text-center">
                        <span
                            class="inline-flex min-w-8 justify-center rounded-full px-2 py-0.5 text-xs font-semibold tabular-nums"
                            :class="
                                row.attention
                                    ? statusMeta('yellow').soft
                                    : 'text-muted-foreground'
                            "
                        >
                            {{ row.attention }}
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
