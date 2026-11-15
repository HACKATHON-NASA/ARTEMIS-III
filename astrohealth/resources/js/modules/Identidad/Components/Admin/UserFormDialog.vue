<script setup lang="ts">
import { computed, reactive, ref, watch } from 'vue';
import { toast } from 'vue-sonner';
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
    ManagedUser,
    MissionOption,
    PersonRole,
    UserFormData,
} from '../../Types/admin';
import { ROLE_OPTIONS, roleOption } from '../../Utils/roles';
import UserMissionFields from './UserMissionFields.vue';

/**
 * Crear una persona o editar su nombre, rol y misión. El correo no se
 * cambia después de crearla. La nave (astronauta y médico de la tripulación)
 * y los médicos de vuelo van asociados a una misión; los de la nave, además,
 * con su cargo (así se crea su tripulante). Administración no lleva misión.
 */
const props = defineProps<{
    user: ManagedUser | null;
    missions: MissionOption[];
    crewRoles: string[];
    submit: (data: UserFormData) => Promise<void>;
}>();

const open = defineModel<boolean>('open', { required: true });

const form = reactive({
    name: '',
    email: '',
    role: '',
    mission: '',
    crewRole: '',
});
const errors = ref<Partial<Record<keyof typeof form, string>>>({});
const saving = ref(false);

const isEdit = computed(() => props.user !== null);
const selectedRole = computed(() =>
    roleOption((form.role || null) as PersonRole | null),
);
const needsMission = computed(
    () => selectedRole.value?.requiresMission ?? false,
);
const needsCrewRole = computed(
    () => selectedRole.value?.requiresAstronaut ?? false,
);

// Al corregir un campo, su error se va.
watch(
    () => ({ ...form }),
    (now, before) => {
        for (const key of Object.keys(now) as (keyof typeof form)[]) {
            if (now[key] !== before[key]) {
                delete errors.value[key];
            }
        }
    },
);

// El médico de la tripulación tiene ese cargo de entrada.
watch(
    () => form.role,
    (role) => {
        if (role === 'crew_medical_officer' && form.crewRole === '') {
            form.crewRole = 'Médico de la tripulación';
        }
    },
);

watch(open, (isOpen) => {
    if (!isOpen) {
        return;
    }

    form.name = props.user?.name ?? '';
    form.email = props.user?.email ?? '';
    form.role = props.user?.role ?? '';
    form.mission = props.user?.mission_id?.toString() ?? '';
    form.crewRole = props.user?.crew_role ?? '';
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

    if (needsMission.value && form.mission === '') {
        errors.value.mission = 'Elige la misión de esta persona.';
    }

    if (needsCrewRole.value && form.crewRole === '') {
        errors.value.crewRole = 'Elige su cargo en la tripulación.';
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
            mission_id: needsMission.value ? Number(form.mission) : null,
            crew_role: needsCrewRole.value ? form.crewRole : null,
        });
        open.value = false;
    } catch (error) {
        showServerErrors(error);
    } finally {
        saving.value = false;
    }
}

/** Los errores del backend, debajo del campo al que pertenecen. */
function showServerErrors(error: unknown): void {
    if (!(error instanceof ApiError)) {
        throw error;
    }

    const fields = {
        name: error.fieldError('name'),
        email: error.fieldError('email'),
        role: error.fieldError('role'),
        mission: error.fieldError('mission_id'),
        crewRole: error.fieldError('astronaut_id'),
    };
    errors.value = Object.fromEntries(
        Object.entries(fields).filter(([, message]) => message !== undefined),
    );

    if (Object.keys(errors.value).length === 0) {
        toast.error(error.message);
    }
}
</script>

<template>
    <Dialog v-model:open="open">
        <DialogContent class="max-h-[92vh] overflow-y-auto sm:max-w-md">
            <DialogHeader>
                <DialogTitle>
                    {{ isEdit ? 'Editar usuario' : 'Nuevo usuario' }}
                </DialogTitle>
                <DialogDescription>
                    {{
                        isEdit
                            ? 'Cambia el nombre, el rol o la misión.'
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
                <UserMissionFields
                    v-if="needsMission"
                    v-model:mission="form.mission"
                    v-model:crew-role="form.crewRole"
                    :missions="missions"
                    :crew-roles="crewRoles"
                    :with-crew-role="needsCrewRole"
                    :current-mission="user?.mission_id ?? null"
                    :errors="{
                        mission: errors.mission,
                        crewRole: errors.crewRole,
                    }"
                />
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
