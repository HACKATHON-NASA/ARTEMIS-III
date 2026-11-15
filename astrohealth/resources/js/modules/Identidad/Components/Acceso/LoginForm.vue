<script setup lang="ts">
import { ArrowRight, LockKeyhole, UserRound } from '@lucide/vue';
import { reactive, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Spinner } from '@/components/ui/spinner';
import { ApiError } from '@/lib/api';
import { useAuthStore } from '@/stores/useAuthStore';
import IconInput from './IconInput.vue';

/**
 * Correo + contraseña contra POST /api/v1/auth/login (vía useAuthStore).
 * Avisa al principal con `success` (para animar la salida) o `failure`.
 */
const emit = defineEmits<{ success: []; failure: [] }>();

const auth = useAuthStore();
const { t } = useI18n();

const form = reactive({ email: '', password: '' });
const errors = ref<{ email?: string; password?: string; general?: string }>({});
const processing = ref(false);
const showResetHint = ref(false);

async function submit(): Promise<void> {
    if (processing.value) {
        return;
    }

    processing.value = true;
    errors.value = {};

    try {
        await auth.login(form.email.trim(), form.password);
        emit('success');
    } catch (error) {
        form.password = '';
        errors.value = toErrors(error);
        emit('failure');
    } finally {
        processing.value = false;
    }
}

function toErrors(error: unknown): typeof errors.value {
    if (!(error instanceof ApiError)) {
        return { general: t('common.noConnection') };
    }

    if (error.status === 422) {
        return {
            email: error.fieldError('email'),
            password: error.fieldError('password'),
        };
    }

    return { general: error.message };
}
</script>

<template>
    <form class="flex flex-col gap-5" novalidate @submit.prevent="submit">
        <IconInput
            id="email"
            v-model="form.email"
            :label="t('Identidad.login.email')"
            type="email"
            autocomplete="email"
            :icon="UserRound"
            :error="errors.email"
            autofocus
        />

        <IconInput
            id="password"
            v-model="form.password"
            :label="t('Identidad.login.password')"
            type="password"
            autocomplete="current-password"
            :icon="LockKeyhole"
            :error="errors.password"
            revealable
        />

        <p
            v-if="errors.general"
            role="alert"
            class="rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-center text-sm text-rose-200"
        >
            {{ errors.general }}
        </p>

        <div data-intro="field" class="mt-2">
            <button
                type="submit"
                :disabled="processing"
                data-test="login-button"
                class="group relative flex h-14 w-full items-center justify-center gap-2 overflow-hidden rounded-2xl bg-linear-to-r from-sky-400 via-sky-500 to-blue-600 text-lg font-semibold text-white shadow-[0_14px_34px_-10px_rgb(56_152_255/0.8)] transition-all duration-300 hover:shadow-[0_18px_44px_-8px_rgb(56_152_255/0.95)] focus-visible:ring-4 focus-visible:ring-sky-400/40 focus-visible:outline-none active:scale-[0.985] disabled:cursor-wait disabled:opacity-80"
            >
                <span
                    class="pointer-events-none absolute inset-y-0 -left-1/3 w-1/4 -skew-x-12 bg-white/35 blur-md transition-transform duration-[1100ms] ease-out group-hover:translate-x-[560%]"
                />
                <Spinner v-if="processing" class="size-5" />
                <span>{{
                    processing
                        ? t('Identidad.login.entering')
                        : t('Identidad.login.enter')
                }}</span>
                <ArrowRight
                    v-if="!processing"
                    class="size-5 transition-transform duration-300 group-hover:translate-x-1"
                />
            </button>
        </div>

        <div data-intro="field" class="text-center">
            <button
                type="button"
                class="text-sm text-sky-400 transition hover:text-sky-300 focus-visible:underline focus-visible:outline-none"
                @click="showResetHint = !showResetHint"
            >
                {{ t('Identidad.login.forgot') }}
            </button>
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="-translate-y-1 opacity-0"
                leave-active-class="transition duration-200 ease-in"
                leave-to-class="-translate-y-1 opacity-0"
            >
                <p v-if="showResetHint" class="mt-2 text-sm text-slate-300">
                    {{ t('Identidad.login.forgotHint') }}
                </p>
            </Transition>
        </div>
    </form>
</template>
