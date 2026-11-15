<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import LanguageSwitch from '@/components/LanguageSwitch.vue';
import { dashboard, inicio, salud } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import BrandHeader from './Components/Acceso/BrandHeader.vue';
import HeartbeatLine from './Components/Acceso/HeartbeatLine.vue';
import LoginForm from './Components/Acceso/LoginForm.vue';
import LoginPanel from './Components/Acceso/LoginPanel.vue';
import QuickAccessPicker from './Components/Acceso/QuickAccessPicker.vue';
import SpaceBackground from './Components/Acceso/SpaceBackground.vue';
import { useLoginMotion } from './Composables/useLoginMotion';

/**
 * M0 · Pantalla de acceso (/acceso). Es lo primero que se ve al abrir la app.
 * El principal solo compone: fondo, marca, electrocardiograma y el acceso.
 *
 * Por defecto se entra eligiendo a una persona (acceso rápido, sin
 * contraseña: lo pide el reto). El correo y la contraseña siguen como
 * opción, y salen solos si el backend apaga el acceso rápido.
 */
const root = ref<HTMLElement | null>(null);
const auth = useAuthStore();
const motion = useLoginMotion(root);
const { t } = useI18n();
const mode = ref<'quick' | 'password'>('quick');

/**
 * Cada quien entra a su pantalla: administración al Panel de salud, la
 * tripulación a su Inicio, el médico de vuelo al Panel de salud y el resto
 * al panel general.
 */
async function enter(): Promise<void> {
    await motion.exit();

    if (auth.can('users.manage')) {
        router.visit(salud());
    } else if (auth.actor?.astronaut_id) {
        router.visit(inicio());
    } else if (auth.can('health.read.any')) {
        router.visit(salud());
    } else {
        router.visit(dashboard());
    }
}

onMounted(async () => {
    motion.intro();

    // Si la cookie del refresh sigue viva, ya hay sesión: directo al panel.
    if (await auth.ensureSession()) {
        await enter();
    }
});
</script>

<template>
    <Head :title="t('Identidad.login.pageTitle')" />

    <!-- A todo el ancho y el alto de la pantalla (dvh: respeta la barra del navegador en el celular). -->
    <main
        ref="root"
        class="relative min-h-dvh overflow-hidden bg-[#020611] text-white"
    >
        <SpaceBackground />

        <HeartbeatLine
            class="absolute top-6 right-0 hidden w-[34vw] max-w-xl md:block lg:top-10"
        />

        <div
            class="relative z-10 grid min-h-dvh w-full grid-cols-1 content-between gap-10 px-5 py-8 sm:px-10 lg:grid-cols-[1fr_minmax(0,460px)] lg:content-center lg:gap-16 lg:px-[8vw] lg:py-[clamp(3.5rem,9vh,6rem)]"
        >
            <BrandHeader
                class="lg:absolute lg:top-[clamp(2rem,6vh,4rem)] lg:left-[6vw]"
            />

            <LoginPanel
                :subtitle="
                    mode === 'quick'
                        ? t('Identidad.login.chooseUser')
                        : t('Identidad.login.signInPrompt')
                "
            >
                <QuickAccessPicker
                    v-if="mode === 'quick'"
                    @success="enter"
                    @unavailable="mode = 'password'"
                />
                <LoginForm v-else @success="enter" />

                <div class="mt-5 text-center">
                    <button
                        type="button"
                        class="text-sm text-sky-400 transition hover:text-sky-300 focus-visible:underline focus-visible:outline-none"
                        @click="mode = mode === 'quick' ? 'password' : 'quick'"
                    >
                        {{
                            mode === 'quick'
                                ? t('Identidad.login.useEmail')
                                : t('Identidad.login.useQuick')
                        }}
                    </button>
                </div>
            </LoginPanel>
        </div>

        <LanguageSwitch dark class="absolute top-4 right-4 z-20" />

        <p
            class="absolute inset-x-0 bottom-3 z-10 px-4 text-center text-xs text-slate-400/80"
        >
            {{ t('common.disclaimer') }}
        </p>
    </main>
</template>
