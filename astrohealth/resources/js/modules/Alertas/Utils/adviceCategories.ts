import {
    Apple,
    Brain,
    Dumbbell,
    Moon,
    Pill,
    Sofa,
    Stethoscope,
} from '@lucide/vue';
import type { Component } from 'vue';
import { t } from '@/lib/i18n';
import type { AdviceCategory } from '../Types/advice';

/** El nombre (en el idioma de ahora) y el ícono de cada tema de una indicación. */
type CategoryInfo = { icon: Component; readonly label: string };

function category(code: AdviceCategory, icon: Component): CategoryInfo {
    return {
        icon,
        get label() {
            return t(`Alertas.adviceCategories.${code}`);
        },
    };
}

export const ADVICE_CATEGORIES: Record<AdviceCategory, CategoryInfo> = {
    sleep: category('sleep', Moon),
    exercise: category('exercise', Dumbbell),
    nutrition: category('nutrition', Apple),
    medication: category('medication', Pill),
    rest: category('rest', Sofa),
    mental: category('mental', Brain),
    other: category('other', Stethoscope),
};
