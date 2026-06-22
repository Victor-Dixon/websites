import type { AnimationDuration, AnimationStyle } from "@/lib/prompt-engine";

export type RenderSource = "text" | "image" | "video" | "dreammotion";
export type RenderStatus = "queued" | "storyboarding" | "rendering" | "ready";

export interface RenderJobRequest {
  prompt: string;
  style: AnimationStyle;
  duration: AnimationDuration;
  source: RenderSource;
  controls: {
    cameraZoom: number;
    cameraPan: number;
    slowMotion: number;
    characterMotion: number;
    backgroundMotion: number;
    particles: number;
    lighting: number;
  };
}

export interface RenderJob {
  id: string;
  status: RenderStatus;
  progress: number;
  eta: string;
  storagePath: string;
  request: RenderJobRequest;
}

export function createRenderJob(request: RenderJobRequest): RenderJob {
  const jobId = `skm_${crypto.randomUUID().slice(0, 8)}`;

  return {
    id: jobId,
    status: "queued",
    progress: 12,
    eta: request.duration === "60s" ? "high-priority queue" : "fast render lane",
    storagePath: `renders/${jobId}/master.mp4`,
    request,
  };
}
