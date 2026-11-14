<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Button } from '@/components/ui/button';
import { acceso, dashboard } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';

/**
 * Portada. Sin sesión: botón para entrar (/acceso). Con sesión en memoria: al panel.
 * (El diseño final lo hace el equipo de frontend.)
 */
const auth = useAuthStore();
</script>

<template>
    <Head title="Bienvenida" />

    <div
        class="flex min-h-svh flex-col items-center justify-center gap-8 bg-background p-6 text-center"
    >
        <AppLogoIcon class="size-14 fill-current text-foreground" />

        <div class="max-w-xl space-y-3">
            <h1 class="text-3xl font-semibold tracking-tight">AstroHealth</h1>
            <p class="text-muted-foreground">
                Monitorización de la salud de la tripulación en misiones
                espaciales.
            </p>
        </div>

        <Button as-child size="lg">
            <Link :href="auth.isAuthenticated ? dashboard() : acceso()">
                {{ auth.isAuthenticated ? 'Ir al panel' : 'Iniciar sesión' }}
            </Link>
        </Button>

        <p class="max-w-md text-xs text-muted-foreground">
            Herramienta de apoyo para la demo. No reemplaza el criterio médico.
        </p>
    </div>
</template>
