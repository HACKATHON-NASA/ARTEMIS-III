import { tOr } from './i18n';

/**
 * Los textos de los 10 indicadores en el idioma de ahora (locales/*.ts →
 * indicators.<código>). El catálogo es fijo; si llega un código que no se
 * conoce, se usa el texto que mandó la API.
 */
export function indicatorName(code: string, fallback = code): string {
    return tOr(`indicators.${code}.name`, fallback);
}

/** El nombre corto (para las gráficas y las listas angostas). */
export function indicatorShort(code: string, fallback = code): string {
    return tOr(`indicators.${code}.short`, fallback);
}

export function indicatorHow(code: string, fallback = ''): string {
    return tOr(`indicators.${code}.how`, fallback);
}

export function indicatorWhy(code: string, fallback = ''): string {
    return tOr(`indicators.${code}.why`, fallback);
}
