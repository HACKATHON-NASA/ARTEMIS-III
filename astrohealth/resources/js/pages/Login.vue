<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { ApiError } from '@/lib/api';
import { dashboard } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';

/**
 * M0 · Inicio de sesión (/acceso) contra POST /api/v1/auth/login (JWT).
 */
defineOptions({
    layout: {
        title: 'Inicia sesión en AstroHealth',
        description: 'Escribe tu correo y tu contraseña',
    },
});

const auth = useAuthStore();

const form = reactive({ email: '', password: '' });
const errors = ref<{ email?: string; password?: string; general?: string }>({});
const processing = ref(false);

async function submit(): Promise<void> {
    processing.value = true;
    errors.value = {};

    try {
        await auth.login(form.email, form.password);
        router.visit(dashboard());
    } catch (error) {
        form.password = '';

        if (error instanceof ApiError && error.status === 422) {
            errors.value = {
                email: error.fieldError('email'),
                password: error.fieldError('password'),
            };
        } else {
            errors.value = {
                general:
                    error instanceof ApiError
                        ? error.message
                        : 'No se pudo conectar con el servidor.',
            };
        }
    } finally {
        processing.value = false;
    }
}
</script>

<template>
    <Head title="Iniciar sesión" />

    <form class="flex flex-col gap-6" @submit.prevent="submit">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Correo</Label>
                <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    name="email"
                    required
                    v-focus
                    autocomplete="email"
                    placeholder="astro1@astrohealth.test"
                />
                <InputError :message="errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Contraseña</Label>
                <PasswordInput
                    id="password"
                    v-model="form.password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Contraseña"
                />
                <InputError :message="errors.password" />
            </div>

            <InputError :message="errors.general" class="text-center" />

            <Button
                type="submit"
                class="mt-2 w-full"
                :disabled="processing"
                data-test="login-button"
            >
                <Spinner v-if="processing" />
                Entrar
            </Button>
        </div>
    </form>
</template>
