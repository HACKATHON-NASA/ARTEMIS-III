<script setup lang="ts">
import { Languages } from '@lucide/vue';
import { useI18n } from 'vue-i18n';
import { LOCALES, setLocale } from '@/lib/i18n';
import type { Locale } from '@/lib/i18n';

/**
 * Cambia el idioma de toda la app (inglés · español). Se recuerda en este
 * navegador. `dark` para usarlo sobre un fondo oscuro (la pantalla de acceso).
 */
defineProps<{ dark?: boolean }>();

const { locale, t } = useI18n();

function choose(code: Locale): void {
    setLocale(code);
}
</script>

<template>
    <div
        role="group"
        :aria-label="t('common.language')"
        class="inline-flex items-center gap-1 rounded-full border p-0.5 text-xs font-semibold"
        :class="
            dark
                ? 'border-white/15 bg-white/5 text-slate-300'
                : 'border-border bg-background text-muted-foreground'
        "
    >
        <Languages class="ml-1.5 size-3.5 opacity-70" aria-hidden="true" />
        <button
            v-for="item in LOCALES"
            :key="item.code"
            type="button"
            :lang="item.code"
            :title="item.label"
            :aria-pressed="locale === item.code"
            class="rounded-full px-2 py-0.5 transition focus-visible:ring-2 focus-visible:ring-sky-400/60 focus-visible:outline-none"
            :class="
                locale === item.code
                    ? 'bg-sky-500 text-white'
                    : dark
                      ? 'hover:text-white'
                      : 'hover:text-foreground'
            "
            @click="choose(item.code)"
        >
            {{ item.short }}
        </button>
    </div>
</template>
