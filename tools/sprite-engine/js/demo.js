import { SpriteEngine } from './SpriteEngine.js';
import { buildAtlasJson } from './atlas-builder.js';
import { generateDemoSpriteSheet } from './demo-sprite.js';

const canvas = document.getElementById('sprite-canvas');
const hud = document.getElementById('hud');
const stateButtons = document.getElementById('state-buttons');
const dirPad = document.getElementById('dir-pad');

const useProcedural = new URLSearchParams(location.search).get('procedural') === '1';

let engine;
let currentState = 'idle';
let currentDir = 'down';

function updateHud(info) {
  hud.innerHTML = [
    `<strong>asset</strong> ${useProcedural ? 'procedural demo' : 'Dream Explorer'} &nbsp;`,
    `<strong>state</strong> ${info.action} &nbsp;`,
    `<strong>dir</strong> ${info.direction} &nbsp;`,
    `<strong>clip</strong> ${info.animationKey} &nbsp;`,
    `<strong>frame</strong> ${info.frameIndex}/${Math.max(0, info.clipLength - 1)} &nbsp;`,
    `<strong>anim</strong> ${info.clipFps} fps &nbsp;`,
    `<strong>render</strong> ${info.renderFps} fps`,
    info.oneShot ? ` &nbsp; <strong>one-shot</strong> ${info.oneShot}` : '',
  ].join('');
}

function setActiveButtons() {
  stateButtons.querySelectorAll('[data-state]').forEach((btn) => {
    btn.classList.toggle('active', btn.dataset.state === currentState);
  });
  dirPad.querySelectorAll('[data-dir]').forEach((btn) => {
    btn.classList.toggle('active', btn.dataset.dir === currentDir);
  });
}

function applyState() {
  if (!engine) return;
  engine.setState(currentState, currentDir);
  setActiveButtons();
}

function bindControls() {
  stateButtons.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-state], [data-one-shot]');
    if (!btn) return;
    if (btn.dataset.oneShot) {
      engine.play(btn.dataset.oneShot);
      return;
    }
    currentState = btn.dataset.state;
    applyState();
  });

  dirPad.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-dir]');
    if (!btn) return;
    currentDir = btn.dataset.dir;
    applyState();
  });

  const keyMap = {
    KeyW: 'up', ArrowUp: 'up',
    KeyS: 'down', ArrowDown: 'down',
    KeyA: 'left', ArrowLeft: 'left',
    KeyD: 'right', ArrowRight: 'right',
  };

  window.addEventListener('keydown', (e) => {
    if (e.repeat) return;
    const dir = keyMap[e.code];
    if (dir) {
      e.preventDefault();
      currentDir = dir;
      if (currentState === 'idle') currentState = 'walking';
      applyState();
      return;
    }
    if (e.code === 'ShiftLeft' || e.code === 'ShiftRight') {
      currentState = 'running';
      applyState();
      return;
    }
    if (e.code === 'Space') {
      e.preventDefault();
      engine.play('attack');
    }
  });

  window.addEventListener('keyup', (e) => {
    if (e.code === 'ShiftLeft' || e.code === 'ShiftRight') {
      currentState = 'walking';
      applyState();
    }
  });
}

async function loadDreamExplorer() {
  engine = new SpriteEngine(canvas, { scale: 2, background: '#161b22' });
  await engine.loadAtlas('./assets/dream-explorer-atlas.json');
  return engine;
}

async function loadProceduralDemo() {
  const image = await generateDemoSpriteSheet();
  const atlas = buildAtlasJson({
    name: 'demo-character',
    texture: 'inline-demo',
    gridW: 64,
    gridH: 64,
  });
  engine = new SpriteEngine(canvas, { scale: 2, background: '#161b22' });
  engine.loadAtlasWithImage(atlas, image);
  return engine;
}

async function init() {
  if (useProcedural) {
    await loadProceduralDemo();
  } else {
    try {
      await loadDreamExplorer();
    } catch (err) {
      console.warn('Dream Explorer load failed, falling back to procedural demo:', err);
      await loadProceduralDemo();
    }
  }

  engine.onStateChange(updateHud);
  engine.setState('idle', 'down');
  engine.start();

  bindControls();
  setInterval(() => updateHud(engine.getDebugInfo()), 500);
}

init().catch((err) => {
  hud.textContent = `Error: ${err.message}`;
  console.error(err);
});
