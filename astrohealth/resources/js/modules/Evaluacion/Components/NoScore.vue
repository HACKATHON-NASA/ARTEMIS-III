<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { registrar } from '@/routes';

/** Cuando el astronauta aún no tiene puntaje (M2): invita a registrar. */
</script>

<template>
    <div class="py-6 text-center">
        <p class="font-semibold">Aún no tienes puntaje</p>
        <p class="mt-1 text-sm text-muted-foreground">
            Registra tus primeras mediciones y aquí verás tu estado general.
        </p>
        <Link
            :href="registrar()"
            class="mt-4 inline-flex rounded-lg bg-linear-to-r from-sky-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:brightness-110"
        >
            Registrar datos
        </Link>
    </div>
</template>
