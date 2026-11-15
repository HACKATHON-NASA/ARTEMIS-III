<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Head, router } from '@inertiajs/vue3';
import { computed, nextTick, onMounted, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
import { Skeleton } from '@/components/ui/skeleton';
import { ApiError } from '@/lib/api';
import { dashboard, usuarios } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import AdminHeader from './Components/Admin/AdminHeader.vue';
import AdminStats from './Components/Admin/AdminStats.vue';
import DeviceFormDialog from './Components/Admin/DeviceFormDialog.vue';
import DevicesPanel from './Components/Admin/DevicesPanel.vue';
import UserFormDialog from './Components/Admin/UserFormDialog.vue';
import UsersPanel from './Components/Admin/UsersPanel.vue';
import { useReveal } from '@/composables/useReveal';
import { useMissionsStore } from '../Entorno/Stores/useMissionsStore';
import { CREW_ROLES, environmentInfo } from '../Entorno/Utils/environments';
import { useAdminStore } from './Stores/useAdminStore';
import type {
    ManagedDevice,
    ManagedUser,
    MissionOption,
    UserFormData,
    UserLink,
} from './Types/admin';
import { roleOption } from './Utils/roles';

const { t } = useI18n();

/**
 * M0 · Vista de administración (/admin/usuarios): usuarios y dispositivos.
 * Solo para quien tiene el permiso users.manage (el rol admin). Cada persona
 * de la nave o médico de vuelo va asociada a una misión de M6: al guardar,
 * el principal crea o actualiza su tripulante (useMissionsStore).
 * ⚠️ Por ahora con datos de prueba: ver Stores/useAdminStore.ts.
 */
defineOptions({
    layout: { breadcrumbs: [{ title: 'nav.users', href: usuarios() }] },
});

const auth = useAuthStore();
const store = useAdminStore();
const missions = useMissionsStore();
const root = ref<HTMLElement | null>(null);
const { reveal } = useReveal(root);

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

/** Las misiones para el formulario (las en curso primero). */
const missionOptions = computed<MissionOption[]>(() =>
    missions.views.map((mission) => ({
        id: mission.id,
        name: mission.name,
        status: mission.status,
        place: environmentInfo(mission.environment).name,
    })),
);

// Los dispositivos se asignan a la tripulación de la misión actual.
watch(
    () => missions.find(store.mission?.id ?? null)?.crew ?? [],
    (crew) =>
        store.setCrew(
            crew.map(({ id, name, crew_role }) => ({ id, name, crew_role })),
        ),
    { immediate: true },
);

/** Crea o actualiza el tripulante de una persona de la nave (M6). */
async function linkFor(data: UserFormData): Promise<UserLink> {
    const mission = missions.find(data.mission_id);
    const link: UserLink = {
        astronaut_id: null,
        astronaut_name: null,
        mission_name: mission?.name ?? null,
    };

    if (!roleOption(data.role)?.requiresAstronaut || mission === null) {
        return link;
    }

    const astronaut = {
        name: data.name,
        crew_role: data.crew_role ?? '',
        mission_id: mission.id,
    };
    const current = editing.value?.astronaut_id ?? null;
    const saved =
        (current !== null &&
            (await missions.updateAstronaut(current, astronaut))) ||
        (await missions.addAstronaut(astronaut));

    return { ...link, astronaut_id: saved.id, astronaut_name: saved.name };
}

async function saveUser(data: UserFormData): Promise<void> {
    await store.saveUser(data, await linkFor(data), editing.value?.id);
    toast.success(
        editing.value
            ? t('Identidad.admin.userUpdated')
            : t('Identidad.admin.userCreated', { name: data.name }),
    );
}

async function toggle(
    kind: 'user' | 'device',
    item: ManagedUser | ManagedDevice,
): Promise<void> {
    busy.value[kind] = item.id;
    const active = !item.active;

    try {
        if (kind === 'user') {
            await store.setUserActive(item.id, active);
        } else {
            await store.setDeviceActive(item.id, active);
        }

        toast(
            t(
                active
                    ? 'Identidad.admin.activated'
                    : 'Identidad.admin.deactivated',
                {
                    name: item.name,
                },
            ),
        );
    } catch (error) {
        toast.error(
            error instanceof ApiError
                ? error.message
                : t('Identidad.admin.couldNotChange'),
        );
    } finally {
        busy.value[kind] = null;
    }
}

onMounted(async () => {
    if (!auth.can('users.manage')) {
        toast.error(t('Identidad.admin.onlyAdmin'));
        router.visit(dashboard());

        return;
    }

    await Promise.all([store.load(), missions.load()]);
    await nextTick();
    reveal();
});
</script>

<template>
    <Head :title="t('Identidad.admin.pageTitle')" />

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
            :missions="missionOptions"
            :crew-roles="CREW_ROLES"
            :submit="saveUser"
        />
        <DeviceFormDialog
            v-model:open="deviceDialog"
            :crew="store.crew"
            :submit="store.createDevice"
        />
    </div>
</template>
