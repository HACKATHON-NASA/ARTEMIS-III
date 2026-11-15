<script setup lang="ts">
import { Brain, CircleCheck, Pencil, Play, Zap } from '@lucide/vue';
import StatusBadge from '@/components/StatusBadge.vue';
import { Button } from '@/components/ui/button';
import { statusMeta } from '@/lib/status';
import type { PsychResult, PsychTest } from '../../Types/psych';

/**
 * Un test del catálogo: qué mide, cuántas preguntas, cada cuánto y cómo se
 * interpreta. El astronauta lo responde cuando le toca; el equipo médico lo
 * edita.
 */
defineProps<{
    test: PsychTest;
    /** Solo para el astronauta: ¿le toca? y su último resultado. */
    due: boolean | null;
    last: PsychResult | null;
    canEdit: boolean;
}>();

defineEmits<{ take: []; edit: [] }>();
</script>

<template>
    <article
        class="flex flex-col gap-4 rounded-2xl border bg-card p-5"
        :class="{ 'border-sky-500/40': due }"
    >
        <header class="flex items-start gap-3">
            <span
                class="flex size-10 shrink-0 items-center justify-center rounded-xl"
                :class="
                    test.kind === 'cognitive'
                        ? 'bg-indigo-500/12 text-indigo-600 dark:text-indigo-300'
                        : 'bg-sky-500/12 text-sky-600 dark:text-sky-300'
                "
            >
                <component
                    :is="test.kind === 'cognitive' ? Zap : Brain"
                    class="size-5"
                />
            </span>
            <div class="min-w-0 flex-1">
                <h2 class="font-semibold">{{ test.name }}</h2>
                <p class="text-xs text-muted-foreground">
                    {{
                        test.kind === 'cognitive' ? 'Cognitivo' : 'Psicológico'
                    }}
                    ·
                    {{
                        test.kind === 'cognitive'
                            ? '8 señales'
                            : `${test.questions.length} preguntas`
                    }}
                    · cada {{ test.frequency_days }} días
                </p>
            </div>
            <Button
                v-if="canEdit"
                variant="outline"
                size="sm"
                :aria-label="`Editar ${test.name}`"
                @click="$emit('edit')"
            >
                <Pencil /> Editar
            </Button>
        </header>

        <p class="text-sm text-muted-foreground">{{ test.description }}</p>

        <ul class="flex flex-wrap gap-1.5" aria-label="Cómo se interpreta">
            <li
                v-for="(band, index) in test.bands"
                :key="band.label"
                class="flex items-center gap-1.5 rounded-full border px-2 py-0.5 text-xs"
            >
                <span
                    class="size-1.5 rounded-full"
                    :style="{ background: statusMeta(band.status).color }"
                />
                {{ band.label }}
                <span class="text-muted-foreground tabular-nums">
                    {{ index === 0 ? 0 : test.bands[index - 1].max + 1
                    }}{{ band.max < 9999 ? `–${band.max}` : '+' }}
                </span>
            </li>
        </ul>

        <footer
            v-if="due !== null"
            class="mt-auto flex items-center justify-between gap-3 border-t pt-4"
        >
            <p v-if="last" class="flex items-center gap-2 text-sm">
                <CircleCheck class="size-4 text-muted-foreground" />
                Día {{ last.day }}:
                <strong class="tabular-nums">{{ last.score }}</strong>
                <StatusBadge :status="last.status" />
            </p>
            <p v-else class="text-sm text-muted-foreground">
                Aún no lo has hecho.
            </p>
            <Button
                v-if="due"
                class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                @click="$emit('take')"
            >
                <Play /> Responder
            </Button>
            <span v-else class="text-xs text-muted-foreground">Al día</span>
        </footer>

        <p v-if="test.source" class="text-[11px] text-muted-foreground/80">
            {{ test.source }}
        </p>
    </article>
</template>
