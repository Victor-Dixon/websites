#!/usr/bin/env node
/**
 * Headless logic tests (no canvas/DOM).
 */
import { buildAnimationsFromGrid, resolveAnimationKey, buildAtlasJson } from '../js/atlas-builder.js';

let passed = 0;
let failed = 0;

function assert(cond, msg) {
  if (cond) { passed++; return; }
  failed++;
  console.error('FAIL:', msg);
}

const anims = buildAnimationsFromGrid();
assert(Object.keys(anims).length === 16, 'expected 16 animations');
assert(anims.walk_down.row === 2, 'walk_down row');
assert(anims.walk_down.length === 4, 'walk_down frames');
assert(anims.attack.loop === false, 'attack non-looping');
assert(resolveAnimationKey('walking', 'left') === 'walk_left', 'resolve walking');
assert(resolveAnimationKey('running', 'up') === 'run_up', 'resolve running');

const atlas = buildAtlasJson({ name: 't', texture: 'x.png' });
assert(atlas.asset.gridSize.w === 64, 'default grid w');
assert(atlas.animations.run_right.fps === 12, 'run fps');

// Frame slice math (sx/sy)
const clip = anims.walk_left;
const gridW = 64;
const frameIndex = 2;
const sx = (clip.startFrame + frameIndex) * gridW;
const sy = clip.row * gridW;
assert(sx === 128 && sy === 192, `slice coords sx=${sx} sy=${sy}`);

console.log(`\n${passed} passed, ${failed} failed`);
process.exit(failed > 0 ? 1 : 0);
