import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api } from '@/lib/api';
import type {
    CreatedDevice,
    CrewOption,
    DeviceFormData,
    ManagedDevice,
    ManagedUser,
    MissionSummary,
    UserFormData,
    UserLink,
} from '../Types/admin';
import { isOnline } from '../Utils/time';

/**
 * Datos de la vista de administración (usuarios, dispositivos, misión).
 * Las misiones y sus tripulantes son de M6 (Entorno/Stores/useMissionsStore).
 *
 * Usuarios y dispositivos: /api/v1/users y /api/v1/devices (M0, users.manage
 * y devices.manage). Los errores salen como ApiError para que el formulario
 * los muestre en su campo.
 *
 * La misión del encabezado: GET /api/v1/missions/current (M6). Las alertas
 * por confirmar: las de GET /api/v1/alerts sin atender (M5).
 */
export const useAdminStore = defineStore('admin', () => {
    const users = ref<ManagedUser[]>([]);
    const devices = ref<ManagedDevice[]>([]);
    const crew = ref<CrewOption[]>([]);
    const mission = ref<MissionSummary | null>(null);
    const pendingAlerts = ref(0);
    const loading = ref(false);

    const stats = computed(() => ({
        activeUsers: users.value.filter((user) => user.active).length,
        totalUsers: users.value.length,
        crew: crew.value.length,
        onlineDevices: devices.value.filter(
            (device) => device.active && isOnline(device.last_seen_at),
        ).length,
        totalDevices: devices.value.length,
        pendingAlerts: pendingAlerts.value,
    }));

    async function load(): Promise<void> {
        loading.value = true;

        try {
            const [userList, deviceList, current, alerts] = await Promise.all([
                api<ManagedUser[]>('/users'),
                api<ManagedDevice[]>('/devices'),
                api<MissionSummary>('/missions/current'),
                api<{ acknowledged_at: string | null }[]>('/alerts'),
            ]);
            users.value = userList;
            devices.value = deviceList;
            mission.value = current;
            pendingAlerts.value = alerts.filter(
                (alert) => alert.acknowledged_at === null,
            ).length;
        } finally {
            loading.value = false;
        }
    }

    /** La tripulación de la misión actual (la pone el principal desde M6). */
    function setCrew(options: CrewOption[]): void {
        crew.value = options;
    }

    function replace<T extends { id: number }>(list: T[], item: T): void {
        const index = list.findIndex((current) => current.id === item.id);

        if (index === -1) {
            list.unshift(item);
        } else {
            list.splice(index, 1, item);
        }
    }

    async function saveUser(
        data: UserFormData,
        link: UserLink,
        id?: number,
    ): Promise<void> {
        const body = {
            name: data.name.trim(),
            email: data.email.trim(),
            role: data.role,
            mission_id: data.mission_id,
            astronaut_id: link.astronaut_id,
        };
        const saved = await api<ManagedUser>(
            id === undefined ? '/users' : `/users/${id}`,
            { method: id === undefined ? 'POST' : 'PATCH', body },
        );
        replace(users.value, saved);
    }

    async function setUserActive(id: number, active: boolean): Promise<void> {
        const saved = await api<ManagedUser>(`/users/${id}/active`, {
            method: 'PATCH',
            body: { active },
        });
        replace(users.value, saved);
    }

    async function createDevice(data: DeviceFormData): Promise<CreatedDevice> {
        const created = await api<CreatedDevice>('/devices', {
            method: 'POST',
            body: {
                name: data.name.trim(),
                mission_id: mission.value?.id ?? null,
                astronaut_id: data.astronaut_id,
            },
        });
        replace(devices.value, created.device);

        return created;
    }

    async function setDeviceActive(id: number, active: boolean): Promise<void> {
        const saved = await api<ManagedDevice>(`/devices/${id}/active`, {
            method: 'PATCH',
            body: { active },
        });
        replace(devices.value, saved);
    }

    return {
        users,
        devices,
        crew,
        mission,
        loading,
        stats,
        load,
        setCrew,
        saveUser,
        setUserActive,
        createDevice,
        setDeviceActive,
    };
});
