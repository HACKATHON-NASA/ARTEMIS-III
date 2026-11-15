<script setup lang="ts">
import { useI18n } from 'vue-i18n';
import { KeyRound, TriangleAlert } from '@lucide/vue';
import { ref, watch } from 'vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { ApiError } from '@/lib/api';
import type {
    CreatedDevice,
    CrewOption,
    DeviceFormData,
} from '../../Types/admin';
import CopyButton from './CopyButton.vue';

const { t } = useI18n();

/**
 * Registrar una máquina en dos pasos: los datos y, después, sus credenciales.
 * El secreto se muestra UNA sola vez (el backend solo guarda su hash).
 */
const props = defineProps<{
    crew: CrewOption[];
    submit: (data: DeviceFormData) => Promise<CreatedDevice>;
}>();

const open = defineModel<boolean>('open', { required: true });

const GATEWAY = 'mission';
const name = ref('');
const target = ref(GATEWAY);
const error = ref<string>();
const saving = ref(false);
const created = ref<CreatedDevice | null>(null);

watch(open, (isOpen) => {
    if (isOpen) {
        name.value = '';
        target.value = GATEWAY;
        error.value = undefined;
        created.value = null;
    }
});

async function save(): Promise<void> {
    if (name.value.trim().length < 3) {
        error.value = t('Identidad.admin.errDeviceName');

        return;
    }

    saving.value = true;

    try {
        created.value = await props.submit({
            name: name.value,
            astronaut_id:
                target.value === GATEWAY ? null : Number(target.value),
        });
    } catch (failure) {
        if (!(failure instanceof ApiError)) {
            throw failure;
        }

        error.value =
            failure.fieldError('name') ??
            failure.fieldError('astronaut_id') ??
            failure.message;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-md">
            <template v-if="created === null">
                <DialogHeader>
                    <DialogTitle>{{
                        t('Identidad.admin.registerDevice')
                    }}</DialogTitle>
                    <DialogDescription>
                        {{ t('Identidad.admin.deviceHint') }}
                    </DialogDescription>
                </DialogHeader>

                <form
                    id="device-form"
                    class="grid gap-4"
                    novalidate
                    @submit.prevent="save"
                >
                    <div class="grid gap-2">
                        <Label for="device-name">{{
                            t('Identidad.admin.name')
                        }}</Label>
                        <Input
                            id="device-name"
                            v-model="name"
                            :placeholder="
                                t('Identidad.admin.devicePlaceholder')
                            "
                            autocomplete="off"
                        />
                        <InputError :message="error" />
                    </div>
                    <div class="grid gap-2">
                        <Label for="device-target">{{
                            t('Identidad.admin.whoMeasures')
                        }}</Label>
                        <Select v-model="target">
                            <SelectTrigger id="device-target" class="w-full">
                                <SelectValue />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem :value="GATEWAY">
                                    {{ t('Identidad.admin.wholeMission') }}
                                </SelectItem>
                                <SelectItem
                                    v-for="member in crew"
                                    :key="member.id"
                                    :value="member.id.toString()"
                                >
                                    {{ member.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                </form>

                <DialogFooter>
                    <Button variant="ghost" @click="open = false">{{
                        t('common.cancel')
                    }}</Button>
                    <Button
                        type="submit"
                        form="device-form"
                        :disabled="saving"
                        class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                    >
                        <Spinner v-if="saving" />
                        {{ t('Identidad.admin.register') }}
                    </Button>
                </DialogFooter>
            </template>

            <template v-else>
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <KeyRound class="size-5 text-sky-500" />
                        {{
                            t('Identidad.admin.credentialsOf', {
                                name: created.device.name,
                            })
                        }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ t('Identidad.admin.credentialsHint') }}
                    </DialogDescription>
                </DialogHeader>

                <dl class="grid gap-3">
                    <div
                        v-for="item in [
                            {
                                label: 'client_id',
                                value: created.device.client_id,
                            },
                            {
                                label: 'client_secret',
                                value: created.client_secret,
                            },
                        ]"
                        :key="item.label"
                        class="grid gap-1"
                    >
                        <dt class="text-xs font-medium text-muted-foreground">
                            {{ item.label }}
                        </dt>
                        <dd
                            class="flex items-center gap-2 rounded-lg border bg-muted/40 py-1 pr-1 pl-3"
                        >
                            <code class="flex-1 font-mono text-xs break-all">{{
                                item.value
                            }}</code>
                            <CopyButton
                                :value="item.value"
                                :label="
                                    t('Identidad.admin.copy', {
                                        what: item.label,
                                    })
                                "
                            />
                        </dd>
                    </div>
                </dl>

                <p
                    class="flex gap-2 rounded-lg border border-amber-500/30 bg-amber-500/10 p-3 text-sm text-amber-800 dark:text-amber-200"
                >
                    <TriangleAlert class="mt-0.5 size-4 shrink-0" />
                    {{ t('Identidad.admin.saveSecret') }}
                </p>

                <DialogFooter>
                    <Button @click="open = false">{{
                        t('Identidad.admin.savedIt')
                    }}</Button>
                </DialogFooter>
            </template>
        </DialogContent>
    </Dialog>
</template>
