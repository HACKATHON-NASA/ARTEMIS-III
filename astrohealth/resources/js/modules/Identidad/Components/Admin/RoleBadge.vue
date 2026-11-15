<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { CircleAlert, Globe, Rocket } from '@lucide/vue';
import { computed } from 'vue';
import type { PersonRole } from '../../Types/admin';
import { roleOption } from '../../Utils/roles';

const { t } = useI18n();

/**
 * El rol de una persona, con dónde está: 🌍 Tierra o 🚀 nave.
 * Sin rol: no puede entrar a la API (se marca para que el admin lo asigne).
 */
const props = defineProps<{ role: PersonRole | null }>();

const option = computed(() => roleOption(props.role));
</script>

<template>
    <span
        v-if="option"
        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium whitespace-nowrap"
        :class="
            option.location === 'earth'
                ? 'border-sky-500/25 bg-sky-500/10 text-sky-700 dark:text-sky-300'
                : 'border-indigo-500/25 bg-indigo-500/10 text-indigo-700 dark:text-indigo-300'
        "
    >
        <Globe v-if="option.location === 'earth'" class="size-3.5" />
        <Rocket v-else class="size-3.5" />
        {{ t(`roles.${option.value}`) }}
    </span>
    <span
        v-else
        class="inline-flex items-center gap-1.5 rounded-full border border-amber-500/30 bg-amber-500/10 px-2.5 py-0.5 text-xs font-medium whitespace-nowrap text-amber-700 dark:text-amber-300"
    >
        <CircleAlert class="size-3.5" />
        {{ t('Identidad.admin.noRole') }}
    </span>
</template>
