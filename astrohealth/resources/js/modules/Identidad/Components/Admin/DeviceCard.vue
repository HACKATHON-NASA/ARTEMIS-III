<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { Cpu, Power, RadioTower } from '@lucide/vue';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import type { ManagedDevice } from '../../Types/admin';
import { isOnline, timeAgo } from '../../Utils/time';
import CopyButton from './CopyButton.vue';
import StatusDot from './StatusDot.vue';

const { t } = useI18n();

/**
 * Una máquina que envía lecturas: su estado, a quién mide y su client_id.
 */
const props = defineProps<{ device: ManagedDevice; busy: boolean }>();
defineEmits<{ toggle: [] }>();

const state = computed(() => {
    if (!props.device.active) {
        return {
            dot: 'inactive' as const,
            label: t('Identidad.admin.stateInactive'),
        };
    }

    return isOnline(props.device.last_seen_at)
        ? { dot: 'online' as const, label: t('Identidad.admin.stateOnline') }
        : { dot: 'offline' as const, label: t('Identidad.admin.stateOffline') };
});

const isGateway = computed(() => props.device.astronaut_id === null);
</script>

<template>
    <article
        class="group relative flex flex-col gap-4 rounded-2xl border bg-card p-5 transition duration-300 hover:-translate-y-0.5 hover:border-sky-500/40 hover:shadow-[0_12px_32px_-16px_rgb(14_165_233/0.45)]"
        :class="{ 'opacity-60': !device.active }"
    >
        <div class="flex items-start gap-3">
            <span
                class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-linear-to-br from-sky-400/25 to-blue-600/10 text-sky-600 dark:text-sky-300"
            >
                <RadioTower v-if="isGateway" class="size-5" />
                <Cpu v-else class="size-5" />
            </span>
            <div class="min-w-0 flex-1">
                <h3 class="truncate font-medium">{{ device.name }}</h3>
                <p class="truncate text-sm text-muted-foreground">
                    {{
                        isGateway
                            ? t('Identidad.admin.gateway')
                            : t('Identidad.admin.measures', {
                                  name: device.astronaut_name,
                              })
                    }}
                </p>
            </div>
            <Button
                variant="ghost"
                size="icon-sm"
                :disabled="busy"
                :aria-label="
                    t(
                        device.active
                            ? 'Identidad.admin.deactivateWho'
                            : 'Identidad.admin.activateWho',
                        { name: device.name },
                    )
                "
                :title="
                    device.active
                        ? t('Identidad.admin.deactivate')
                        : t('Identidad.admin.activate')
                "
                :class="
                    device.active
                        ? 'hover:text-destructive'
                        : 'text-[var(--status-green)]'
                "
                @click="$emit('toggle')"
            >
                <Power />
            </Button>
        </div>

        <div
            class="flex items-center gap-2 rounded-lg border bg-muted/40 py-1 pr-1 pl-3"
        >
            <code
                class="flex-1 truncate font-mono text-xs text-muted-foreground"
            >
                {{ device.client_id }}
            </code>
            <CopyButton
                :value="device.client_id"
                :label="t('Identidad.admin.copy', { what: 'client_id' })"
            />
        </div>

        <div class="flex items-center justify-between text-sm">
            <span class="flex items-center gap-2">
                <StatusDot :state="state.dot" />
                {{ state.label }}
            </span>
            <span class="text-muted-foreground">
                {{ timeAgo(device.last_seen_at) }}
            </span>
        </div>
    </article>
</template>
