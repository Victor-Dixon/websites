"use client";

import { useEffect, useRef } from "react";
import * as THREE from "three";

export function DreamMotionStage() {
  const mountRef = useRef<HTMLDivElement | null>(null);

  useEffect(() => {
    const mount = mountRef.current;
    if (!mount) {
      return;
    }

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(48, 1, 0.1, 100);
    camera.position.set(0, 0.35, 5.8);

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(mount.clientWidth, mount.clientHeight);
    mount.appendChild(renderer.domElement);

    const group = new THREE.Group();
    scene.add(group);

    const core = new THREE.Mesh(
      new THREE.IcosahedronGeometry(1.05, 2),
      new THREE.MeshStandardMaterial({
        color: 0x38bdf8,
        emissive: 0x1d4ed8,
        emissiveIntensity: 0.55,
        metalness: 0.62,
        roughness: 0.18,
        transparent: true,
        opacity: 0.9,
      }),
    );
    group.add(core);

    const ringMaterial = new THREE.MeshBasicMaterial({
      color: 0xc084fc,
      transparent: true,
      opacity: 0.55,
      side: THREE.DoubleSide,
    });

    const rings = [1.55, 2.05, 2.55].map((radius, index) => {
      const ring = new THREE.Mesh(new THREE.TorusGeometry(radius, 0.012, 12, 140), ringMaterial.clone());
      ring.rotation.x = Math.PI / 2.8 + index * 0.35;
      ring.rotation.y = index * 0.42;
      group.add(ring);
      return ring;
    });

    const particleGeometry = new THREE.BufferGeometry();
    const particleCount = 360;
    const positions = new Float32Array(particleCount * 3);

    for (let index = 0; index < particleCount; index += 1) {
      const radius = 1.8 + Math.random() * 2.4;
      const theta = Math.random() * Math.PI * 2;
      const phi = Math.acos(2 * Math.random() - 1);
      positions[index * 3] = radius * Math.sin(phi) * Math.cos(theta);
      positions[index * 3 + 1] = radius * Math.sin(phi) * Math.sin(theta);
      positions[index * 3 + 2] = radius * Math.cos(phi);
    }

    particleGeometry.setAttribute("position", new THREE.BufferAttribute(positions, 3));

    const particles = new THREE.Points(
      particleGeometry,
      new THREE.PointsMaterial({
        color: 0xe0f2fe,
        size: 0.025,
        transparent: true,
        opacity: 0.75,
      }),
    );
    scene.add(particles);

    scene.add(new THREE.AmbientLight(0x7dd3fc, 1.6));

    const cyanLight = new THREE.PointLight(0x22d3ee, 26, 12);
    cyanLight.position.set(2.5, 2.2, 3.5);
    scene.add(cyanLight);

    const violetLight = new THREE.PointLight(0xa855f7, 22, 12);
    violetLight.position.set(-3, -1.4, 2.8);
    scene.add(violetLight);

    const resize = () => {
      const width = mount.clientWidth;
      const height = mount.clientHeight;
      camera.aspect = width / height;
      camera.updateProjectionMatrix();
      renderer.setSize(width, height);
    };

    resize();
    window.addEventListener("resize", resize);

    let frameId = 0;
    const animate = () => {
      frameId = window.requestAnimationFrame(animate);
      const time = performance.now() * 0.001;

      group.rotation.y = time * 0.22;
      group.rotation.x = Math.sin(time * 0.42) * 0.16;
      core.scale.setScalar(1 + Math.sin(time * 1.8) * 0.035);
      particles.rotation.y = -time * 0.04;
      particles.rotation.x = time * 0.025;

      rings.forEach((ring, index) => {
        ring.rotation.z = time * (0.18 + index * 0.045);
      });

      renderer.render(scene, camera);
    };

    animate();

    return () => {
      window.cancelAnimationFrame(frameId);
      window.removeEventListener("resize", resize);
      renderer.dispose();
      particleGeometry.dispose();
      core.geometry.dispose();
      core.material.dispose();
      rings.forEach((ring) => {
        ring.geometry.dispose();
        if (Array.isArray(ring.material)) {
          ring.material.forEach((material) => material.dispose());
        } else {
          ring.material.dispose();
        }
      });
      mount.removeChild(renderer.domElement);
    };
  }, []);

  return (
    <div
      ref={mountRef}
      aria-label="Three.js Spark Motion holographic animation preview"
      className="absolute inset-0"
      role="img"
    />
  );
}
