<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { MissionView } from '../Types/missions';
import { environmentInfo } from '../Utils/environments';
import { statusLabel } from '../Utils/missionDates';

const { t } = useI18n();

/**
 * Elegir de qué misión se ve la tripulación, la salud o las alertas. Quien
 * va en la nave solo ve la suya: el selector sale fijo, como un rótulo.
 */
defineProps<{ missions: MissionView[]; locked: boolean }>();

const model = defineModel<number | null>({ required: true });
</script>

<template>
    <div class="grid gap-2">
        <label for="mission-picker" class="text-sm font-medium">{{
            t('Entorno.mission')
        }}</label>
        <Select
            :model-value="model?.toString() ?? ''"
            :disabled="locked"
            @update:model-value="model = Number($event)"
        >
            <SelectTrigger id="mission-picker" class="w-full sm:w-72">
                <SelectValue :placeholder="t('Entorno.chooseMission')" />
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
                        {{ statusLabel(mission.status) }}
                    </span>
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
