<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { nextTick, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { admin, dashboard } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import AdminHeader from './Components/Admin/AdminHeader.vue';
import AdminStats from './Components/Admin/AdminStats.vue';
import DeviceFormDialog from './Components/Admin/DeviceFormDialog.vue';
import DevicesPanel from './Components/Admin/DevicesPanel.vue';
import UserFormDialog from './Components/Admin/UserFormDialog.vue';
import UsersPanel from './Components/Admin/UsersPanel.vue';
import { useAdminMotion } from './Composables/useAdminMotion';
import { useAdminStore } from './Stores/useAdminStore';
import type { ManagedDevice, ManagedUser, UserFormData } from './Types/admin';

/**
 * M0 · Vista de administración (/admin): usuarios y dispositivos.
 * Solo para quien tiene el permiso users.manage (el rol admin).
 * ⚠️ Por ahora con datos de prueba: ver Stores/useAdminStore.ts.
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'Administración', href: admin() }] },
});

const auth = useAuthStore();
const store = useAdminStore();
const root = ref<HTMLElement | null>(null);
const motion = useAdminMotion(root);

const userDialog = ref(false);
const editing = ref<ManagedUser | null>(null);
const deviceDialog = ref(false);
// Qué fila está esperando al servidor (separado: un usuario y un dispositivo pueden tener el mismo id).
const busy = ref<{ user: number | null; device: number | null }>({
    user: null,
    device: null,
});

function openUser(user: ManagedUser | null): void {
    editing.value = user;
    userDialog.value = true;
}

async function saveUser(data: UserFormData): Promise<void> {
    await store.saveUser(data, editing.value?.id);
    toast.success(
        editing.value ? 'Usuario actualizado' : `${data.name} ya tiene acceso`,
    );
}

async function toggle(
    kind: 'user' | 'device',
    item: ManagedUser | ManagedDevice,
): Promise<void> {
    busy.value[kind] = item.id;
    const active = !item.active;

    if (kind === 'user') {
        await store.setUserActive(item.id, active);
    } else {
        await store.setDeviceActive(item.id, active);
    }

    busy.value[kind] = null;
    toast(`${item.name} ${active ? 'activado' : 'desactivado'}`);
}

onMounted(async () => {
    if (!auth.can('users.manage')) {
        toast.error('Esa vista es solo para administración.');
        router.visit(dashboard());

        return;
    }

    await store.load();
    await nextTick();
    motion.reveal();
});
</script>

<template>
    <Head title="Administración" />

    <div ref="root" class="flex flex-col gap-6 p-4 sm:p-6">
        <template v-if="store.loading || store.mission === null">
            <Skeleton class="h-48 rounded-2xl" />
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Skeleton v-for="n in 4" :key="n" class="h-32 rounded-2xl" />
            </div>
            <Skeleton class="h-96 rounded-2xl" />
        </template>

        <template v-else>
            <AdminHeader :mission="store.mission" />
            <AdminStats :stats="store.stats" />
            <UsersPanel
                :users="store.users"
                :busy-id="busy.user"
                @create="openUser(null)"
                @edit="openUser"
                @toggle="toggle('user', $event)"
            />
            <DevicesPanel
                :devices="store.devices"
                :busy-id="busy.device"
                @create="deviceDialog = true"
                @toggle="toggle('device', $event)"
            />
        </template>

        <UserFormDialog
            v-model:open="userDialog"
            :user="editing"
            :crew="store.crew"
            :submit="saveUser"
        />
        <DeviceFormDialog
            v-model:open="deviceDialog"
            :crew="store.crew"
            :submit="store.createDevice"
        />
    </div>
</template>
