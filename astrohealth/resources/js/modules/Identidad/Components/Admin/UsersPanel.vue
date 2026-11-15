<script setup lang="ts">
import { Search, UserPlus } from '@lucide/vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { BRAND } from '@/lib/brand';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { ManagedUser } from '../../Types/admin';
import { ROLE_OPTIONS } from '../../Utils/roles';
import UserRow from './UserRow.vue';

/**
 * Las personas con acceso: buscar, filtrar por rol, crear, editar y
 * activar o desactivar.
 */
const props = defineProps<{ users: ManagedUser[]; busyId: number | null }>();
defineEmits<{
    create: [];
    edit: [user: ManagedUser];
    toggle: [user: ManagedUser];
}>();

const search = ref('');
const roleFilter = ref('all');

const visible = computed(() => {
    const term = search.value.trim().toLowerCase();

    return props.users.filter(
        (user) =>
            (roleFilter.value === 'all' ||
                (roleFilter.value === 'none' && user.role === null) ||
                user.role === roleFilter.value) &&
            (term === '' ||
                user.name.toLowerCase().includes(term) ||
                user.email.includes(term)),
    );
});
</script>

<template>
    <section class="overflow-hidden rounded-2xl border bg-card" data-reveal>
        <div
            class="flex flex-col gap-4 border-b p-4 sm:p-6 lg:flex-row lg:items-center lg:justify-between"
        >
            <div>
                <h2 class="text-lg font-semibold">Usuarios</h2>
                <p class="text-sm text-muted-foreground">
                    {{ users.length }} personas con acceso a {{ BRAND.name }}
                </p>
            </div>

            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                <div class="relative">
                    <Search
                        class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                    />
                    <Input
                        v-model="search"
                        type="search"
                        placeholder="Buscar por nombre o correo"
                        class="pl-9 sm:w-64"
                        aria-label="Buscar usuarios"
                    />
                </div>
                <Select v-model="roleFilter">
                    <SelectTrigger class="sm:w-48" aria-label="Filtrar por rol">
                        <SelectValue />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Todos los roles</SelectItem>
                        <SelectItem
                            v-for="option in ROLE_OPTIONS"
                            :key="option.value"
                            :value="option.value"
                        >
                            {{ option.label }}
                        </SelectItem>
                        <SelectItem value="none">Sin rol</SelectItem>
                    </SelectContent>
                </Select>
                <Button
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white shadow-[0_8px_20px_-8px_rgb(14_165_233/0.7)] hover:brightness-110"
                    @click="$emit('create')"
                >
                    <UserPlus />
                    Nuevo usuario
                </Button>
            </div>
        </div>

        <div
            class="hidden grid-cols-[minmax(0,2.2fr)_minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_auto] gap-x-4 border-b bg-muted/40 px-6 py-2.5 text-xs font-medium tracking-wide text-muted-foreground uppercase md:grid"
            aria-hidden="true"
        >
            <span>Persona</span>
            <span>Rol</span>
            <span>Estado</span>
            <span>Último acceso</span>
            <span class="w-[4.5rem]" />
        </div>

        <TransitionGroup
            tag="ul"
            class="relative divide-y"
            move-class="transition duration-300"
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="-translate-y-1 opacity-0"
            leave-active-class="absolute inset-x-0 transition duration-200"
            leave-to-class="opacity-0"
        >
            <UserRow
                v-for="user in visible"
                :key="user.id"
                :user="user"
                :busy="busyId === user.id"
                @edit="$emit('edit', user)"
                @toggle="$emit('toggle', user)"
            />
        </TransitionGroup>

        <p
            v-if="visible.length === 0"
            class="px-6 py-10 text-center text-sm text-muted-foreground"
        >
            Ningún usuario coincide con la búsqueda.
        </p>
    </section>
</template>
