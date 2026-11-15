import type { Ref } from 'vue';
import type { BodyZoneCode } from '@/types/health';
import type { Flight, Measurement } from '../Types/measurement';
import { FIELD_GROUPS, ZONE_HEIGHT } from '../Utils/fieldGroups';

/**
 * El puente entre el formulario y el cuerpo: tocar un órgano enfoca su primer
 * campo, y al guardar se calcula de dónde a dónde vuela cada valor (del
 * centro de su campo a la altura de su órgano en el visor). Si el cuerpo no
 * está en pantalla (en el celular queda abajo), no hay vuelo.
 */
export function useBodyLink(
    root: Ref<HTMLElement | null>,
    bodyHost: Ref<HTMLElement | null>,
) {
    function focusZoneField(code: BodyZoneCode): void {
        const first = FIELD_GROUPS.find((group) => group.zone === code)
            ?.indicators[0];

        root.value
            ?.querySelector<HTMLElement>(
                `[data-indicator="${first}"] input, [data-indicator="${first}"] [role="radio"]`,
            )
            ?.focus();
    }

    function planFlights(saved: Measurement[]): Flight[] {
        const target = bodyHost.value?.getBoundingClientRect();

        if (!target || target.bottom < 0 || target.top > window.innerHeight) {
            return [];
        }

        return saved.flatMap((item) => {
            const from = root.value
                ?.querySelector(`[data-indicator="${item.indicator}"]`)
                ?.getBoundingClientRect();

            if (!from) {
                return [];
            }

            const unit = item.unit === '1-5' ? '/ 5' : item.unit;

            return [
                {
                    id: `${item.id}`,
                    label: `${item.value.toLocaleString('es')} ${unit}`,
                    status: item.status,
                    from: {
                        x: from.left + from.width / 2,
                        y: from.top + from.height / 2,
                    },
                    to: {
                        x: target.left + target.width / 2,
                        y:
                            target.top +
                            target.height * ZONE_HEIGHT[item.body_part],
                    },
                },
            ];
        });
    }

    return { focusZoneField, planFlights };
}
