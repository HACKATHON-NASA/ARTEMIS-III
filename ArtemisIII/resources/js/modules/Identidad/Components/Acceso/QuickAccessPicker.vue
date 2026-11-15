<script setup lang="ts">
import { ArrowRight } from '@lucide/vue';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import { Spinner } from '@/components/ui/spinner';
import { useInitials } from '@/composables/useInitials';
import { api, ApiError } from '@/lib/api';
import { useAuthStore } from '@/stores/useAuthStore';

/**
 * Acceso rápido: se elige a quién y se entra, sin contraseña (lo pide el
 * reto). GET/POST /api/v1/auth/quick-access. Si el backend lo tiene
 * apagado, avisa con `unavailable` y el principal muestra el formulario.
 */
type QuickUser = {
    id: number;
    name: string;
    role: string;
    astronaut_id: number | null;
};

const emit = defineEmits<{ success: []; unavailable: [] }>();

const auth = useAuthStore();
const { t } = useI18n();
const { getInitials } = useInitials();

const users = ref<QuickUser[]>([]);
const loading = ref(true);
const entering = ref<number | null>(null);
const error = ref<string | null>(null);

async function enter(user: QuickUser): Promise<void> {
    if (entering.value !== null) {
        return;
    }

    entering.value = user.id;
    error.value = null;

    try {
        await auth.quickLogin(user.id);
        emit('success');
    } catch (failure) {
        error.value =
            failure instanceof ApiError
                ? failure.message
                : t('common.noConnection');
        entering.value = null;
    }
}

onMounted(async () => {
    try {
        users.value = await api<QuickUser[]>('/auth/quick-access', {
            auth: false,
            retry: false,
        });
    } catch {
        emit('unavailable');
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <div class="flex flex-col gap-3">
        <div v-if="loading" class="flex justify-center py-8">
            <Spinner class="size-6 text-sky-400" />
        </div>

        <button
            v-for="user in users"
            :key="user.id"
            type="button"
            data-intro="field"
            :data-test="`quick-user-${user.id}`"
            :disabled="entering !== null"
            class="group flex w-full items-center gap-4 rounded-2xl border border-white/15 bg-white/[0.04] px-4 py-3 text-left transition-all duration-300 hover:border-sky-400/60 hover:bg-white/[0.08] focus-visible:ring-4 focus-visible:ring-sky-400/40 focus-visible:outline-none disabled:cursor-wait disabled:opacity-70"
            @click="enter(user)"
        >
            <span
                class="flex size-11 shrink-0 items-center justify-center rounded-full bg-linear-to-br from-sky-400 to-blue-600 text-sm font-semibold text-white"
                aria-hidden="true"
            >
                {{ getInitials(user.name) }}
            </span>
            <span class="min-w-0 flex-1">
                <span class="block truncate font-semibold text-white">
                    {{ user.name }}
                </span>
                <span class="block truncate text-sm text-slate-300">
                    {{ t(`roles.${user.role}`) }}
                </span>
            </span>
            <Spinner v-if="entering === user.id" class="size-5 text-sky-300" />
            <ArrowRight
                v-else
                class="size-5 text-sky-300 transition-transform duration-300 group-hover:translate-x-1"
            />
        </button>

        <p
            v-if="error"
            role="alert"
            class="rounded-xl border border-rose-400/30 bg-rose-500/10 px-4 py-3 text-center text-sm text-rose-200"
        >
            {{ error }}
        </p>
    </div>
</template>
