import * as THREE from "https://unpkg.com/three@0.184.0/build/three.module.js";

const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

if (!prefersReducedMotion) {
  initMarketField();
}

function initMarketField() {
  const mount = document.createElement("canvas");
  mount.id = "freeride-market-field";
  mount.setAttribute("aria-hidden", "true");
  document.body.prepend(mount);

  let renderer;

  try {
    renderer = new THREE.WebGLRenderer({
      canvas: mount,
      alpha: true,
      antialias: true,
      powerPreference: "high-performance",
    });
  } catch (error) {
    mount.remove();
    return;
  }

  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(58, 1, 0.1, 1000);
  camera.position.set(0, 12, 76);
  camera.lookAt(0, 0, 0);

  const count = 900;
  const positions = new Float32Array(count * 3);
  const base = new Float32Array(count * 3);
  const colors = new Float32Array(count * 3);
  const colorA = new THREE.Color("#70e1ff");
  const colorB = new THREE.Color("#a78bfa");

  for (let i = 0; i < count; i += 1) {
    const row = Math.floor(i / 30);
    const col = i % 30;
    const x = (col - 14.5) * 4.1;
    const z = (row - 14.5) * 3.8;
    const y = Math.sin(col * 0.7) * 1.2 + Math.cos(row * 0.45) * 1.6;
    const index = i * 3;
    const color = colorA.clone().lerp(colorB, row / 30);

    base[index] = positions[index] = x;
    base[index + 1] = positions[index + 1] = y;
    base[index + 2] = positions[index + 2] = z;

    colors[index] = color.r;
    colors[index + 1] = color.g;
    colors[index + 2] = color.b;
  }

  const geometry = new THREE.BufferGeometry();
  geometry.setAttribute("position", new THREE.BufferAttribute(positions, 3));
  geometry.setAttribute("color", new THREE.BufferAttribute(colors, 3));

  const points = new THREE.Points(
    geometry,
    new THREE.PointsMaterial({
      size: 0.26,
      vertexColors: true,
      transparent: true,
      opacity: 0.78,
      depthWrite: false,
      blending: THREE.AdditiveBlending,
    }),
  );
  points.rotation.x = -0.38;
  points.rotation.z = 0.05;
  scene.add(points);

  const lineGeometry = new THREE.BufferGeometry();
  const linePositions = new Float32Array(120 * 3);
  lineGeometry.setAttribute("position", new THREE.BufferAttribute(linePositions, 3));
  const line = new THREE.Line(
    lineGeometry,
    new THREE.LineBasicMaterial({
      color: "#86efac",
      transparent: true,
      opacity: 0.22,
      blending: THREE.AdditiveBlending,
    }),
  );
  line.rotation.x = points.rotation.x;
  scene.add(line);

  const clock = new THREE.Clock();
  const resize = () => {
    const width = window.innerWidth;
    const height = window.innerHeight;
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.6));
    renderer.setSize(width, height, false);
    camera.aspect = width / height;
    camera.position.z = width < 760 ? 94 : 76;
    camera.updateProjectionMatrix();
  };

  window.addEventListener("resize", resize, { passive: true });
  resize();

  renderer.setAnimationLoop(() => {
    const elapsed = clock.getElapsedTime();
    const positionAttribute = geometry.getAttribute("position");

    for (let i = 0; i < count; i += 1) {
      const index = i * 3;
      const x = base[index];
      const z = base[index + 2];
      positions[index + 1] = base[index + 1]
        + Math.sin(elapsed * 0.9 + x * 0.08) * 1.6
        + Math.cos(elapsed * 0.65 + z * 0.12) * 1.1;
    }

    positionAttribute.needsUpdate = true;

    for (let i = 0; i < 120; i += 1) {
      const progress = i / 119;
      const x = (progress - 0.5) * 118;
      const z = Math.sin(progress * Math.PI * 2 + elapsed * 0.55) * 14;
      const y = Math.sin(progress * Math.PI * 7 + elapsed * 1.35) * 4 + 5;
      const index = i * 3;
      linePositions[index] = x;
      linePositions[index + 1] = y;
      linePositions[index + 2] = z;
    }

    lineGeometry.getAttribute("position").needsUpdate = true;
    points.rotation.y = Math.sin(elapsed * 0.08) * 0.12;
    line.rotation.y = points.rotation.y;
    renderer.render(scene, camera);
  });
}
