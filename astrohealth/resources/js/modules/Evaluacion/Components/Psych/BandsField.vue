<script setup lang="ts">
import { Plus, Trash2 } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { statusMeta } from '@/lib/status';
import type { Status } from '@/types/health';
import type { PsychBand } from '../../Types/psych';

/**
 * Cómo se interpreta el puntaje: hasta qué puntaje va cada nivel, su nombre
 * y su color del semáforo. Van de menor a mayor.
 */
const bands = defineModel<PsychBand[]>({ required: true });

const STATUSES: Status[] = ['green', 'yellow', 'red'];

function add(): void {
    const last = bands.value.at(-1)?.max ?? 0;
    bands.value = [...bands.value, { max: last + 5, label: '', status: 'red' }];
}
</script>

<template>
    <fieldset class="grid gap-2">
        <legend class="mb-1 text-sm font-medium">Cómo se interpreta</legend>
        <div
            v-for="(band, index) in bands"
            :key="index"
            class="flex flex-wrap items-center gap-2"
        >
            <span class="text-xs text-muted-foreground">hasta</span>
            <Input
                v-model.number="band.max"
                type="number"
                class="w-20 tabular-nums"
                :aria-label="`Puntaje máximo del nivel ${index + 1}`"
            />
            <Input
                v-model="band.label"
                class="min-w-0 flex-1"
                :aria-label="`Nombre del nivel ${index + 1}`"
                placeholder="Ej.: Leve"
            />
            <div
                class="flex gap-1"
                role="radiogroup"
                :aria-label="`Color del nivel ${index + 1}`"
            >
                <button
                    v-for="status in STATUSES"
                    :key="status"
                    type="button"
                    role="radio"
                    :aria-checked="band.status === status"
                    :aria-label="statusMeta(status).label"
                    :title="statusMeta(status).label"
                    class="size-6 rounded-full border-2 transition"
                    :class="
                        band.status === status
                            ? 'scale-110 border-foreground'
                            : 'border-transparent opacity-50'
                    "
                    :style="{ background: statusMeta(status).color }"
                    @click="band.status = status"
                />
            </div>
            <Button
                variant="ghost"
                size="icon-sm"
                :aria-label="`Quitar el nivel ${index + 1}`"
                @click="bands = bands.filter((_, i) => i !== index)"
            >
                <Trash2 />
            </Button>
        </div>
        <Button
            variant="outline"
            size="sm"
            class="justify-self-start"
            @click="add"
            ><Plus /> Agregar nivel</Button
        >
    </fieldset>
</template>
