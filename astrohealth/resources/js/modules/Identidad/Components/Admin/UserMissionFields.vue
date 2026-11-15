<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { crewRoleLabel } from '../../../Entorno/Utils/environments';
import { Rocket } from '@lucide/vue';
import InputError from '@/components/InputError.vue';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { MissionOption } from '../../Types/admin';

const { t } = useI18n();

/**
 * La misión de la persona y, si va en la nave, su cargo en la tripulación.
 * Las misiones terminadas no se pueden elegir (salvo la que ya tenía).
 */
defineProps<{
    missions: MissionOption[];
    crewRoles: string[];
    withCrewRole: boolean;
    currentMission: number | null;
    errors: { mission?: string; crewRole?: string };
}>();

const mission = defineModel<string>('mission', { required: true });
const crewRole = defineModel<string>('crewRole', { required: true });
</script>

<template>
    <div
        class="grid gap-4 rounded-xl border border-sky-500/25 bg-sky-500/5 p-4"
    >
        <p class="flex items-center gap-2 text-sm font-medium">
            <Rocket class="size-4 text-sky-500" />
            {{
                withCrewRole
                    ? t('Identidad.admin.missionInSpace')
                    : t('Identidad.admin.missionFromEarth')
            }}
        </p>

        <div class="grid gap-2">
            <Label for="user-mission">{{ t('Identidad.admin.mission') }}</Label>
            <Select v-model="mission">
                <SelectTrigger id="user-mission" class="w-full bg-background">
                    <SelectValue
                        :placeholder="t('Identidad.admin.whichMission')"
                    />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="option in missions"
                        :key="option.id"
                        :value="option.id.toString()"
                        :disabled="
                            option.status === 'finished' &&
                            option.id !== currentMission
                        "
                    >
                        {{ option.name }}
                        <span class="text-muted-foreground">
                            · {{ option.place }} ·
                            {{
                                t(
                                    `Identidad.admin.missionStatus.${option.status}`,
                                )
                            }}
                        </span>
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="errors.mission" />
        </div>

        <div v-if="withCrewRole" class="grid gap-2">
            <Label for="user-crew-role">{{
                t('Identidad.admin.crewRole')
            }}</Label>
            <Select v-model="crewRole">
                <SelectTrigger id="user-crew-role" class="w-full bg-background">
                    <SelectValue
                        :placeholder="t('Identidad.admin.chooseCrewRole')"
                    />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem
                        v-for="option in crewRoles"
                        :key="option"
                        :value="option"
                    >
                        {{ crewRoleLabel(option) }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputError :message="errors.crewRole" />
        </div>
    </div>
</template>
