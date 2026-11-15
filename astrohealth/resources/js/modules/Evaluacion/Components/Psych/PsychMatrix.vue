<script setup lang="ts">
import {
    ArrowDownRight,
    ArrowRight,
    ArrowUpRight,
    TriangleAlert,
} from '@lucide/vue';
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { PsychResult, PsychTest } from '../../Types/psych';

/**
 * El mapa de la tripulación: una fila por tripulante y una columna por test
 * (primero los psicológicos, después los cognitivos), con el último puntaje,
 * su nivel y si subió o bajó desde la vez anterior. En todos estos tests,
 * subir es empeorar. Tocar una columna la abre en la gráfica.
 */
const props = defineProps<{
    members: { id: number; name: string }[];
    tests: PsychTest[];
    /** Historial de un tripulante en un test (del más viejo al más nuevo). */
    history: (astronautId: number, testId: number) => PsychResult[];
    selected: number | null;
}>();

defineEmits<{ pick: [testId: number] }>();

const ordered = computed(() =>
    [...props.tests].sort(
        (a, b) =>
            Number(a.kind === 'cognitive') - Number(b.kind === 'cognitive'),
    ),
);
const groups = computed(() => [
    {
        label: 'Psicológico',
        span: ordered.value.filter((t) => t.kind === 'psychological').length,
    },
    {
        label: 'Intelectual (cognitivo)',
        span: ordered.value.filter((t) => t.kind === 'cognitive').length,
    },
]);

function cell(astronautId: number, testId: number) {
    const all = props.history(astronautId, testId);
    const last = all.at(-1) ?? null;
    const before = all.at(-2) ?? null;
    const delta = last && before ? last.score - before.score : 0;

    return {
        last,
        trend: delta > 0 ? 'up' : delta < 0 ? 'down' : 'flat',
    } as const;
}
</script>

<template>
    <div class="-mx-4 overflow-x-auto px-4 sm:mx-0 sm:px-0">
        <table
            class="w-full min-w-[640px] border-separate border-spacing-1 text-sm"
        >
            <caption class="sr-only">
                Último resultado de cada test por tripulante
            </caption>
            <thead>
                <tr class="text-xs text-muted-foreground">
                    <th />
                    <th
                        v-for="group in groups.filter((g) => g.span)"
                        :key="group.label"
                        :colspan="group.span"
                        class="pb-1 font-medium"
                    >
                        {{ group.label }}
                    </th>
                </tr>
                <tr>
                    <th
                        scope="col"
                        class="w-40 text-left text-xs font-medium text-muted-foreground"
                    >
                        Tripulante
                    </th>
                    <th
                        v-for="test in ordered"
                        :key="test.id"
                        scope="col"
                        class="p-0 text-xs font-medium"
                    >
                        <button
                            type="button"
                            class="w-full rounded-md px-1 py-1.5 transition hover:bg-accent"
                            :class="
                                selected === test.id
                                    ? 'bg-sky-500/15 text-sky-700 dark:text-sky-300'
                                    : 'text-muted-foreground'
                            "
                            :aria-pressed="selected === test.id"
                            :title="`Ver ${test.name} en la gráfica`"
                            @click="$emit('pick', test.id)"
                        >
                            {{ test.code }}
                        </button>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="member in members" :key="member.id">
                    <th scope="row" class="truncate pr-2 text-left font-medium">
                        {{ member.name }}
                    </th>
                    <td
                        v-for="test in ordered"
                        :key="test.id"
                        class="cursor-pointer rounded-lg border px-2 py-1.5 text-center"
                        :class="[
                            statusMeta(
                                cell(member.id, test.id).last?.status ?? null,
                            ).soft,
                            { 'ring-2 ring-sky-500/60': selected === test.id },
                        ]"
                        :title="
                            cell(member.id, test.id).last
                                ? `${member.name} · ${test.name}: ${cell(member.id, test.id).last!.score} ${test.unit} (${cell(member.id, test.id).last!.band})`
                                : `${member.name} · ${test.name}: sin datos`
                        "
                        @click="$emit('pick', test.id)"
                    >
                        <template v-if="cell(member.id, test.id).last">
                            <span
                                class="flex items-center justify-center gap-1 font-semibold tabular-nums"
                            >
                                {{ cell(member.id, test.id).last!.score }}
                                <ArrowUpRight
                                    v-if="
                                        cell(member.id, test.id).trend === 'up'
                                    "
                                    class="size-3.5"
                                    aria-label="subió"
                                />
                                <ArrowDownRight
                                    v-else-if="
                                        cell(member.id, test.id).trend ===
                                        'down'
                                    "
                                    class="size-3.5"
                                    aria-label="bajó"
                                />
                                <ArrowRight
                                    v-else
                                    class="size-3.5 opacity-50"
                                    aria-label="igual"
                                />
                                <TriangleAlert
                                    v-if="
                                        cell(member.id, test.id).last!.flagged
                                    "
                                    class="size-3.5 text-[var(--status-red)]"
                                    aria-label="respuesta de riesgo"
                                />
                            </span>
                            <span class="block text-[10px] opacity-80">{{
                                cell(member.id, test.id).last!.band
                            }}</span>
                        </template>
                        <span v-else class="text-muted-foreground">—</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
