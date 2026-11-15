/**
 * El ÚNICO cliente HTTP del frontend: habla con /api/v1.
 *
 * - Agrega `Authorization: Bearer <token>` (el token vive en memoria, en useAuthStore).
 * - Devuelve solo `data` del formato { data, message }.
 * - Si el token venció (401 AUTH_TOKEN_EXPIRED), renueva la sesión UNA vez y repite.
 * - Cualquier error sale como ApiError con message, errorCode y errors.
 */

const BASE_URL = '/api/v1';

export class ApiError extends Error {
    constructor(
        public readonly status: number,
        message: string,
        public readonly errorCode: string | null,
        public readonly errors: Record<string, string[]>,
    ) {
        super(message);
        this.name = 'ApiError';
    }

    /** El primer error de un campo (para mostrar debajo del input). */
    fieldError(field: string): string | undefined {
        return this.errors[field]?.[0];
    }
}

/** Lo que el cliente necesita de la sesión. Lo registra useAuthStore. */
type SessionBridge = {
    token: () => string | null;
    refresh: () => Promise<boolean>;
};

let session: SessionBridge | null = null;

export function connectSession(bridge: SessionBridge): void {
    session = bridge;
}

type RequestOptions = {
    method?: 'GET' | 'POST' | 'PATCH' | 'PUT' | 'DELETE';
    body?: unknown;
    /** Enviar el Bearer (por defecto, sí). */
    auth?: boolean;
    /** Renovar y repetir una vez si el token venció (por defecto, sí). */
    retry?: boolean;
};

type Envelope = {
    data?: unknown;
    message?: string;
    error_code?: string;
    errors?: Record<string, string[]>;
};

export async function api<T>(
    path: string,
    options: RequestOptions = {},
): Promise<T> {
    const { method = 'GET', body, auth = true, retry = true } = options;

    const headers: Record<string, string> = { Accept: 'application/json' };
    const token = auth ? (session?.token() ?? null) : null;

    if (body !== undefined) {
        headers['Content-Type'] = 'application/json';
    }

    if (token !== null) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(`${BASE_URL}${path}`, {
        method,
        headers,
        body: body === undefined ? undefined : JSON.stringify(body),
        credentials: 'same-origin',
    });

    if (response.status === 204) {
        return undefined as T;
    }

    const json = (await response.json().catch(() => ({}))) as Envelope;

    if (response.ok) {
        return json.data as T;
    }

    if (
        response.status === 401 &&
        json.error_code === 'AUTH_TOKEN_EXPIRED' &&
        retry &&
        session !== null &&
        (await session.refresh())
    ) {
        return api<T>(path, { ...options, retry: false });
    }

    throw new ApiError(
        response.status,
        json.message ?? 'Ocurrió un error inesperado.',
        json.error_code ?? null,
        json.errors ?? {},
    );
}
