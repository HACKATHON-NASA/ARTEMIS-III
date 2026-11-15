<script setup lang="ts">
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

/**
 * Elegir de qué tripulante se ve la salud (médicos y administración).
 */
defineProps<{ members: { id: number; name: string; crew_role: string }[] }>();

const model = defineModel<number | null>({ required: true });
</script>

<template>
    <div class="grid gap-2">
        <label for="astronaut-picker" class="text-sm font-medium"
            >Astronauta</label
        >
        <Select
            :model-value="model?.toString() ?? ''"
            @update:model-value="model = Number($event)"
        >
            <SelectTrigger id="astronaut-picker" class="w-full sm:w-80">
                <SelectValue placeholder="Elige un tripulante" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="member in members"
                    :key="member.id"
                    :value="member.id.toString()"
                >
                    {{ member.name }}
                    <span class="text-muted-foreground"
                        >— {{ member.crew_role }}</span
                    >
                </SelectItem>
            </SelectContent>
        </Select>
    </div>
</template>
