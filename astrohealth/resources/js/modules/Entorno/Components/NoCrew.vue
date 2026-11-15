<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { UserPlus, UsersRound } from '@lucide/vue';
import { usuarios } from '@/routes';

/**
 * Estado vacío: la misión elegida todavía no tiene tripulantes. Quien
 * administra usuarios ve el atajo para crearlos (asociados a la misión).
 */
defineProps<{ missionName: string; canManage: boolean }>();
</script>

<template>
    <div
        class="flex flex-col items-center gap-3 rounded-2xl border border-dashed p-10 text-center"
    >
        <span
            class="flex size-12 items-center justify-center rounded-2xl bg-sky-500/10 text-sky-600 dark:text-sky-400"
        >
            <UsersRound class="size-6" />
        </span>
        <p class="font-medium">{{ missionName }} aún no tiene tripulantes</p>
        <p class="max-w-sm text-sm text-muted-foreground">
            Los tripulantes se crean en Usuarios, asociados a su misión. Cuando
            registren datos, aquí aparece su salud.
        </p>
        <Link
            v-if="canManage"
            :href="usuarios()"
            class="mt-1 inline-flex items-center gap-2 rounded-lg bg-linear-to-r from-sky-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:brightness-110"
        >
            <UserPlus class="size-4" />
            Crear tripulantes
        </Link>
    </div>
</template>
