<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import { acceso } from '@/routes';
import { useAuthStore } from '@/stores/useAuthStore';
import type { BreadcrumbItem } from '@/types';

const { breadcrumbs = [] } = defineProps<{
    breadcrumbs?: BreadcrumbItem[];
}>();

/*
 * Las páginas con este layout exigen sesión. Si no hay token en memoria,
 * se intenta recuperar con la cookie del refresh (recargar la página no
 * cierra la sesión). Si tampoco hay cookie, se va a /acceso.
 */
const auth = useAuthStore();
const ready = ref(auth.isAuthenticated);

onMounted(async () => {
    if (await auth.ensureSession()) {
        ready.value = true;

        return;
    }

    router.visit(acceso());
});
</script>

<template>
    <AppLayout v-if="ready" :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
    <div v-else class="flex min-h-svh items-center justify-center">
        <Spinner class="size-6 text-muted-foreground" />
    </div>
</template>
