<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { LogOut } from '@lucide/vue';
import {
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import UserInfo from '@/components/UserInfo.vue';
import { acceso } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { Actor } from '@/types';

defineProps<{
    actor: Actor;
}>();

const auth = useAuthStore();

async function handleLogout(): Promise<void> {
    await auth.logout();
    router.flushAll();
    router.visit(acceso());
}
</script>

<template>
    <DropdownMenuLabel class="p-0 font-normal">
        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
            <UserInfo :actor="actor" :show-details="true" />
        </div>
    </DropdownMenuLabel>
    <DropdownMenuSeparator />
    <DropdownMenuItem
        class="cursor-pointer"
        data-test="logout-button"
        @select="handleLogout"
    >
        <LogOut class="mr-2 h-4 w-4" />
        Cerrar sesión
    </DropdownMenuItem>
</template>
