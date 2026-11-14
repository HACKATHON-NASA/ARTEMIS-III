<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
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
import type {
    CrewOption,
    ManagedUser,
    PersonRole,
    UserFormData,
} from '../../Types/admin';
import { ROLE_OPTIONS, roleOption } from '../../Utils/roles';

/**
 * Crear una persona o editar su nombre, rol y astronauta. El correo no se
 * cambia después de crearla. Los roles de la nave exigen un astronauta.
 */
const props = defineProps<{
    user: ManagedUser | null;
    crew: CrewOption[];
    submit: (data: UserFormData) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const form = reactive({ name: '', email: '', role: '', astronaut: '' });
const errors = ref<Partial<Record<keyof typeof form, string>>>({});
const saving = ref(false);

const isEdit = computed(() => props.user !== null);
const needsAstronaut = computed(
    () =>
        roleOption((form.role || null) as PersonRole | null)
            ?.requiresAstronaut ?? false,
);

watch(open, (isOpen) => {
    if (!isOpen) {
        return;
    }

    form.name = props.user?.name ?? '';
    form.email = props.user?.email ?? '';
    form.role = props.user?.role ?? '';
    form.astronaut = props.user?.astronaut_id?.toString() ?? '';
    errors.value = {};
});

function validate(): boolean {
    errors.value = {};

    if (form.name.trim().length < 3) {
        errors.value.name = 'Escribe el nombre completo.';
    }

    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email.trim())) {
        errors.value.email = 'Escribe un correo válido.';
    }

    if (form.role === '') {
        errors.value.role = 'Elige un rol.';
    }

    if (needsAstronaut.value && form.astronaut === '') {
        errors.value.astronaut = 'Este rol va en la nave: elige el astronauta.';
    }

    return Object.keys(errors.value).length === 0;
}

async function save(): Promise<void> {
    if (!validate()) {
        return;
    }

    saving.value = true;

    try {
        await props.submit({
            name: form.name,
            email: form.email,
            role: form.role as PersonRole,
            astronaut_id: needsAstronaut.value ? Number(form.astronaut) : null,
        });
        open.value = false;
    } finally {
        saving.value = false;
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>
                    {{ isEdit ? 'Editar usuario' : 'Nuevo usuario' }}
                </DialogTitle>
                <DialogDescription>
                    {{
                        isEdit
                            ? 'Cambia el nombre, el rol o el astronauta.'
                            : 'La persona entrará con este correo. La contraseña se le asigna aparte.'
                    }}
                </DialogDescription>
            </DialogHeader>

            <form
                id="user-form"
                class="grid gap-4"
                novalidate
                @submit.prevent="save"
            >
                <div class="grid gap-2">
                    <Label for="user-name">Nombre</Label>
                    <Input
                        id="user-name"
                        v-model="form.name"
                        autocomplete="off"
                    />
                    <InputError :message="errors.name" />
                </div>
                <div class="grid gap-2">
                    <Label for="user-email">Correo</Label>
                    <Input
                        id="user-email"
                        v-model="form.email"
                        type="email"
                        :disabled="isEdit"
                        autocomplete="off"
                    />
                    <InputError :message="errors.email" />
                </div>
                <div class="grid gap-2">
                    <Label for="user-role">Rol</Label>
                    <Select v-model="form.role">
                        <SelectTrigger id="user-role" class="w-full">
                            <SelectValue placeholder="Elige un rol" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="option in ROLE_OPTIONS"
                                :key="option.value"
                                :value="option.value"
                            >
                                {{ option.label }}
                                <span class="text-muted-foreground">
                                    ·
                                    {{
                                        option.location === 'earth'
                                            ? 'Tierra'
                                            : 'Nave'
                                    }}
                                </span>
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.role" />
                </div>
                <div v-if="needsAstronaut" class="grid gap-2">
                    <Label for="user-astronaut">Astronauta</Label>
                    <Select v-model="form.astronaut">
                        <SelectTrigger id="user-astronaut" class="w-full">
                            <SelectValue
                                placeholder="¿Quién de la tripulación?"
                            />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem
                                v-for="member in crew"
                                :key="member.id"
                                :value="member.id.toString()"
                            >
                                {{ member.name }}
                                <span class="text-muted-foreground"
                                    >· {{ member.crew_role }}</span
                                >
                            </SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="errors.astronaut" />
                </div>
            </form>

            <DialogFooter>
                <Button variant="ghost" @click="open = false">Cancelar</Button>
                <Button
                    type="submit"
                    form="user-form"
                    :disabled="saving"
                    class="bg-linear-to-r from-sky-500 to-blue-600 text-white hover:brightness-110"
                >
                    <Spinner v-if="saving" />
                    {{ isEdit ? 'Guardar cambios' : 'Crear usuario' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
