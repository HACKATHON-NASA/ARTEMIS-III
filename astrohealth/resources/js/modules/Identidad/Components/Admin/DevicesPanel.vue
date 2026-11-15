<script setup lang="ts">
import { Plus } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import type { ManagedDevice } from '../../Types/admin';
import DeviceCard from './DeviceCard.vue';

/**
 * Las máquinas de la nave (el simulador y los sensores) que envían lecturas.
 */
defineProps<{ devices: ManagedDevice[]; busyId: number | null }>();
defineEmits<{ create: []; toggle: [device: ManagedDevice] }>();
</script>

<template>
    <section class="rounded-2xl border bg-card" data-reveal>
        <div
            class="flex flex-col gap-4 border-b p-4 sm:flex-row sm:items-center sm:justify-between sm:p-6"
        >
            <div>
                <h2 class="text-lg font-semibold">Dispositivos</h2>
                <p class="text-sm text-muted-foreground">
                    Sensores y simulador que envían las lecturas de la nave
                </p>
            </div>
            <Button variant="outline" @click="$emit('create')">
                <Plus />
                Registrar dispositivo
            </Button>
        </div>

        <TransitionGroup
            tag="div"
            class="grid gap-4 p-4 sm:p-6 md:grid-cols-2"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="scale-95 opacity-0"
        >
            <DeviceCard
                v-for="device in devices"
                :key="device.id"
                :device="device"
                :busy="busyId === device.id"
                @toggle="$emit('toggle', device)"
            />
        </TransitionGroup>
    </section>
</template>
