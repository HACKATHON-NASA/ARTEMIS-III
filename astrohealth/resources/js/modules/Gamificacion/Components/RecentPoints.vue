<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import type { GamificationProfile, PointEntry } from '../Types/gamification';
import { reasonLabel } from '../Utils/rules';

const { t } = useI18n();

/**
 * Lo último que sumó puntos y, debajo, cómo se ganan (para que sea claro
 * qué hábito premia cada cosa).
 */
defineProps<{
    recent: PointEntry[];
    today: number;
    /** Cómo se ganan (lo manda el backend con el perfil). */
    earning: GamificationProfile['earning'];
}>();
</script>

<template>
    <div class="flex flex-col gap-5">
        <ul class="flex flex-col gap-1.5">
            <li
                v-for="item in recent"
                :key="`${item.reason}-${item.reference}`"
                class="flex items-center justify-between gap-3 text-sm"
            >
                <span class="min-w-0 truncate">
                    {{ reasonLabel(item.reason) }}
                    <span class="text-xs text-muted-foreground">
                        ·
                        {{
                            item.day === today
                                ? t('common.today')
                                : t('common.dayShort', { n: item.day })
                        }}
                    </span>
                </span>
                <span
                    class="font-semibold text-sky-600 tabular-nums dark:text-sky-400"
                    >+{{ item.points }}</span
                >
            </li>
        </ul>
        <div class="rounded-xl bg-muted/50 p-3">
            <p
                class="mb-2 text-xs font-semibold text-muted-foreground uppercase"
            >
                {{ t('Gamificacion.how.title') }}
            </p>
            <ul class="flex flex-col gap-1 text-sm">
                <li
                    v-for="item in earning"
                    :key="item.reason"
                    class="flex justify-between gap-3"
                >
                    <span>{{ t(`Gamificacion.how.${item.reason}`) }}</span>
                    <span class="font-medium tabular-nums"
                        >+{{ item.points }}</span
                    >
                </li>
            </ul>
        </div>
    </div>
</template>
