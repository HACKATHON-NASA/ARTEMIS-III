<script setup lang="ts">
import { Eye, EyeOff } from '@lucide/vue';
import { computed, ref } from 'vue';
import type { Component } from 'vue';
import { useI18n } from 'vue-i18n';

/**
 * Campo con ícono a la izquierda (como en el prototipo). Si `revealable`,
 * trae el ojo para ver u ocultar la contraseña.
 */
const props = defineProps<{
    id: string;
    label: string;
    icon: Component;
    type?: 'email' | 'password' | 'text';
    autocomplete?: string;
    revealable?: boolean;
    error?: string;
    autofocus?: boolean;
}>();

const model = defineModel<string>({ required: true });
const revealed = ref(false);
const { t } = useI18n();

const inputType = computed(() =>
    props.revealable && revealed.value ? 'text' : (props.type ?? 'text'),
);
</script>

<template>
    <div data-intro="field">
        <label :for="id" class="sr-only">{{ label }}</label>
        <div class="group relative">
            <component
                :is="icon"
                class="pointer-events-none absolute top-1/2 left-5 size-6 -translate-y-1/2 text-slate-300 transition-colors duration-300 group-focus-within:text-sky-300"
                aria-hidden="true"
            />
            <input
                :id="id"
                v-model="model"
                v-focus="autofocus === true"
                :type="inputType"
                :name="id"
                :autocomplete="autocomplete"
                :placeholder="label"
                :aria-invalid="error ? 'true' : undefined"
                :aria-describedby="error ? `${id}-error` : undefined"
                required
                class="h-14 w-full rounded-2xl border border-white/20 bg-white/[0.03] pr-14 pl-14 text-base text-white transition-all duration-300 outline-none placeholder:text-slate-400 hover:border-white/30 focus:border-sky-400/70 focus:bg-white/[0.06] focus:shadow-[0_0_0_4px_rgb(56_152_255/0.15),0_0_30px_-8px_rgb(56_152_255/0.6)] aria-[invalid=true]:border-rose-400/70"
            />
            <button
                v-if="revealable"
                type="button"
                class="absolute top-1/2 right-4 -translate-y-1/2 rounded-lg p-1.5 text-slate-300 transition hover:text-white focus-visible:ring-2 focus-visible:ring-sky-400/60 focus-visible:outline-none"
                :aria-label="
                    revealed
                        ? t('Identidad.login.hidePassword')
                        : t('Identidad.login.showPassword')
                "
                @click="revealed = !revealed"
            >
                <Eye v-if="revealed" class="size-5" />
                <EyeOff v-else class="size-5" />
            </button>
        </div>
        <p
            v-if="error"
            :id="`${id}-error`"
            class="mt-2 pl-1 text-sm text-rose-300"
        >
            {{ error }}
        </p>
    </div>
</template>
