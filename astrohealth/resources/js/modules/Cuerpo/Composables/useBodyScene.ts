import * as THREE from 'three';
import type { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { MeshoptDecoder } from 'three/addons/libs/meshopt_decoder.module.js';
import { onBeforeUnmount, onMounted, reactive, watch } from 'vue';
import type { Ref } from 'vue';
import { needsAttention, statusHex } from '@/lib/status';
import type { BodyZoneCode } from '@/types/health';
import type { BodyStatus } from '../Types/body';
import {
    MODEL_URL,
    prepareModel,
    skinMaterial,
    zoneMaterial,
} from '../Utils/bodyModel';
import { createStage, disposeStage } from '../Utils/bodyStage';
import type { BodyStage } from '../Utils/bodyStage';

/**
 * La escena 3D del cuerpo: carga el modelo, pinta cada zona con el color de
 * su estado, hace latir las que están en amarillo o rojo y avisa qué zona
 * está bajo el puntero y cuál se eligió con un clic.
 *
 * No gira ni flota sola: se mueve solo cuando la persona la arrastra.
 */
type Options = {
    host: Ref<HTMLElement | null>;
    canvas: Ref<HTMLCanvasElement | null>;
    status: () => BodyStatus;
    selected: () => BodyZoneCode | null;
    onSelect: (code: BodyZoneCode) => void;
};

const PULSE_SECONDS = 1.6;

export function useBodyScene(options: Options) {
    const state = reactive({
        loading: true,
        failed: false,
        hovered: null as { zone: BodyZoneCode; x: number; y: number } | null,
    });

    const reducedMotion = window.matchMedia(
        '(prefers-reduced-motion: reduce)',
    ).matches;
    const materials = new Map<BodyZoneCode, THREE.MeshStandardMaterial>();
    const pickables: THREE.Mesh[] = [];
    const raycaster = new THREE.Raycaster();
    const pointer = new THREE.Vector2();
    const clock = new THREE.Clock();
    const home = { position: new THREE.Vector3(), target: new THREE.Vector3() };

    let stage: BodyStage | null = null;
    let renderer: THREE.WebGLRenderer | null = null;
    let scene: THREE.Scene | null = null;
    let camera: THREE.PerspectiveCamera | null = null;
    let controls: OrbitControls | null = null;
    let observer: ResizeObserver | null = null;
    let frame = 0;
    let downAt: { x: number; y: number } | null = null;

    function paint(): void {
        const status = options.status();
        materials.forEach((material, zone) => {
            const hex = statusHex(status[zone].status);
            material.color.set(hex);
            material.emissive.set(hex);
        });
    }

    function frameCamera(box: THREE.Box3): void {
        if (!camera || !controls) {
            return;
        }

        const size = box.getSize(new THREE.Vector3());
        const center = box.getCenter(new THREE.Vector3());
        // 1.3: margen para que los controles de abajo no tapen los pies.
        const distance =
            (size.y / 2 / Math.tan(THREE.MathUtils.degToRad(camera.fov / 2))) *
            1.3;
        home.target.set(center.x, center.y - size.y * 0.03, center.z);
        home.position.set(
            center.x,
            center.y + size.y * 0.04,
            center.z + distance,
        );
        resetView();
    }

    function addEarMarker(skull: THREE.Object3D | undefined): void {
        if (!skull || !scene) {
            return;
        }

        const box = new THREE.Box3().setFromObject(skull);
        const center = box.getCenter(new THREE.Vector3());
        const material = zoneMaterial(statusHex(null));
        const marker = new THREE.Mesh(
            new THREE.SphereGeometry(0.014, 24, 16),
            material,
        );
        marker.position.set(box.max.x - 0.006, center.y - 0.01, center.z);
        marker.userData.zone = 'ear';
        materials.set('ear', material);
        pickables.push(marker);
        scene.add(marker);
    }

    async function load(): Promise<void> {
        try {
            const loader = new GLTFLoader().setMeshoptDecoder(MeshoptDecoder);
            const gltf = await loader.loadAsync(MODEL_URL);

            if (!scene) {
                return;
            }

            const { skin, zones, skull } = prepareModel(gltf.scene);
            const skinLook = skinMaterial();
            skin.forEach((mesh) => (mesh.material = skinLook));
            zones.forEach((meshes, zone) => {
                const material = zoneMaterial(statusHex(null));
                materials.set(zone, material);
                meshes.forEach((mesh) => {
                    mesh.material = material;
                    pickables.push(mesh);
                });
            });

            scene.add(gltf.scene);
            addEarMarker(skull);
            frameCamera(
                new THREE.Box3().setFromObject(
                    gltf.scene.getObjectByName('skin') ?? gltf.scene,
                ),
            );
            paint();
            state.loading = false;
        } catch (error) {
            console.error('No se pudo cargar el modelo 3D del cuerpo:', error);
            state.failed = true;
        }
    }

    function pick(event: PointerEvent): BodyZoneCode | null {
        const canvas = options.canvas.value;

        if (!canvas || !camera) {
            return null;
        }

        const rect = canvas.getBoundingClientRect();
        pointer.set(
            ((event.clientX - rect.left) / rect.width) * 2 - 1,
            -((event.clientY - rect.top) / rect.height) * 2 + 1,
        );
        raycaster.setFromCamera(pointer, camera);
        const hit = raycaster.intersectObjects(pickables, false)[0];
        const zone =
            (hit?.object.userData.zone as BodyZoneCode | undefined) ?? null;

        state.hovered = zone
            ? {
                  zone,
                  x: ((event.clientX - rect.left) / rect.width) * 100,
                  y: ((event.clientY - rect.top) / rect.height) * 100,
              }
            : null;
        canvas.style.cursor = zone ? 'pointer' : 'grab';

        return zone;
    }

    function onPointerDown(event: PointerEvent): void {
        downAt = { x: event.clientX, y: event.clientY };
    }

    function onPointerUp(event: PointerEvent): void {
        const moved = downAt
            ? Math.hypot(event.clientX - downAt.x, event.clientY - downAt.y)
            : 99;
        downAt = null;
        const zone = moved < 5 ? pick(event) : null;

        if (zone) {
            options.onSelect(zone);
        }
    }

    function animate(): void {
        frame = requestAnimationFrame(animate);
        const t = clock.getElapsedTime();
        const status = options.status();
        const selected = options.selected();

        materials.forEach((material, zone) => {
            const active = zone === selected || zone === state.hovered?.zone;
            const beat = needsAttention(status[zone].status)
                ? reducedMotion
                    ? 0.6
                    : 0.35 +
                      0.5 *
                          (0.5 +
                              0.5 * Math.sin((t * 2 * Math.PI) / PULSE_SECONDS))
                : 0.3;
            material.emissiveIntensity = active ? Math.max(beat, 0.9) : beat;
        });

        controls?.update();

        if (renderer && scene && camera) {
            renderer.render(scene, camera);
        }
    }

    function resize(): void {
        const host = options.host.value;

        if (!host || !renderer || !camera) {
            return;
        }

        camera.aspect =
            Math.max(1, host.clientWidth) / Math.max(1, host.clientHeight);
        camera.updateProjectionMatrix();
        renderer.setSize(host.clientWidth, host.clientHeight, false);
    }

    function resetView(): void {
        if (!camera || !controls) {
            return;
        }

        camera.position.copy(home.position);
        controls.target.copy(home.target);
        controls.update();
    }

    function zoom(factor: number): void {
        if (!camera || !controls) {
            return;
        }

        const offset = camera.position
            .clone()
            .sub(controls.target)
            .multiplyScalar(factor);
        offset.clampLength(controls.minDistance, controls.maxDistance);
        camera.position.copy(controls.target).add(offset);
        controls.update();
    }

    onMounted(() => {
        const canvas = options.canvas.value;

        if (!canvas) {
            return;
        }

        try {
            stage = createStage(canvas);
        } catch {
            state.failed = true;

            return;
        }

        ({ renderer, scene, camera, controls } = stage);

        canvas.addEventListener('pointermove', pick);
        canvas.addEventListener('pointerdown', onPointerDown);
        canvas.addEventListener('pointerup', onPointerUp);
        canvas.addEventListener('pointerleave', () => (state.hovered = null));
        observer = new ResizeObserver(resize);

        if (options.host.value) {
            observer.observe(options.host.value);
        }

        resize();
        animate();
        void load();
    });

    watch(options.status, paint, { deep: true });

    onBeforeUnmount(() => {
        cancelAnimationFrame(frame);
        observer?.disconnect();

        if (stage) {
            disposeStage(stage);
        }
    });

    return { state, resetView, zoom: (factor: number) => zoom(factor) };
}
