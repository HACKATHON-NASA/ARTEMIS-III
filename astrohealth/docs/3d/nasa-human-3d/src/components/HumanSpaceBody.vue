<template>
  <section ref="host" class="space-body" aria-label="Modelo anatómico interactivo de riesgos espaciales">
    <canvas ref="canvas" class="space-body__canvas" aria-hidden="true"></canvas>
    <div class="space-body__halo" aria-hidden="true"></div>

    <div v-if="loading" class="space-body__loading">Cargando figura anatómica 3D…</div>
    <div v-if="error" class="space-body__error" role="alert">
      {{ error }}
      <button @click="loadBody">Reintentar</button>
    </div>

    <template v-if="!loading && !error">
      <button
        v-for="point in projectedPoints" :key="point.id"
        class="space-body__pin"
        :class="{ 'is-active': selected === point.id, 'is-occluded': !point.visible }"
        :style="{left: point.x + 'px', top: point.y + 'px', '--tone': point.color}"
        :title="point.label"
        :aria-label="`Seleccionar ${point.label}`"
        :aria-pressed="selected === point.id"
        @mouseenter="hovered = point.id" @mouseleave="hovered = null"
        @focus="hovered = point.id" @blur="hovered = null"
        @click="choose(point.id)"
      >
        <span class="space-body__pin-ring"></span>
        <span class="space-body__pin-core"></span>
        <span class="space-body__pin-label" :class="point.side">{{ point.icon }} {{ point.label }}</span>
      </button>
    </template>

    <div class="space-body__controls">
      <button @click="resetView" title="Restablecer vista" aria-label="Restablecer vista">↺</button>
      <button @click="autoRotate = !autoRotate" :aria-pressed="autoRotate" :title="autoRotate ? 'Detener giro' : 'Activar giro'">{{ autoRotate ? 'Ⅱ' : '↻' }}</button>
      <button @click="zoom(-0.25)" title="Acercar">＋</button>
      <button @click="zoom(0.25)" title="Alejar">－</button>
    </div>
    <p v-if="selected && !error" class="space-body__selection">{{ selectedData.icon }} {{ selectedData.label }}</p>
    <small class="space-body__hint">Arrastra para girar · Rueda para zoom · Selecciona un punto</small>
  </section>
</template>

<script setup>
import { computed, nextTick, onBeforeUnmount, onMounted, ref } from 'vue'
import * as THREE from 'three'
import { OrbitControls } from 'three/addons/controls/OrbitControls.js'
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js'
import { MeshoptDecoder } from 'three/addons/libs/meshopt_decoder.module.js'

// En metros: el modelo anatómico tiene 1.7–1.8 m de altura y los pies en y=0.
// Los puntos son zonas de INTERFAZ, no coordenadas clínicas exactas.
const hazards = [
  { id:'heart', label:'Corazón', icon:'♥', color:'#ff637b', at:[0.10,1.34,0.17], side:'right', risk:'La microgravedad modifica el sistema cardiovascular.' },
  { id:'bones', label:'Huesos y músculos', icon:'◆', color:'#a0dbff', at:[-0.16,0.86,0.13], side:'left', risk:'La microgravedad favorece la pérdida ósea y muscular.' },
  { id:'eyes', label:'Ojos', icon:'◉', color:'#75d7ff', at:[0.09,1.64,0.13], side:'right', risk:'El SANS puede producir cambios neurooculares.' },
  { id:'mind', label:'Mente', icon:'✦', color:'#c0a1ff', at:[-0.10,1.75,0.05], side:'left', risk:'El aislamiento puede afectar la cognición y el bienestar.' },
  { id:'sleep', label:'Sueño', icon:'☾', color:'#8dc8ff', at:[0.18,1.52,0.10], side:'right', risk:'Los ciclos de sueño y los ritmos circadianos pueden alterarse.' },
  { id:'immune', label:'Sistema inmune', icon:'✚', color:'#7df2cf', at:[-0.12,1.12,0.15], side:'left', risk:'El vuelo espacial puede alterar la respuesta inmunológica.' },
  { id:'ear', label:'Oído interno', icon:'◌', color:'#f9b0ff', at:[-0.13,1.62,0.06], side:'left', risk:'Los cambios de gravedad afectan la orientación vestibular.' },
  { id:'nutrition', label:'Nutrición', icon:'●', color:'#ffbe75', at:[0.11,1.04,0.16], side:'right', risk:'Una dieta insuficiente afecta la salud y el rendimiento.' },
  { id:'radiation', label:'Radiación', icon:'☢', color:'#ffe07b', at:[0.17,0.62,0.12], side:'right', risk:'La radiación espacial eleva riesgos a largo plazo.' },
]

const emit = defineEmits(['select'])
const host = ref(null)
const canvas = ref(null)
const loading = ref(true)
const error = ref('')
const selected = ref(null)
const hovered = ref(null)
const autoRotate = ref(true)
const projectedPoints = ref([])
const selectedData = computed(() => hazards.find(p => p.id === selected.value) || {})
let scene, camera, renderer, controls, bodyRoot, model, markerGroup, resizeObserver, frameId
let disposed = false
let startTime = performance.now()
const clock = new THREE.Clock()
const temp = new THREE.Vector3()
const target = new THREE.Vector3(0, .88, 0)

function createScene() {
  scene = new THREE.Scene()
  camera = new THREE.PerspectiveCamera(36, 1, 0.01, 100)
  camera.position.set(0.16, 1.02, 4.1)
  renderer = new THREE.WebGLRenderer({canvas:canvas.value, antialias:true, alpha:true, powerPreference:'high-performance'})
  renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2))
  renderer.outputColorSpace = THREE.SRGBColorSpace
  renderer.toneMapping = THREE.ACESFilmicToneMapping
  renderer.toneMappingExposure = 1.8
  controls = new OrbitControls(camera, canvas.value)
  controls.target.copy(target)
  controls.enableDamping = true
  controls.dampingFactor = .065
  controls.enablePan = false
  controls.minDistance = 2.3
  controls.maxDistance = 6.0
  controls.minPolarAngle = .48
  controls.maxPolarAngle = Math.PI - .48
  controls.update()

  scene.add(new THREE.HemisphereLight(0xd9eeff, 0x354a77, 3.4))
  const key = new THREE.DirectionalLight(0xffffff, 4.0)
  key.position.set(-2, 3, 5); scene.add(key)
  const rim = new THREE.DirectionalLight(0x42bdff, 5.0)
  rim.position.set(2.5, 2, -3); scene.add(rim)
  const fill = new THREE.DirectionalLight(0xa7dbff, 2.0)
  fill.position.set(3, 1, 2); scene.add(fill)

  bodyRoot = new THREE.Group(); scene.add(bodyRoot)
  markerGroup = new THREE.Group(); bodyRoot.add(markerGroup)
  hazards.forEach(p => {
    const core = new THREE.Mesh(
      new THREE.SphereGeometry(.016, 20, 12),
      new THREE.MeshBasicMaterial({color:p.color})
    )
    core.position.set(...p.at)
    core.userData.id = p.id
    markerGroup.add(core)
  })
  resize()
  animate()
}

function resize() {
  if (!host.value || !renderer || !camera) return
  const w = Math.max(1, host.value.clientWidth), h = Math.max(1, host.value.clientHeight)
  camera.aspect = w/h; camera.updateProjectionMatrix(); renderer.setSize(w,h,false)
}

async function loadBody() {
  loading.value = true; error.value = ''
  try {
    const loader = new GLTFLoader().setMeshoptDecoder(MeshoptDecoder)
    // Modelo libre de anatomía real (CC BY 4.0). Hospédalo localmente para producción.
    const gltf = await loader.loadAsync('/models/anatomy-male.glb')
    if (disposed) return
    if (model) bodyRoot.remove(model)
    model = gltf.scene
    // Por defecto, mostrar solo piel; interior escondido para acabado de maniquí de silicona.
    // Mantener la anatomía separada para un futuro modo de transparencia y órganos.
    const layers = ['skeleton','muscles','organs','vessels','nerves','joints']
    layers.forEach(name => {const obj = model.getObjectByName(name); if (obj) obj.visible = false})
    // Recolorear mallas visibles con un acabado perlado/satinado, NO formar cuerpo con primitivas.
    const silicone = new THREE.MeshPhysicalMaterial({
      color:0xd2e8ff, metalness:0.04, roughness:0.27,
      clearcoat:0.5, clearcoatRoughness:0.28,
      transparent:true, opacity:0.83, side:THREE.DoubleSide,
      depthWrite:true,
    })
    model.traverse(obj => {
      if(obj.isMesh) { obj.material = silicone; obj.frustumCulled = false }
    })
    bodyRoot.add(model)
    // Evitar duplicación de offsets; modelo original en metros con pies en y=0.
    loading.value = false
  } catch (e) {
    console.error('Error al abrir el modelo anatómico:',e)
    error.value = 'No se pudo cargar el archivo /models/anatomy-male.glb. Revisa la instalación del modelo.'
    loading.value = false
  }
}

function choose(id) {
  selected.value = selected.value === id ? null : id
  const p = hazards.find(x => x.id === selected.value)
  if (p) { autoRotate.value = false; emit('select', {id:p.id,label:p.label,risk:p.risk}) }
  else emit('select', null)
}
function resetView() {
  selected.value = null; hovered.value = null; autoRotate.value = true
  camera.position.set(0.16,1.02,4.1); controls.target.copy(target); controls.update()
  emit('select', null)
}
function zoom(delta) {
  temp.copy(camera.position).sub(controls.target).multiplyScalar(1+delta)
  temp.clampLength(controls.minDistance, controls.maxDistance)
  camera.position.copy(controls.target).add(temp)
  controls.update()
}

function animate() {
  if (disposed) return
  frameId = requestAnimationFrame(animate)
  const t = clock.getElapsedTime()
  if (bodyRoot) {
    // Respiración sutil: sin deformar anatomía ni animación brusca.
    bodyRoot.position.y = .008 * Math.sin(t*1.5)
    if (autoRotate.value && !window.matchMedia('(prefers-reduced-motion: reduce)').matches)
      bodyRoot.rotation.y += .00145
  }
  if (markerGroup) {
    markerGroup.children.forEach((mesh,i) => {
      const id = hazards[i].id
      const active = id === selected.value || id === hovered.value
      mesh.scale.setScalar(active ? 1.9 + .25*Math.sin(t*5) : 1 + .17*Math.sin(t*2.8+i))
    })
  }
  controls?.update()
  if (host.value && camera && bodyRoot && !loading.value) {
    const w=host.value.clientWidth, h=host.value.clientHeight
    const positions=hazards.map(p=>{
      const v = new THREE.Vector3(...p.at)
      bodyRoot.localToWorld(v)
      v.project(camera)
      return {...p, x:(v.x*.5+.5)*w, y:(-.5*v.y+.5)*h, visible: v.z < 1 && v.z > -1 }
    })
    projectedPoints.value = positions
  }
  renderer?.render(scene,camera)
}

onMounted(async () => {
  await nextTick()
  if (!host.value || !canvas.value) return
  createScene()
  resizeObserver = new ResizeObserver(resize)
  resizeObserver.observe(host.value)
  loadBody()
})
onBeforeUnmount(() => {
  disposed = true
  cancelAnimationFrame(frameId)
  resizeObserver?.disconnect()
  controls?.dispose()
  scene?.traverse(o=>{
    if (o.geometry) o.geometry.dispose()
    if (o.material) {
      const materials=Array.isArray(o.material)?o.material:[o.material]
      materials.forEach(m=>m.dispose())
    }
  })
  renderer?.dispose()
})
</script>

<style scoped>
.space-body { position:relative; overflow:hidden; isolation:isolate; width:100%; height:min(80vh,760px); min-height:520px; border-radius:28px; background:radial-gradient(ellipse at 50% 47%,#174168 0%,#07192c 58%,#050e1a 100%); color:#fff; font-family:Inter,ui-sans-serif,system-ui,-apple-system,Segoe UI,sans-serif; }
.space-body__canvas { position:absolute;inset:0; width:100%; height:100%; z-index:1; touch-action:none; cursor:grab; }
.space-body__canvas:active {cursor:grabbing}
.space-body__halo { position:absolute;z-index:0;left:50%;top:50%;width:50%;height:80%;transform:translate(-50%,-50%);border-radius:50%;background:radial-gradient(ellipse,#3b9cfd27,transparent 66%);filter:blur(18px);pointer-events:none }
.space-body__pin { position:absolute;z-index:3;transform:translate(-50%,-50%);width:24px;height:24px;display:grid;place-items:center;padding:0;border:0;border-radius:50%;background:transparent;cursor:pointer;color:#fff;outline-offset:8px; }
.space-body__pin.is-occluded {display:none}
.space-body__pin-core {width:12px;height:12px;border:3px solid #fff;border-radius:50%;background:var(--tone);box-shadow:0 0 0 4px #ffffff38,0 0 16px 5px var(--tone);transition:transform .2s;}
.space-body__pin-ring {position:absolute;inset:-8px;border-radius:50%;border:1px solid var(--tone);opacity:.7;animation:pinPulse 2.8s ease-out infinite;pointer-events:none;}
.space-body__pin:hover .space-body__pin-core,.space-body__pin.is-active .space-body__pin-core {transform:scale(1.42)}
.space-body__pin-label {position:absolute;display:inline-flex;align-items:center;gap:5px;top:50%;transform:translateY(-50%);white-space:nowrap;max-width:155px;overflow:hidden;text-overflow:ellipsis;padding:6px 10px;border:1px solid #c8eaff4a;border-radius:9px;background:#071e32d9;box-shadow:0 5px 25px #0004;color:#d9f2ff;letter-spacing:.01em;font-size:11px;font-weight:600;backdrop-filter:blur(12px);transition:background .2s,border-color .2s;}
.space-body__pin-label.right {left:24px}.space-body__pin-label.left {right:24px}
.space-body__pin:hover .space-body__pin-label,.space-body__pin.is-active .space-body__pin-label{background:#13527b;border-color:var(--tone)}
.space-body__controls {position:absolute;z-index:5;bottom:24px;left:50%;transform:translateX(-50%);display:flex;gap:7px;padding:7px;border:1px solid #a3e7ff40;border-radius:100px;background:#071725bd;backdrop-filter:blur(14px)}
.space-body__controls button {display:grid;place-items:center;width:36px;height:36px;border:0;border-radius:50%;background:#2b58784d;color:#e9faff;font-size:21px;cursor:pointer;transition:background .2s}
.space-body__controls button:hover {background:#328ac4}
.space-body__hint {position:absolute;z-index:2;bottom:82px;left:12px;right:12px;text-align:center;color:#aacbdf;font-size:11px;pointer-events:none}
.space-body__selection {position:absolute;z-index:3;top:17px;left:50%;transform:translateX(-50%);margin:0;padding:9px 16px;border:1px solid #a3e7ff45;border-radius:50px;background:#061e32cc;font-size:13px;white-space:nowrap}
.space-body__loading,.space-body__error {position:absolute;z-index:6;top:45%;left:50%;transform:translate(-50%,-50%);width:80%;text-align:center;color:#c9e8ff;font-size:14px}
.space-body__error button {margin:12px;display:inline-block;padding:8px 14px;border:1px solid #9be7ff;border-radius:10px;background:#173a55;color:#fff;cursor:pointer}
@keyframes pinPulse {0%{transform:scale(.72);opacity:.9}75%,100%{transform:scale(1.7);opacity:0}}
@media (max-width:680px){.space-body{height:72vh;min-height:480px;border-radius:18px}.space-body__pin-label{font-size:9px;padding:5px 6px;max-width:108px}.space-body__pin-label.right{left:17px}.space-body__pin-label.left{right:17px}.space-body__pin-core{width:10px;height:10px}}
@media (prefers-reduced-motion:reduce){.space-body__pin-ring{animation:none}}
</style>
