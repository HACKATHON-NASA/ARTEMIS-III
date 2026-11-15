<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { currentLocale } from '@/lib/i18n';
import { CircleCheck, Flag } from '@lucide/vue';
import { Button } from '@/components/ui/button';
import type { ClinicalNote } from '../../Types/notes';
import { NOTE_KINDS } from '../../Utils/noteKinds';

const { t } = useI18n();

/**
 * Una nota clínica: tipo, texto, quién y cuándo. Si requiere seguimiento,
 * se ve pendiente (en ámbar) hasta que se marca resuelta.
 */
defineProps<{ note: ClinicalNote; busy: boolean; canResolve: boolean }>();

defineEmits<{ resolve: [] }>();

function when(iso: string): string {
    return new Date(iso).toLocaleString(currentLocale(), {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <li
        class="rounded-xl border p-3"
        :class="
            note.follow_up && !note.resolved_at
                ? 'border-[var(--status-yellow)]/50 bg-[var(--status-yellow)]/5'
                : ''
        "
    >
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span
                class="rounded-full bg-sky-500/12 px-2 py-0.5 font-semibold text-sky-700 dark:text-sky-300"
            >
                {{ NOTE_KINDS[note.kind].label }}
            </span>
            <span class="text-muted-foreground"
                >{{ note.author }} · {{ when(note.created_at) }}</span
            >
            <span
                v-if="note.follow_up && !note.resolved_at"
                class="ml-auto inline-flex items-center gap-1 font-medium text-amber-600 dark:text-amber-300"
            >
                <Flag class="size-3.5" /> {{ t('Cuerpo.notes.pending') }}
            </span>
        </div>
        <p class="mt-2 text-sm whitespace-pre-line">{{ note.text }}</p>
        <p
            v-if="note.resolved_at"
            class="mt-2 flex items-center gap-1.5 text-xs text-muted-foreground"
        >
            <CircleCheck class="size-3.5 text-[var(--status-green)]" />
            {{
                t('Cuerpo.notes.resolvedBy', {
                    name: note.resolved_by,
                    when: when(note.resolved_at),
                })
            }}
        </p>
        <Button
            v-else-if="note.follow_up && canResolve"
            variant="outline"
            size="sm"
            class="mt-2"
            :disabled="busy"
            @click="$emit('resolve')"
        >
            <CircleCheck /> {{ t('Cuerpo.notes.resolve') }}
        </Button>
    </li>
</template>
