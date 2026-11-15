<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Link } from '@inertiajs/vue3';
import { registrar } from '@/routes';

const { t } = useI18n();

/** Cuando el astronauta aún no tiene puntaje (M2): invita a registrar. */
</script>

<template>
    <div class="py-6 text-center">
        <p class="font-semibold">{{ t('Evaluacion.score.noScore') }}</p>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ t('Evaluacion.score.noScoreHint') }}
        </p>
        <Link
            :href="registrar()"
            class="mt-4 inline-flex rounded-lg bg-linear-to-r from-sky-500 to-blue-600 px-4 py-2 text-sm font-medium text-white hover:brightness-110"
        >
            {{ t('Evaluacion.score.record') }}
        </Link>
    </div>
</template>
