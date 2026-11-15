<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { BRAND } from '@/lib/brand';
</script>

<template>
    <div class="flex aspect-square size-8 items-center justify-center">
        <AppLogoIcon class="size-8" />
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">
            {{ BRAND.first
            }}<span class="text-sky-500">{{ BRAND.accent }}</span>
        </span>
    </div>
</template>
