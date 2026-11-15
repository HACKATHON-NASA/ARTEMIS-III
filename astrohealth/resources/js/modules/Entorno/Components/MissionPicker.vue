<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { MissionView } from '../Types/missions';
import { environmentInfo } from '../Utils/environments';
import { STATUS_LABELS } from '../Utils/missionDates';

/**
 * Elegir de qué misión se ve la tripulación, la salud o las alertas. Quien
 * va en la nave solo ve la suya: el selector sale fijo, como un rótulo.
 */
defineProps<{ missions: MissionView[]; locked: boolean }>();

const model = defineModel<number | null>({ required: true });
</script>

<template>
    <div class="grid gap-2">
        <label for="mission-picker" class="text-sm font-medium">Misión</label>
        <Select
            :model-value="model?.toString() ?? ''"
            :disabled="locked"
            @update:model-value="model = Number($event)"
        >
            <SelectTrigger id="mission-picker" class="w-full sm:w-72">
                <SelectValue placeholder="Elige una misión" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="mission in missions"
                    :key="mission.id"
                    :value="mission.id.toString()"
                >
                    {{ mission.name }}
                    <span class="text-muted-foreground">
                        · {{ environmentInfo(mission.environment).name }} ·
                        {{ STATUS_LABELS[mission.status] }}
                    </span>
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
