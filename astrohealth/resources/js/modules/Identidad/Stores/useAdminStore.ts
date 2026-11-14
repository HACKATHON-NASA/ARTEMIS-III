import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import type {
    CreatedDevice,
    CrewOption,
    DeviceFormData,
    ManagedDevice,
    ManagedUser,
    MissionSummary,
    UserFormData,
} from '../Types/admin';
import {
    MOCK_CREW,
    MOCK_MISSION,
    MOCK_PENDING_ALERTS,
    mockDevices,
    mockUsers,
} from '../Utils/adminMockData';
import { isOnline } from '../Utils/time';

/**
 * Datos de la vista de administración (usuarios, dispositivos, misión).
 *
 * ⚠️ HOY usa datos de prueba (Utils/adminMockData.ts). Cuando el backend
 * tenga /api/v1/users y /api/v1/devices, solo cambia el interior de estas
 * acciones por llamadas a `api()` de @/lib/api: los componentes no cambian.
 */
const FAKE_LATENCY_MS = 350;
const wait = (ms = FAKE_LATENCY_MS) =>
    new Promise((resolve) => setTimeout(resolve, ms));

function randomToken(length: number): string {
    const alphabet = 'abcdefghijklmnopqrstuvwxyz0123456789';
    const bytes = crypto.getRandomValues(new Uint8Array(length));

    return Array.from(bytes, (byte) => alphabet[byte % alphabet.length]).join(
        '',
    );
}

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

    function crewName(astronautId: number | null): string | null {
        return (
            crew.value.find((member) => member.id === astronautId)?.name ?? null
        );
    }

    async function load(): Promise<void> {
        loading.value = true;
        await wait();
        mission.value = MOCK_MISSION;
        crew.value = MOCK_CREW;
        users.value = mockUsers();
        devices.value = mockDevices();
        pendingAlerts.value = MOCK_PENDING_ALERTS;
        loading.value = false;
    }

    async function saveUser(data: UserFormData, id?: number): Promise<void> {
        await wait();
        const fields = {
            name: data.name.trim(),
            role: data.role,
            astronaut_id: data.astronaut_id,
            astronaut_name: crewName(data.astronaut_id),
        };
        const existing = users.value.find((user) => user.id === id);

        if (existing) {
            Object.assign(existing, fields);

            return;
        }

        users.value.unshift({
            id: Math.max(0, ...users.value.map((user) => user.id)) + 1,
            email: data.email.trim().toLowerCase(),
            active: true,
            last_login_at: null,
            ...fields,
        });
    }

    async function setUserActive(id: number, active: boolean): Promise<void> {
        await wait(200);
        const user = users.value.find((item) => item.id === id);

        if (user) {
            user.active = active;
        }
    }

    async function createDevice(data: DeviceFormData): Promise<CreatedDevice> {
        await wait();
        const device: ManagedDevice = {
            id: Math.max(0, ...devices.value.map((item) => item.id)) + 1,
            name: data.name.trim(),
            client_id: `dev-${randomToken(12)}`,
            mission_id: mission.value?.id ?? 1,
            astronaut_id: data.astronaut_id,
            astronaut_name: crewName(data.astronaut_id),
            active: true,
            last_seen_at: null,
        };
        devices.value.unshift(device);

        return { device, client_secret: randomToken(48) };
    }

    async function setDeviceActive(id: number, active: boolean): Promise<void> {
        await wait(200);
        const device = devices.value.find((item) => item.id === id);

        if (device) {
            device.active = active;
        }
    }

    return {
        users,
        devices,
        crew,
        mission,
        loading,
        stats,
        load,
        saveUser,
        setUserActive,
        createDevice,
        setDeviceActive,
    };
});
