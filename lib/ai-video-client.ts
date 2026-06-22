export type RenderJobStatus = "queued" | "running" | "succeeded" | "failed" | string;

export interface RenderJobPublic {
  job_id: string;
  status: RenderJobStatus;
  progress: number;
  provider?: string;
  model?: string;
  video_url?: string | null;
  error?: string | null;
  created_at?: string;
  updated_at?: string;
}

export interface SavedRenderPreview {
  job_id: string;
  video_url: string;
  prompt: string;
  saved_at: string;
}

export const LAST_RENDER_STORAGE_KEY = "skymotion:lastRender";

function apiBase(): string {
  const configured = process.env.NEXT_PUBLIC_AI_VIDEO_API_URL?.trim();
  if (configured) {
    return configured.replace(/\/$/, "");
  }
  return "/api/ai-video";
}

function jobsUrl(jobId?: string): string {
  const base = `${apiBase()}/jobs.php`;
  return jobId ? `${base}?job_id=${encodeURIComponent(jobId)}` : base;
}

function parseJobPayload(payload: unknown): RenderJobPublic {
  if (!payload || typeof payload !== "object") {
    throw new Error("Invalid render API response");
  }

  const record = payload as Record<string, unknown>;
  if (record.detail && typeof record.detail === "object") {
    return parseJobPayload(record.detail);
  }
  if (record.job && typeof record.job === "object") {
    return parseJobPayload(record.job);
  }

  const jobId = String(record.job_id ?? "");
  if (!jobId) {
    const err = record.error;
    throw new Error(typeof err === "string" ? err : JSON.stringify(record));
  }

  return {
    job_id: jobId,
    status: String(record.status ?? "unknown"),
    progress: Number(record.progress ?? 0),
    provider: record.provider ? String(record.provider) : undefined,
    model: record.model ? String(record.model) : undefined,
    video_url: record.video_url ? String(record.video_url) : null,
    error: record.error ? String(record.error) : null,
    created_at: record.created_at ? String(record.created_at) : undefined,
    updated_at: record.updated_at ? String(record.updated_at) : undefined,
  };
}

export function loadSavedRender(): SavedRenderPreview | null {
  if (typeof window === "undefined") {
    return null;
  }
  try {
    const raw = window.localStorage.getItem(LAST_RENDER_STORAGE_KEY);
    if (!raw) {
      return null;
    }
    const parsed = JSON.parse(raw) as SavedRenderPreview;
    if (!parsed?.video_url || !parsed?.job_id) {
      return null;
    }
    return parsed;
  } catch {
    return null;
  }
}

export function saveRenderPreview(job: RenderJobPublic, prompt: string): void {
  if (typeof window === "undefined" || !job.video_url) {
    return;
  }
  const payload: SavedRenderPreview = {
    job_id: job.job_id,
    video_url: job.video_url,
    prompt,
    saved_at: new Date().toISOString(),
  };
  window.localStorage.setItem(LAST_RENDER_STORAGE_KEY, JSON.stringify(payload));
}

export async function createVideoJob(prompt: string): Promise<RenderJobPublic> {
  const response = await fetch(jobsUrl(), {
    method: "POST",
    credentials: "include",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ prompt, mode: "text_to_video" }),
  });

  const payload = await response.json();
  if (!response.ok) {
    const record = payload as Record<string, unknown>;
    const code = String(record.error ?? "");
    if (response.status === 401 || code === "auth_required") {
      throw new Error("Sign in required — create a MaskZero account to generate previews.");
    }
    if (response.status === 402 || code === "payment_required") {
      throw new Error("Payment required — subscribe or buy render credits to generate previews.");
    }
    const job = parseJobPayload(payload);
    throw new Error(job.error || `Render submit failed (${response.status})`);
  }

  return parseJobPayload(payload);
}

export async function fetchVideoJob(jobId: string): Promise<RenderJobPublic> {
  const response = await fetch(jobsUrl(jobId), { cache: "no-store", credentials: "include" });
  const payload = await response.json();
  if (!response.ok) {
    throw new Error(parseJobPayload(payload).error || `Render poll failed (${response.status})`);
  }
  return parseJobPayload(payload);
}

export async function submitAndPollVideoJob(
  prompt: string,
  onUpdate?: (job: RenderJobPublic) => void,
  maxAttempts = 72,
  intervalMs = 5000,
): Promise<RenderJobPublic> {
  let job = await createVideoJob(prompt);
  onUpdate?.(job);

  if (job.status === "succeeded" && job.video_url) {
    return job;
  }
  if (job.status === "failed") {
    throw new Error(job.error || "Render failed immediately");
  }

  for (let attempt = 0; attempt < maxAttempts; attempt += 1) {
    await new Promise((resolve) => window.setTimeout(resolve, intervalMs));
    job = await fetchVideoJob(job.job_id);
    onUpdate?.(job);

    if (job.status === "succeeded" && job.video_url) {
      return job;
    }
    if (job.status === "failed") {
      throw new Error(job.error || "Render failed");
    }
  }

  throw new Error("Render timed out while still processing");
}
