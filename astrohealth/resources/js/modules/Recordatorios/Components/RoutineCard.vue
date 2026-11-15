<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { reminderTitle } from '../Utils/reminderTypes';
import { Bell, BellOff, BellRing, Plus } from '@lucide/vue';
import { computed, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { useNotifications } from '@/composables/useNotifications';
import { useRemindersStore } from '../Stores/useRemindersStore';
import type { Reminder, ReminderDraft } from '../Types/reminders';
import ReminderEditor from './ReminderEditor.vue';
import ReminderItem from './ReminderItem.vue';

const { t } = useI18n();

/**
 * La rutina del día (M8) en el Inicio: el avance, la lista, crear y editar
 * recordatorios y activar los avisos del navegador. Marcar uno como hecho lo
 * avisa a la pantalla (`done`), que suma los puntos (M7).
 */
defineProps<{ busyId: number | null }>();
defineEmits<{ done: [reminder: Reminder] }>();

const store = useRemindersStore();
const { permission, request } = useNotifications();
const editor = ref<{ open: boolean; reminder: Reminder | null }>({
    open: false,
    reminder: null,
});

const percent = computed(() =>
    store.progress.total === 0
        ? 0
        : (store.progress.done / store.progress.total) * 100,
);

async function save(draft: ReminderDraft): Promise<void> {
    if (!(await store.save(draft, editor.value.reminder?.id))) {
        return;
    }

    toast.success(
        editor.value.reminder
            ? t('Recordatorios.updated')
            : t('Recordatorios.created'),
        {
            description: `${reminderTitle(draft)} · ${draft.time}`,
        },
    );
}

async function remove(): Promise<void> {
    if (editor.value.reminder) {
        if (await store.remove(editor.value.reminder.id)) {
            toast(t('Recordatorios.deleted'));
        }
    }
}
</script>

<template>
    <div>
        <div class="mb-3 flex items-center justify-between gap-2">
            <h2 class="text-lg font-semibold">
                {{ t('Recordatorios.routineToday') }}
            </h2>
            <div class="flex items-center gap-1">
                <span class="mr-1 text-sm text-muted-foreground tabular-nums">
                    {{
                        t('Recordatorios.progress', {
                            done: store.progress.done,
                            total: store.progress.total,
                        })
                    }}
                </span>
                <Button
                    v-if="
                        permission !== 'unsupported' && permission !== 'granted'
                    "
                    variant="ghost"
                    size="icon-sm"
                    :disabled="permission === 'denied'"
                    :title="
                        permission === 'denied'
                            ? t('Recordatorios.notificationsBlocked')
                            : t('Recordatorios.enableNotifications')
                    "
                    :aria-label="
                        permission === 'denied'
                            ? t('Recordatorios.notificationsBlockedShort')
                            : t('Recordatorios.enableNotificationsShort')
                    "
                    @click="request"
                >
                    <BellOff v-if="permission === 'denied'" />
                    <Bell v-else />
                </Button>
                <BellRing
                    v-else-if="permission === 'granted'"
                    class="mx-1.5 size-4 text-sky-500"
                    :aria-label="t('Recordatorios.notificationsOn')"
                />
                <Button
                    variant="ghost"
                    size="icon-sm"
                    :aria-label="t('Recordatorios.newTitle')"
                    :title="t('Recordatorios.newTitle')"
                    @click="editor = { open: true, reminder: null }"
                >
                    <Plus />
                </Button>
            </div>
        </div>
        <div class="mb-4 h-1.5 overflow-hidden rounded-full bg-muted">
            <div
                class="h-full rounded-full bg-linear-to-r from-sky-400 to-blue-600 transition-[width] duration-700 ease-out"
                :style="{ width: `${percent}%` }"
            />
        </div>

        <TransitionGroup
            v-if="store.reminders.length"
            tag="ul"
            class="flex flex-col gap-2"
            move-class="transition duration-300"
            enter-active-class="transition duration-300"
            enter-from-class="-translate-y-1 opacity-0"
        >
            <ReminderItem
                v-for="reminder in store.reminders"
                :key="reminder.id"
                :reminder="reminder"
                :busy="busyId === reminder.id"
                @done="$emit('done', reminder)"
                @edit="editor = { open: true, reminder }"
            />
        </TransitionGroup>
        <p
            v-else
            class="rounded-xl border border-dashed p-6 text-center text-sm text-muted-foreground"
        >
            {{ t('Recordatorios.empty') }}
        </p>

        <ReminderEditor
            v-model:open="editor.open"
            :reminder="editor.reminder"
            :submit="save"
            :remove="remove"
        />
    </div>
</template>
