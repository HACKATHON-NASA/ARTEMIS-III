import { createI18n } from 'vue-i18n';

/**
 * Los dos idiomas de la app: inglés (por defecto) y español.
 *
 * Cada módulo trae sus textos en `modules/<Modulo>/Locales/{en,es}.ts` y se
 * leen con su nombre delante: t('Registro.title'). Lo compartido (menú,
 * botones comunes, roles, indicadores…) vive en `locales/{en,es}.ts` y se
 * lee sin prefijo: t('common.save'). El idioma elegido se recuerda en este
 * navegador y viaja a la API en Accept-Language (los mensajes del backend
 * también salen en ese idioma).
 */
export type Locale = 'en' | 'es';

export const LOCALES: { code: Locale; label: string; short: string }[] = [
    { code: 'en', label: 'English', short: 'EN' },
    { code: 'es', label: 'Español', short: 'ES' },
];

const KEY = 'astrohealth.locale';

type Messages = { [key: string]: string | Messages };

function storedLocale(): Locale {
    try {
        return localStorage.getItem(KEY) === 'es' ? 'es' : 'en';
    } catch {
        return 'en';
    }
}

function buildMessages(): Record<Locale, Messages> {
    const messages: Record<Locale, Messages> = { en: {}, es: {} };
    const shared = import.meta.glob<{ default: Messages }>('../locales/*.ts', {
        eager: true,
    });
    const modules = import.meta.glob<{ default: Messages }>(
        '../modules/*/Locales/*.ts',
        { eager: true },
    );

    for (const [path, file] of Object.entries(shared)) {
        const locale = path.match(/\/(en|es)\.ts$/)?.[1] as Locale | undefined;

        if (locale) {
            Object.assign(messages[locale], file.default);
        }
    }

    for (const [path, file] of Object.entries(modules)) {
        const match = path.match(/modules\/(\w+)\/Locales\/(en|es)\.ts$/);

        if (match) {
            messages[match[2] as Locale][match[1]] = file.default;
        }
    }

    return messages;
}

export const i18n = createI18n<false>({
    legacy: false,
    locale: storedLocale(),
    fallbackLocale: 'en',
    messages: buildMessages(),
    missingWarn: false,
    fallbackWarn: false,
});

/**
 * La parte de i18n que se usa fuera de los componentes. Los textos se leen
 * con claves que se arman en tiempo de ejecución, así que aquí no sirve la
 * verificación de claves de vue-i18n (y la hace "excesivamente profunda").
 */
const global = i18n.global as unknown as {
    locale: { value: string };
    t: (
        key: string,
        params?: Record<string, unknown>,
        options?: { locale: string } | number,
    ) => string;
    te: (key: string) => boolean;
};

/**
 * El texto de una clave o, si no existe, el que llegó (p. ej. el nombre de
 * un test que creó el equipo médico y no tiene traducción).
 */
export function tOr(key: string, fallback: string): string {
    return global.te(key) ? global.t(key) : fallback;
}

/** El idioma de ahora (reactivo: quien lo lee se vuelve a pintar al cambiarlo). */
export function currentLocale(): Locale {
    return global.locale.value as Locale;
}

export function setLocale(locale: Locale): void {
    global.locale.value = locale;
    document.documentElement.lang = locale;

    try {
        localStorage.setItem(KEY, locale);
    } catch {
        // Sin localStorage: el idioma dura hasta recargar.
    }
}

/** El texto de una clave en un idioma dado (no en el de ahora). */
export function tIn(locale: Locale, key: string): string {
    return global.t(key, {}, { locale });
}

/** Traducir fuera de un componente (stores, utilidades). */
export function t(
    key: string,
    params?: Record<string, unknown>,
    plural?: number,
): string {
    return plural === undefined
        ? global.t(key, params)
        : global.t(key, params ?? {}, plural);
}

/** Un número con el formato del idioma (1,234.5 · 1.234,5). */
export function formatNumber(
    value: number,
    options?: Intl.NumberFormatOptions,
): string {
    return value.toLocaleString(currentLocale(), options);
}

/** Una fecha u hora con el formato del idioma. */
export function formatDate(
    value: Date | string,
    options: Intl.DateTimeFormatOptions,
): string {
    return new Date(value).toLocaleString(currentLocale(), options);
}
