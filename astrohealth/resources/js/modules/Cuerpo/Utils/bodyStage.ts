import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

/**
 * El escenario del cuerpo 3D: renderer, cámara, controles (girar y acercar,
 * sin desplazar) y luces azules. Lanza un error si el equipo no tiene WebGL.
 */
export type BodyStage = {
    renderer: THREE.WebGLRenderer;
    scene: THREE.Scene;
    camera: THREE.PerspectiveCamera;
    controls: OrbitControls;
};

export function createStage(canvas: HTMLCanvasElement): BodyStage {
    const renderer = new THREE.WebGLRenderer({
        canvas,
        antialias: true,
        alpha: true,
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.3;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(30, 1, 0.05, 50);

    const controls = new OrbitControls(camera, canvas);
    controls.enableDamping = true;
    controls.dampingFactor = 0.08;
    controls.enablePan = false;
    controls.minDistance = 1.2;
    controls.maxDistance = 6;
    controls.minPolarAngle = 0.5;
    controls.maxPolarAngle = Math.PI - 0.5;

    scene.add(new THREE.HemisphereLight(0xdbeafe, 0x1e3a8a, 2.2));
    const key = new THREE.DirectionalLight(0xffffff, 2.4);
    key.position.set(-2, 3, 4);
    const rim = new THREE.DirectionalLight(0x38bdf8, 4);
    rim.position.set(2.5, 2, -3);
    scene.add(key, rim);

    return { renderer, scene, camera, controls };
}

/** Libera la memoria de la GPU (geometrías, materiales y renderer). */
export function disposeStage(stage: BodyStage): void {
    stage.controls.dispose();
    stage.scene.traverse((object) => {
        if (object instanceof THREE.Mesh) {
            object.geometry.dispose();
            (Array.isArray(object.material)
                ? object.material
                : [object.material]
            ).forEach((material) => material.dispose());
        }
    });
    stage.renderer.dispose();
}
