import { onBeforeUnmount, onMounted, ref } from 'vue';
import type { Ref } from 'vue';

/** El ancho real de un elemento (las gráficas se dibujan a su tamaño). */
export function useElementWidth(element: Ref<HTMLElement | null>) {
    const width = ref(0);
    let observer: ResizeObserver | null = null;

    onMounted(() => {
        if (!element.value) {
            return;
        }

        observer = new ResizeObserver(([entry]) => {
            width.value = entry.contentRect.width;
        });
        observer.observe(element.value);
        width.value = element.value.clientWidth;
    });

    onBeforeUnmount(() => observer?.disconnect());

    return width;
}
