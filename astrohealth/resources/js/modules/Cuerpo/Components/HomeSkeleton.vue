<script setup lang="ts">
import { Skeleton } from '@/components/ui/skeleton';

/** Lo que se ve en el Inicio mientras carga. */
</script>

<template>
    <Skeleton class="h-44 rounded-2xl" />
    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <Skeleton v-for="n in 4" :key="n" class="h-36 rounded-2xl" />
    </div>
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.25fr)_minmax(0,1fr)]">
        <Skeleton class="h-[560px] rounded-2xl" />
        <Skeleton class="h-[560px] rounded-2xl" />
    </div>
</template>
