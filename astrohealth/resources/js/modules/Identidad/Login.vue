<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { dashboard } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import BrandHeader from './Components/BrandHeader.vue';
import HeartbeatLine from './Components/HeartbeatLine.vue';
import LoginCard from './Components/LoginCard.vue';
import LoginForm from './Components/LoginForm.vue';
import SpaceBackground from './Components/SpaceBackground.vue';
import Starfield from './Components/Starfield.vue';
import { useLoginMotion } from './Composables/useLoginMotion';

/**
 * M0 · Pantalla de acceso (/acceso). Es lo primero que se ve al abrir la app.
 * El principal solo compone: fondo, estrellas, marca, pulso y la tarjeta.
 */
const root = ref<HTMLElement | null>(null);
const auth = useAuthStore();
const motion = useLoginMotion(root);

async function enter(): Promise<void> {
    await motion.exit();
    router.visit(dashboard());
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
    <Head title="Iniciar sesión" />

    <!-- A todo el ancho y el alto de la pantalla (dvh: respeta la barra del navegador en el celular). -->
    <main
        ref="root"
        class="relative min-h-dvh overflow-hidden bg-[#020611] text-white"
    >
        <SpaceBackground />
        <Starfield />

        <HeartbeatLine
            class="absolute top-6 right-0 hidden w-[34vw] max-w-xl md:block lg:top-10"
        />

        <div
            class="relative z-10 grid min-h-dvh w-full grid-cols-1 content-between gap-10 px-5 py-8 sm:px-10 lg:grid-cols-[1fr_minmax(0,540px)] lg:content-center lg:gap-16 lg:px-[6vw] lg:py-[clamp(3.5rem,9vh,6rem)]"
        >
            <BrandHeader
                class="lg:absolute lg:top-[clamp(2rem,6vh,4rem)] lg:left-[6vw]"
            />

            <LoginCard>
                <LoginForm @success="enter" @failure="motion.shake()" />
            </LoginCard>
        </div>

        <p
            class="absolute inset-x-0 bottom-3 z-10 px-4 text-center text-xs text-slate-400/80"
        >
            Herramienta de apoyo. No reemplaza el criterio médico.
        </p>
    </main>
</template>
