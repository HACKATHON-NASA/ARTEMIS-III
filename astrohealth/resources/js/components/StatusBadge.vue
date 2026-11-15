<script setup lang="ts">
import { computed } from 'vue';
import { statusMeta } from '@/lib/status';
import type { MaybeStatus } from '@/types/health';

/**
 * El chip del semáforo que usan todos los módulos (norma: va en components/).
 */
const props = withDefaults(
    defineProps<{ status: MaybeStatus; size?: 'sm' | 'md' }>(),
    { size: 'sm' },
);

const meta = computed(() => statusMeta(props.status));
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-full border font-medium whitespace-nowrap"
        :class="[
            meta.soft,
            size === 'sm' ? 'px-2 py-0.5 text-xs' : 'px-3 py-1 text-sm',
        ]"
    >
        <span
            class="size-1.5 rounded-full"
            :style="{ backgroundColor: meta.color }"
        />
        {{ meta.label }}
    </span>
</template>
