<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Check, Stethoscope } from '@lucide/vue';
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { toast } from 'vue-sonner';
import { Button } from '@/components/ui/button';
import { useAdviceStore } from '../Stores/useAdviceStore';
import type { Advice } from '../Types/advice';
import { ADVICE_CATEGORIES } from '../Utils/adviceCategories';

const { t } = useI18n();

/**
 * Las indicaciones del médico que le llegaron al astronauta y aún no ha
 * leído (en su Inicio). Revisa cada 15 s si llegó una nueva (viajan con el
 * retraso de la comunicación). "Entendido" le avisa al médico que la leyó.
 */
const props = defineProps<{ astronautId: number }>();

const store = useAdviceStore();
const busyId = ref<number | null>(null);
let timer: ReturnType<typeof setInterval> | undefined;

async function read(item: Advice): Promise<void> {
    busyId.value = item.id;
    await store.markRead(item.id);
    busyId.value = null;
    toast.success(t('Alertas.inbox.read'), {
        description: t('Alertas.inbox.readHint'),
    });
}

onMounted(() => {
    void store.load(props.astronautId);
    timer = setInterval(() => void store.load(props.astronautId), 15_000);
});

onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
    <section
        v-if="store.unread.length"
        class="flex flex-col gap-3"
        :aria-label="t('Alertas.inbox.aria')"
    >
        <article
            v-for="item in store.unread"
            :key="item.id"
            class="flex flex-col gap-3 rounded-2xl border bg-linear-to-r to-card p-4 sm:flex-row sm:items-start sm:p-5"
            :class="
                item.important
                    ? 'border-[var(--status-red)]/50 from-[var(--status-red)]/10'
                    : 'border-sky-500/40 from-sky-500/10'
            "
        >
            <span
                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-sky-500/15 text-sky-600 dark:text-sky-300"
            >
                <Stethoscope class="size-5" />
            </span>
            <div class="min-w-0 flex-1">
                <p
                    class="text-xs font-semibold tracking-wide text-sky-600 uppercase dark:text-sky-400"
                >
                    {{
                        t('Alertas.inbox.header', {
                            category: ADVICE_CATEGORIES[item.category].label,
                        })
                    }}
                    <template v-if="item.important">
                        {{ t('Alertas.inbox.important') }}</template
                    >
                </p>
                <p class="mt-1 font-semibold">{{ item.title }}</p>
                <ol
                    class="mt-2 list-decimal space-y-0.5 pl-4 text-sm text-muted-foreground"
                >
                    <li v-for="step in item.body.split('\n')" :key="step">
                        {{ step }}
                    </li>
                </ol>
                <p class="mt-2 text-xs text-muted-foreground">
                    {{ item.author
                    }}<template v-if="item.valid_days">
                        {{
                            t(
                                'Alertas.inbox.forDays',
                                { n: item.valid_days },
                                item.valid_days,
                            )
                        }}</template
                    >
                </p>
            </div>
            <Button
                :disabled="busyId === item.id"
                class="shrink-0 bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                @click="read(item)"
            >
                <Check /> {{ t('common.gotIt') }}
            </Button>
        </article>
    </section>
</template>
