<script setup lang="ts">
/**
 * Punto de estado. "online" late como un signo vital; los demás, quietos.
 */
defineProps<{ state: 'online' | 'offline' | 'inactive' }>();
</script>

<template>
    <span class="relative inline-flex size-2.5 shrink-0" aria-hidden="true">
        <span
            v-if="state === 'online'"
            class="absolute inset-0 animate-ping rounded-full bg-[var(--status-green)] opacity-60 motion-reduce:hidden"
        />
        <span
            class="relative inline-flex size-2.5 rounded-full"
            :class="{
                'bg-[var(--status-green)]': state === 'online',
                'bg-[var(--status-yellow)]': state === 'offline',
                'bg-[var(--status-none)]': state === 'inactive',
            }"
        />
    </span>
</template>
