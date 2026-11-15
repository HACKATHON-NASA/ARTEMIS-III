<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { reminderTitle } from '../Utils/reminderTypes';
import { Check, Pencil } from '@lucide/vue';
import { computed } from 'vue';
import type { Reminder } from '../Types/reminders';
import { reminderIcon } from '../Utils/reminderTypes';

const { t } = useI18n();

/**
 * Un recordatorio: su ícono, su hora, editarlo y la casilla para marcarlo
 * hecho.
 */
const props = defineProps<{ reminder: Reminder; busy: boolean }>();
defineEmits<{ done: []; edit: [] }>();

const isDone = computed(() => props.reminder.state === 'done');
</script>

<template>
    <li
        class="flex items-center gap-3 rounded-xl border px-3 py-2.5 transition-colors"
        :class="{
            'border-sky-500/40 bg-sky-500/5': reminder.state === 'due',
            'bg-muted/30': isDone,
        }"
    >
        <span
            class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-linear-to-br from-sky-400/20 to-blue-600/10 text-sky-600 dark:text-sky-300"
        >
            <component :is="reminderIcon(reminder.type)" class="size-4" />
        </span>

        <div class="min-w-0 flex-1">
            <p
                class="truncate text-sm font-medium transition-colors"
                :class="{ 'text-muted-foreground line-through': isDone }"
            >
                {{ reminderTitle(reminder) }}
            </p>
            <p class="text-xs text-muted-foreground">
                {{ reminder.time }}
                <span
                    v-if="reminder.state === 'due'"
                    class="ml-1 font-medium text-sky-600 dark:text-sky-400"
                    >{{ t('Recordatorios.dueNow') }}</span
                >
            </p>
        </div>

        <button
            type="button"
            class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted-foreground transition hover:bg-accent hover:text-foreground focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none"
            :aria-label="
                t('Recordatorios.editWho', { title: reminderTitle(reminder) })
            "
            :title="t('common.edit')"
            @click="$emit('edit')"
        >
            <Pencil class="size-3.5" />
        </button>

        <button
            type="button"
            class="check flex size-8 shrink-0 items-center justify-center rounded-full border-2 transition focus-visible:ring-2 focus-visible:ring-ring/50 focus-visible:outline-none disabled:cursor-not-allowed"
            :class="
                isDone
                    ? 'border-[var(--status-green)] bg-[var(--status-green)] text-white'
                    : 'border-muted-foreground/40 hover:border-sky-500'
            "
            :disabled="isDone || busy"
            :aria-label="
                isDone
                    ? t('Recordatorios.doneWho', {
                          title: reminderTitle(reminder),
                      })
                    : t('Recordatorios.markDone', {
                          title: reminderTitle(reminder),
                      })
            "
            @click="$emit('done')"
        >
            <Transition enter-active-class="check-pop">
                <Check v-if="isDone" class="size-4" />
            </Transition>
        </button>
    </li>
</template>

<style scoped>
.check-pop {
    animation: pop 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes pop {
    from {
        transform: scale(0);
    }
}

@media (prefers-reduced-motion: reduce) {
    .check-pop {
        animation: none;
    }
}
</style>
