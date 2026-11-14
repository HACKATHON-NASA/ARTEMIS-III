import { defineStore } from 'pinia';
import { computed, ref } from 'vue';
import { api, connectSession } from '@/lib/api';
import type { Actor, TokenResponse } from '@/types/auth';

/**
 * La sesión (M0 · Identidad, JWT).
 *
 * - El access token vive SOLO en memoria (nunca en localStorage).
 * - El refresh token viaja en una cookie HttpOnly que JavaScript no ve:
 *   al recargar la página, ensureSession() llama a /auth/refresh y la sesión vuelve.
 * - can() oculta o muestra botones; el backend vuelve a comprobar siempre.
 */
export const useAuthStore = defineStore('auth', () => {
    const accessToken = ref<string | null>(null);
    const actor = ref<Actor | null>(null);

    const isAuthenticated = computed(() => accessToken.value !== null);

    let refreshing: Promise<boolean> | null = null;

    function start(response: TokenResponse): void {
        accessToken.value = response.access_token;
        actor.value = response.actor;
    }

    function clear(): void {
        accessToken.value = null;
        actor.value = null;
    }

    async function login(email: string, password: string): Promise<void> {
        const response = await api<TokenResponse>('/auth/login', {
            method: 'POST',
            body: { email, password },
            auth: false,
            retry: false,
        });

        start(response);
    }

    /** Renueva la sesión con la cookie. Si hay varias llamadas a la vez, comparten una. */
    function refresh(): Promise<boolean> {
        refreshing ??= api<TokenResponse>('/auth/refresh', {
            method: 'POST',
            auth: false,
            retry: false,
        })
            .then((response) => {
                start(response);

                return true;
            })
            .catch(() => {
                clear();

                return false;
            })
            .finally(() => {
                refreshing = null;
            });

        return refreshing;
    }

    /** ¿Hay sesión? Si no hay token en memoria, intenta recuperarla con la cookie. */
    async function ensureSession(): Promise<boolean> {
        return isAuthenticated.value || refresh();
    }

    async function logout(): Promise<void> {
        try {
            // Con retry: si el token ya venció, se renueva y se cierra la sesión igual.
            await api<void>('/auth/logout', { method: 'POST' });
        } finally {
            clear();
        }
    }

    function can(permission: string): boolean {
        return actor.value?.permissions.includes(permission) ?? false;
    }

    connectSession({ token: () => accessToken.value, refresh });

    return {
        accessToken,
        actor,
        isAuthenticated,
        login,
        refresh,
        ensureSession,
        logout,
        can,
    };
});
