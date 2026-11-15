import * as THREE from 'three';
import type { BodyZoneCode } from '@/types/health';

/**
 * Cómo se usa el modelo anatómico (open-anatomy-3d, CC BY 4.0) para
 * nuestras 9 zonas: qué piezas reales del modelo forman cada zona y cómo se
 * pinta. El modelo viene en metros, con los pies en y = 0 y estos grupos:
 * skin, skeleton, muscles, organs, vessels, nerves.
 */

export const MODEL_URL = '/models/anatomy-male.glb';

export const MODEL_CREDIT =
    'Anatomía: BodyParts3D © DBCLS (CC BY 4.0) · modificado por open-anatomy-3d (CC BY 4.0)';

export const MODEL_CREDIT_URL = 'https://github.com/MervinR07/open-anatomy-3d';

/** Piezas reales del modelo que forman cada zona. `ear` no tiene malla: va con un marcador. */
export const ZONE_MESHES: Partial<Record<BodyZoneCode, string[]>> = {
    heart: ['heart'],
    brain: ['brain', 'cerebellum', 'brainstem'],
    eyes: ['eye_l', 'eye_r'],
    lungs: ['lung_l', 'lung_r', 'bronchi', 'trachea'],
    immune: ['thymus', 'spleen'],
    metabolism: ['liver', 'pancreas', 'stomach'],
    bones: [
        'femur_l',
        'femur_r',
        'lower_leg_l',
        'lower_leg_r',
        'patella_l',
        'patella_r',
        'pelvis',
    ],
};

/** Grupos del modelo que no se muestran (se ven solo la piel y las zonas). */
const HIDDEN_GROUPS = ['muscles', 'vessels', 'nerves'];

/** La piel: silicona azul translúcida, para ver los órganos por dentro. */
export function skinMaterial(): THREE.MeshPhysicalMaterial {
    return new THREE.MeshPhysicalMaterial({
        color: 0xcfe6ff,
        roughness: 0.32,
        metalness: 0.05,
        clearcoat: 0.6,
        clearcoatRoughness: 0.25,
        transparent: true,
        opacity: 0.22,
        depthWrite: false,
        side: THREE.FrontSide,
    });
}

/** Un órgano: el color de su estado, con brillo propio para que se lea a través de la piel. */
export function zoneMaterial(hex: string): THREE.MeshStandardMaterial {
    const color = new THREE.Color(hex);

    return new THREE.MeshStandardMaterial({
        color,
        emissive: color,
        emissiveIntensity: 0.35,
        roughness: 0.45,
        metalness: 0.05,
    });
}

/**
 * Prepara el modelo: esconde lo que no se usa y devuelve, por zona, las
 * mallas que la forman (para pintarlas y para el clic).
 */
export function prepareModel(model: THREE.Object3D): {
    skin: THREE.Mesh[];
    zones: Map<BodyZoneCode, THREE.Mesh[]>;
    skull: THREE.Object3D | undefined;
} {
    HIDDEN_GROUPS.forEach((name) => {
        const group = model.getObjectByName(name);

        if (group) {
            group.visible = false;
        }
    });

    const wanted = new Map<string, BodyZoneCode>();
    Object.entries(ZONE_MESHES).forEach(([zone, names]) =>
        names?.forEach((name) => wanted.set(name, zone as BodyZoneCode)),
    );

    // En "organs" y "skeleton" solo quedan visibles las piezas de las zonas.
    ['organs', 'skeleton'].forEach((groupName) => {
        model.getObjectByName(groupName)?.children.forEach((child) => {
            child.visible = wanted.has(child.name);
        });
    });

    const zones = new Map<BodyZoneCode, THREE.Mesh[]>();
    wanted.forEach((zone, name) => {
        model.getObjectByName(name)?.traverse((object) => {
            if (object instanceof THREE.Mesh) {
                object.userData.zone = zone;
                zones.set(zone, [...(zones.get(zone) ?? []), object]);
            }
        });
    });

    const skin: THREE.Mesh[] = [];
    model.getObjectByName('skin')?.traverse((object) => {
        if (object instanceof THREE.Mesh) {
            object.renderOrder = 2;
            skin.push(object);
        }
    });

    return { skin, zones, skull: model.getObjectByName('skull') };
}
