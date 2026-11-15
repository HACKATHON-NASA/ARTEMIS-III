<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { ref } from 'vue';
import type { EnvironmentCode } from '../Types/mission';
import { ENVIRONMENTS } from '../Utils/environments';

const { t } = useI18n();

/**
 * Elegir el entorno de la misión: 4 tarjetas con su gravedad, radiación y
 * retraso con Tierra. Es un grupo de radio (flechas para moverse).
 */
const model = defineModel<EnvironmentCode | null>({ required: true });
defineProps<{ labelledby: string }>();

const cards = ref<HTMLButtonElement[]>([]);

function move(index: number, step: number): void {
    const next = (index + step + ENVIRONMENTS.length) % ENVIRONMENTS.length;
    model.value = ENVIRONMENTS[next].code;
    cards.value[next]?.focus();
}
</script>

<template>
    <div
        role="radiogroup"
        :aria-labelledby="labelledby"
        class="grid grid-cols-2 gap-2"
    >
        <button
            v-for="(env, index) in ENVIRONMENTS"
            :key="env.code"
            ref="cards"
            type="button"
            role="radio"
            :aria-checked="model === env.code"
            :tabindex="model === env.code || (!model && index === 0) ? 0 : -1"
            class="relative overflow-hidden rounded-xl border bg-linear-to-br to-transparent p-3 text-left transition hover:border-sky-500/50 focus-visible:ring-[3px] focus-visible:ring-sky-500/25 focus-visible:outline-none"
            :class="[
                env.tint,
                model === env.code
                    ? 'border-sky-500 ring-1 ring-sky-500'
                    : 'opacity-80 hover:opacity-100',
            ]"
            @click="model = env.code"
            @keydown.right.prevent="move(index, 1)"
            @keydown.down.prevent="move(index, 1)"
            @keydown.left.prevent="move(index, -1)"
            @keydown.up.prevent="move(index, -1)"
        >
            <component :is="env.icon" class="size-5 text-foreground/80" />
            <p class="mt-2 text-sm font-semibold">{{ env.name }}</p>
            <p class="text-[11px] leading-snug text-muted-foreground">
                {{ env.gravity }} ·
                {{ t('Entorno.delay', { delay: env.delay }) }}
            </p>
        </button>
    </div>
</template>
