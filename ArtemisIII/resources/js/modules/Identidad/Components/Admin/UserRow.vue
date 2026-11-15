<script setup lang="ts">
import { Pencil, Power } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import { getInitials } from '@/composables/useInitials';
import type { ManagedUser } from '../../Types/admin';
import { timeAgo } from '../../Utils/time';
import RoleBadge from './RoleBadge.vue';
import StatusDot from './StatusDot.vue';

/**
 * Una persona: quién es, su rol y su misión, su estado, su último acceso y sus acciones.
 * En el celular las columnas se apilan.
 */
defineProps<{ user: ManagedUser; busy: boolean }>();
defineEmits<{ edit: []; toggle: [] }>();
</script>

<template>
    <li
        class="grid grid-cols-[minmax(0,1fr)_auto] items-center gap-x-4 gap-y-2 px-4 py-3.5 transition-colors hover:bg-accent/40 sm:px-6 md:grid-cols-[minmax(0,2.2fr)_minmax(0,1.4fr)_minmax(0,1fr)_minmax(0,1fr)_auto]"
        :class="{ 'opacity-60': !user.active }"
    >
        <div class="flex min-w-0 items-center gap-3">
            <span
                class="flex size-10 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-sm font-semibold text-white shadow-sm"
                aria-hidden="true"
            >
                {{ getInitials(user.name) }}
            </span>
            <div class="min-w-0">
                <p class="truncate font-medium">{{ user.name }}</p>
                <p class="truncate text-sm text-muted-foreground">
                    {{ user.email }}
                </p>
            </div>
        </div>

        <div class="col-start-1 min-w-0 md:col-start-auto">
            <RoleBadge :role="user.role" />
            <p
                v-if="user.mission_name"
                class="mt-1 truncate text-xs text-muted-foreground"
            >
                {{ user.mission_name
                }}<template v-if="user.crew_role">
                    · {{ user.crew_role }}</template
                >
            </p>
        </div>

        <div
            class="col-start-1 flex items-center gap-2 text-sm md:col-start-auto"
        >
            <StatusDot :state="user.active ? 'online' : 'inactive'" />
            {{ user.active ? 'Activo' : 'Desactivado' }}
        </div>

        <p class="col-start-1 text-sm text-muted-foreground md:col-start-auto">
            <span class="md:hidden">Último acceso: </span>
            {{ timeAgo(user.last_login_at) }}
        </p>

        <div
            class="col-start-2 row-span-4 row-start-1 flex items-center gap-1 self-center md:col-start-auto md:row-span-1 md:row-start-auto"
        >
            <Button
                variant="ghost"
                size="icon-sm"
                :aria-label="`Editar a ${user.name}`"
                title="Editar"
                @click="$emit('edit')"
            >
                <Pencil />
            </Button>
            <Button
                variant="ghost"
                size="icon-sm"
                :disabled="busy"
                :aria-label="`${user.active ? 'Desactivar' : 'Activar'} a ${user.name}`"
                :title="user.active ? 'Desactivar' : 'Activar'"
                :class="
                    user.active
                        ? 'hover:text-destructive'
                        : 'text-[var(--status-green)]'
                "
                @click="$emit('toggle')"
            >
                <Power />
            </Button>
        </div>
    </li>
</template>
