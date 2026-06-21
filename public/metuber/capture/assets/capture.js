const filters = [
  {
    id: "cartoon",
    label: "Cartoon",
    title: "MeTuber Cartoon Mode",
    thumb: "linear-gradient(135deg, #ff4ecd, #ffe66d 52%, #37d9ff)"
  },
  {
    id: "anime",
    label: "Anime",
    title: "MeTuber Anime Pop",
    thumb: "linear-gradient(135deg, #ff8bd1, #8be9ff)"
  },
  {
    id: "sparkle",
    label: "Sparkle",
    title: "MeTuber Sparkle Burst",
    thumb: "radial-gradient(circle at 35% 30%, #fff 0 8%, transparent 9%), linear-gradient(135deg, #ffe66d, #ff4ecd)"
  },
  {
    id: "comic",
    label: "Comic",
    title: "MeTuber Comic Ink",
    thumb: "repeating-linear-gradient(135deg, #ffe66d 0 8px, #ff6b6b 8px 16px, #171721 16px 24px)"
  },
  {
    id: "glow",
    label: "Glow",
    title: "MeTuber Neon Glow",
    thumb: "linear-gradient(135deg, #37d9ff, #7c5cff, #8cffb7)"
  },
  {
    id: "monster",
    label: "Monster",
    title: "MeTuber Monster Mask",
    thumb: "linear-gradient(135deg, #8cffb7, #5bff5b 46%, #6c2bd9)"
  },
  {
    id: "dream",
    label: "Dream",
    title: "MeTuber Dream Cloud",
    thumb: "linear-gradient(135deg, #b8a7ff, #ffd6f5, #9fe8ff)"
  },
  {
    id: "baller",
    label: "Baller",
    title: "MeTuber Baller Flash",
    thumb: "linear-gradient(135deg, #ff9f1c, #ff4ecd 54%, #37d9ff)"
  }
];

const filterEffects = {
  cartoon: "saturate(1.5) contrast(1.16) brightness(1.06)",
  anime: "saturate(1.85) contrast(1.18) brightness(1.12) hue-rotate(8deg)",
  sparkle: "saturate(1.7) contrast(1.1) brightness(1.22)",
  comic: "grayscale(.08) saturate(1.4) contrast(1.42) brightness(1.02)",
  glow: "saturate(1.55) brightness(1.18)",
  monster: "saturate(1.75) contrast(1.2) hue-rotate(92deg) brightness(.96)",
  dream: "saturate(1.25) contrast(.96) brightness(1.14) hue-rotate(28deg) blur(.2px)",
  baller: "saturate(1.8) contrast(1.18) brightness(1.08) hue-rotate(-16deg)"
};

const state = {
  activeFilter: filters[0],
  facingMode: "user",
  stream: null,
  capturedDataUrl: "",
  toastTimer: 0,
  debugEnabled: new URLSearchParams(window.location.search).has("debug")
};

const root = document.querySelector("[data-metuber-capture]");
const video = document.querySelector("[data-camera-preview]");
const photo = document.querySelector("[data-capture-photo]");
const canvas = document.querySelector("[data-capture-canvas]");
const permissionOverlay = document.querySelector("[data-permission-overlay]");
const permissionNote = document.querySelector("[data-permission-note]");
const enableCameraButton = document.querySelector("[data-enable-camera]");
const captureButton = document.querySelector("[data-capture-button]");
const saveButton = document.querySelector("[data-save-capture]");
const postButton = document.querySelector("[data-post-button]");
const closeButton = document.querySelector("[data-close-capture]");
const modeTitle = document.querySelector("[data-mode-title]");
const carousel = document.querySelector("[data-filter-carousel]");
const moreButton = document.querySelector("[data-more-tools]");
const toolSheet = document.querySelector("[data-tool-sheet]");
const flipButton = document.querySelector("[data-flip-camera]");
const restartButton = document.querySelector("[data-restart-camera]");
const debugToggle = document.querySelector("[data-debug-toggle]");
const debugPanel = document.querySelector("[data-debug-panel]");
const debugStatus = document.querySelector("[data-debug-status]");
const debugStart = document.querySelector("[data-debug-start]");
const debugStop = document.querySelector("[data-debug-stop]");
const debugFlip = document.querySelector("[data-debug-flip]");
const toast = document.querySelector("[data-toast]");

function renderFilters() {
  carousel.innerHTML = filters.map((filter) => `
    <button class="filter-chip${filter.id === state.activeFilter.id ? " is-active" : ""}" type="button" data-filter-id="${filter.id}">
      <span class="filter-thumb" style="--thumb: ${filter.thumb}"></span>
      <span>${filter.label}</span>
    </button>
  `).join("");
}

function setActiveFilter(filterId) {
  const nextFilter = filters.find((filter) => filter.id === filterId) || filters[0];
  state.activeFilter = nextFilter;
  modeTitle.textContent = nextFilter.title;
  updateFilterClass(video);
  updateFilterClass(photo);
  renderFilters();
  dispatchPipelineEvent("filterchange", { filter: nextFilter });
}

function updateFilterClass(element) {
  element.className = element.className
    .split(/\s+/)
    .filter((className) => className && !className.startsWith("filter-"))
    .concat(`filter-${state.activeFilter.id}`)
    .join(" ");
}

async function startCamera() {
  if (!navigator.mediaDevices?.getUserMedia) {
    showPermission("This browser does not support camera capture.");
    updateDebugStatus("Camera unsupported");
    return;
  }

  stopCamera();
  updateDebugStatus("Requesting camera access...");

  try {
    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: state.facingMode,
        width: { ideal: 1080 },
        height: { ideal: 1920 }
      },
      audio: false
    });

    state.stream = stream;
    video.srcObject = stream;
    await video.play();
    photo.classList.add("is-hidden");
    permissionOverlay.classList.remove("is-visible");
    updateDebugStatus(`Camera live - ${state.facingMode} - ${state.activeFilter.label}`);
    dispatchPipelineEvent("camerastart", { facingMode: state.facingMode });
  } catch (error) {
    const message = cameraErrorMessage(error);
    showPermission(message);
    updateDebugStatus(message);
  }
}

function stopCamera() {
  if (!state.stream) {
    return;
  }

  state.stream.getTracks().forEach((track) => track.stop());
  state.stream = null;
  video.srcObject = null;
  updateDebugStatus("Camera stopped");
  dispatchPipelineEvent("camerastop", {});
}

async function flipCamera() {
  state.facingMode = state.facingMode === "user" ? "environment" : "user";
  showToast(`Switched to ${state.facingMode === "user" ? "selfie" : "back"} camera`);
  await startCamera();
}

function showPermission(message) {
  permissionOverlay.classList.add("is-visible");
  permissionNote.textContent = message;
}

function cameraErrorMessage(error) {
  if (error?.name === "NotAllowedError" || error?.name === "SecurityError") {
    return "Camera permission was blocked. Enable camera access in your browser settings to keep creating.";
  }

  if (error?.name === "NotFoundError" || error?.name === "OverconstrainedError") {
    return "No matching camera was found on this device.";
  }

  return "Camera could not start. Check permission and try again.";
}

function captureFrame() {
  if (!state.stream || video.readyState < HTMLMediaElement.HAVE_CURRENT_DATA) {
    startCamera();
    showToast("Starting camera...");
    return;
  }

  const rect = root.getBoundingClientRect();
  const width = Math.max(320, Math.round(rect.width * window.devicePixelRatio));
  const height = Math.max(568, Math.round(rect.height * window.devicePixelRatio));
  canvas.width = width;
  canvas.height = height;

  const context = canvas.getContext("2d");
  const crop = objectCoverCrop(video.videoWidth, video.videoHeight, width, height);
  context.save();
  context.filter = filterEffects[state.activeFilter.id] || filterEffects.cartoon;
  context.drawImage(video, crop.x, crop.y, crop.width, crop.height, 0, 0, width, height);
  context.restore();

  drawMeTuberBranding(context, width, height);

  state.capturedDataUrl = canvas.toDataURL("image/png");
  photo.src = state.capturedDataUrl;
  photo.classList.remove("is-hidden");
  updateFilterClass(photo);
  updateDebugStatus(`Captured - ${state.activeFilter.label}`);
  showToast(`${state.activeFilter.label} snap captured`);
  dispatchPipelineEvent("capture", {
    filter: state.activeFilter,
    canvas,
    dataUrl: state.capturedDataUrl
  });
}

function objectCoverCrop(sourceWidth, sourceHeight, targetWidth, targetHeight) {
  const sourceRatio = sourceWidth / sourceHeight;
  const targetRatio = targetWidth / targetHeight;

  if (sourceRatio > targetRatio) {
    const width = sourceHeight * targetRatio;
    return {
      x: (sourceWidth - width) / 2,
      y: 0,
      width,
      height: sourceHeight
    };
  }

  const height = sourceWidth / targetRatio;
  return {
    x: 0,
    y: (sourceHeight - height) / 2,
    width: sourceWidth,
    height
  };
}

function drawMeTuberBranding(context, width, height) {
  const scale = Math.max(1, width / 430);
  context.save();
  context.globalAlpha = .94;
  context.fillStyle = "rgba(0, 0, 0, .42)";
  roundRect(context, 22 * scale, 22 * scale, 174 * scale, 42 * scale, 21 * scale);
  context.fill();
  context.font = `${15 * scale}px system-ui, sans-serif`;
  context.fillStyle = "#ffffff";
  context.fillText("MeTuber Cartoon", 42 * scale, 49 * scale);
  context.restore();
}

function roundRect(context, x, y, width, height, radius) {
  context.beginPath();
  context.moveTo(x + radius, y);
  context.arcTo(x + width, y, x + width, y + height, radius);
  context.arcTo(x + width, y + height, x, y + height, radius);
  context.arcTo(x, y + height, x, y, radius);
  context.arcTo(x, y, x + width, y, radius);
  context.closePath();
}

function saveCapture() {
  if (!state.capturedDataUrl) {
    captureFrame();
  }

  if (!state.capturedDataUrl) {
    return;
  }

  const anchor = document.createElement("a");
  anchor.href = state.capturedDataUrl;
  anchor.download = `metuber-${state.activeFilter.id}-${Date.now()}.png`;
  anchor.click();
  showToast("Saved MeTuber frame");
}

function postCapture() {
  const detail = {
    filter: state.activeFilter,
    dataUrl: state.capturedDataUrl || null
  };

  dispatchPipelineEvent("post", detail);
  showToast(state.capturedDataUrl ? "Ready to post" : "Capture a snap first");
}

function dispatchPipelineEvent(name, detail) {
  window.dispatchEvent(new CustomEvent(`metuber:${name}`, { detail }));

  if (name === "capture" && typeof window.MeTuberAI?.processFrame === "function") {
    window.MeTuberAI.processFrame(detail);
  }
}

function toggleToolSheet(force) {
  const shouldOpen = typeof force === "boolean" ? force : toolSheet.hidden;
  toolSheet.hidden = !shouldOpen;
}

function toggleDebug(force, options = {}) {
  state.debugEnabled = typeof force === "boolean" ? force : !state.debugEnabled;
  debugPanel.hidden = !state.debugEnabled;
  if (!options.silent) {
    showToast(state.debugEnabled ? "Debug controls enabled" : "Debug controls hidden");
  }
}

function updateDebugStatus(message) {
  debugStatus.textContent = message;
}

function showToast(message) {
  toast.textContent = message;
  toast.classList.add("is-visible");
  clearTimeout(state.toastTimer);
  state.toastTimer = window.setTimeout(() => {
    toast.classList.remove("is-visible");
  }, 1800);
}

function closeCapture() {
  stopCamera();

  if (window.history.length > 1) {
    window.history.back();
    return;
  }

  window.location.href = "/";
}

function bindEvents() {
  enableCameraButton.addEventListener("click", startCamera);
  captureButton.addEventListener("click", captureFrame);
  saveButton.addEventListener("click", saveCapture);
  postButton.addEventListener("click", postCapture);
  closeButton.addEventListener("click", closeCapture);
  moreButton.addEventListener("click", () => toggleToolSheet());
  flipButton.addEventListener("click", () => {
    toggleToolSheet(false);
    flipCamera();
  });
  restartButton.addEventListener("click", () => {
    toggleToolSheet(false);
    startCamera();
  });
  debugToggle.addEventListener("click", () => {
    toggleToolSheet(false);
    toggleDebug();
  });
  debugStart.addEventListener("click", startCamera);
  debugStop.addEventListener("click", stopCamera);
  debugFlip.addEventListener("click", flipCamera);

  carousel.addEventListener("click", (event) => {
    const chip = event.target.closest("[data-filter-id]");
    if (!chip) {
      return;
    }

    setActiveFilter(chip.dataset.filterId);
    showToast(`${state.activeFilter.label} selected`);
  });

  document.addEventListener("click", (event) => {
    if (toolSheet.hidden || toolSheet.contains(event.target) || moreButton.contains(event.target)) {
      return;
    }

    toggleToolSheet(false);
  });
}

function init() {
  renderFilters();
  setActiveFilter(state.activeFilter.id);
  toggleDebug(state.debugEnabled, { silent: true });
  bindEvents();
  startCamera();
}

init();
