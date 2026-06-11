#!/usr/bin/env node
/**
 * Validate sprite atlas JSON against Grid Schema V1 expectations.
 * Usage: node scripts/validate-atlas.js [path/to/atlas.json]
 */

import { readFileSync } from 'node:fs';
import { resolve, dirname } from 'node:path';
import { fileURLToPath } from 'node:url';
import { buildAnimationsFromGrid } from '../js/atlas-builder.js';

const __dirname = dirname(fileURLToPath(import.meta.url));
const defaultPath = resolve(__dirname, '../assets/demo-atlas.json');

const REQUIRED_ANIMATIONS = Object.keys(buildAnimationsFromGrid());

function validate(data, label) {
  const errors = [];
  const warnings = [];

  if (!data || typeof data !== 'object') {
    return { ok: false, errors: ['Root must be an object'], warnings };
  }

  const asset = data.asset;
  if (!asset) errors.push('Missing "asset" object');
  else {
    if (!asset.name) warnings.push('asset.name is empty');
    if (!asset.texture) errors.push('asset.texture is required');
    if (!asset.gridSize?.w || !asset.gridSize?.h) {
      errors.push('asset.gridSize.w and asset.gridSize.h are required');
    }
  }

  const anims = data.animations;
  if (!anims || typeof anims !== 'object') {
    errors.push('Missing "animations" object');
    return { ok: false, errors, warnings };
  }

  for (const key of REQUIRED_ANIMATIONS) {
    if (!anims[key]) warnings.push(`Missing recommended animation: ${key}`);
  }

  for (const [key, clip] of Object.entries(anims)) {
    if (typeof clip.row !== 'number') errors.push(`${key}: row must be a number`);
    if (typeof clip.startFrame !== 'number') errors.push(`${key}: startFrame must be a number`);
    if (typeof clip.length !== 'number' || clip.length < 1) {
      errors.push(`${key}: length must be >= 1`);
    }
    if (typeof clip.loop !== 'boolean') errors.push(`${key}: loop must be boolean`);
    if (clip.fps != null && (clip.fps < 1 || clip.fps > 60)) {
      warnings.push(`${key}: fps ${clip.fps} outside typical 8–12 range`);
    }
    if (asset?.gridSize && clip.startFrame + clip.length > 4 && key.startsWith('idle_')) {
      warnings.push(`${key}: idle clips usually use 1 frame per direction on row 0`);
    }
  }

  return { ok: errors.length === 0, errors, warnings };
}

const target = process.argv[2] ? resolve(process.argv[2]) : defaultPath;

let raw;
try {
  raw = readFileSync(target, 'utf8');
} catch (e) {
  if (target === defaultPath) {
    console.log('No default assets/demo-atlas.json — validating schema reference only.');
    const ref = { asset: { name: 'ref', texture: 'x.png', gridSize: { w: 64, h: 64 } }, animations: buildAnimationsFromGrid() };
    const result = validate(ref, 'reference');
    console.log(result.ok ? '✓ Reference schema valid' : '✗ Reference schema invalid');
    result.errors.forEach((e) => console.error('  ERROR:', e));
    process.exit(result.ok ? 0 : 1);
  }
  console.error(`Cannot read ${target}:`, e.message);
  process.exit(1);
}

const data = JSON.parse(raw);
const result = validate(data, target);

console.log(`Validating: ${target}`);
if (result.ok) console.log('✓ Atlas valid');
else console.log('✗ Atlas invalid');

result.errors.forEach((e) => console.error('  ERROR:', e));
result.warnings.forEach((w) => console.warn('  WARN:', w));

process.exit(result.ok ? 0 : 1);
