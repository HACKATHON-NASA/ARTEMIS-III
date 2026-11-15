<script setup lang="ts">
import AstronautHelmetIcon from './AstronautHelmetIcon.vue';

/**
 * El bloque de acceso: casco, saludo y el formulario, directo sobre el
 * fondo (sin tarjeta ni recuadro).
 */
</script>

<template>
    <section
        class="w-full max-w-[460px] justify-self-center lg:col-start-2 lg:justify-self-end"
        data-intro="panel"
    >
        <div class="flex flex-col items-center text-center">
            <AstronautHelmetIcon />
            <h2
                class="mt-4 text-3xl font-bold tracking-tight text-white drop-shadow-[0_2px_12px_rgb(0_0_0/0.6)] sm:text-4xl"
            >
                Bienvenido/a
            </h2>
            <p class="mt-2 text-base text-slate-200">
                Inicia sesión para acceder al sistema
            </p>
        </div>

        <div class="mt-9">
            <slot />
        </div>
    </section>
</template>
