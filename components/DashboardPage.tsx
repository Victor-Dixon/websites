"use client";

import Link from "next/link";
import { type FormEvent, type ReactNode, useEffect, useMemo, useState } from "react";
import {
  freeLimits,
  maskApiKey,
  premiumLimits,
  readLocalSession,
  readStoredApiKey,
  type AccountPlan,
  type SkyMotionSession,
  type StoredApiKey,
  writeLocalSession,
  writeStoredApiKey,
} from "@/lib/account";
import { type AnimationDuration, type AnimationStyle, type StoryScene } from "@/lib/prompt-engine";
import { type RenderJob, type RenderJobRequest, type RenderSource } from "@/lib/render-queue";
import { createSkyMotionSupabaseClient } from "@/lib/supabase";

type ControlState = RenderJobRequest["controls"];

const styles: AnimationStyle[] = ["cinematic", "anime", "cartoon", "realistic", "fantasy", "3d"];
const durations: AnimationDuration[] = ["5s", "10s", "30s", "60s"];
const sources: RenderSource[] = ["text", "image", "video", "dreammotion"];

const defaultControls: ControlState = {
  cameraZoom: 62,
  cameraPan: 48,
  slowMotion: 28,
  characterMotion: 74,
  backgroundMotion: 66,
  particles: 78,
  lighting: 88,
};

const providerOptions = ["Runway", "Pika", "Kling", "OpenAI", "Replicate", "Custom"];

async function postJson<TResponse>(url: string, payload: unknown, headers?: HeadersInit): Promise<TResponse> {
  const response = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      ...headers,
    },
    body: JSON.stringify(payload),
  });
  const data = (await response.json()) as TResponse & { error?: string };

  if (!response.ok) {
    throw new Error(data.error ?? `Request failed with status ${response.status}`);
  }

  return data;
}

export function DashboardPage() {
  const supabase = useMemo(() => createSkyMotionSupabaseClient(), []);
  const [session, setSession] = useState<SkyMotionSession | null>(null);
  const [storedKey, setStoredKey] = useState<StoredApiKey | null>(null);
  const [isCheckingSession, setIsCheckingSession] = useState(true);
  const [isSavingPlan, setIsSavingPlan] = useState(false);
  const [provider, setProvider] = useState(providerOptions[0]);
  const [apiKey, setApiKey] = useState("");
  const [prompt, setPrompt] = useState("A cinematic anime hero discovering a city of floating glass islands at sunrise.");
  const [storyIdea, setStoryIdea] = useState("a musician finds an instrument that turns memories into animated worlds");
  const [style, setStyle] = useState<AnimationStyle>("cinematic");
  const [duration, setDuration] = useState<AnimationDuration>("10s");
  const [source, setSource] = useState<RenderSource>("text");
  const [controls, setControls] = useState<ControlState>(defaultControls);
  const [enhancedPrompt, setEnhancedPrompt] = useState("Enhance a prompt to see AI director notes here.");
  const [storyScenes, setStoryScenes] = useState<StoryScene[]>([]);
  const [jobs, setJobs] = useState<RenderJob[]>([]);
  const [status, setStatus] = useState("Ready. Choose a plan path, then create a render.");
  const [isEnhancing, setIsEnhancing] = useState(false);
  const [isStoryboarding, setIsStoryboarding] = useState(false);
  const [isRendering, setIsRendering] = useState(false);

  useEffect(() => {
    async function loadSession() {
      try {
        if (supabase) {
          const { data } = await supabase.auth.getSession();
          const user = data.session?.user;
          if (user?.email) {
            const userPlan = user.user_metadata?.plan === "premium" ? "premium" : "free";
            setSession({
              email: user.email,
              plan: userPlan,
              source: "supabase",
              signedInAt: data.session?.expires_at ? new Date().toISOString() : new Date().toISOString(),
            });
          } else {
            setSession(readLocalSession());
          }
        } else {
          setSession(readLocalSession());
        }
        setStoredKey(readStoredApiKey());
      } finally {
        setIsCheckingSession(false);
      }
    }

    void loadSession();
  }, [supabase]);

  async function savePlan(plan: AccountPlan) {
    if (!session) {
      return;
    }

    setIsSavingPlan(true);
    setStatus(`Switching account to ${plan} mode...`);
    const nextSession: SkyMotionSession = { ...session, plan };
    try {
      if (supabase && session.source === "supabase") {
        const { error } = await supabase.auth.updateUser({
          data: {
            plan,
          },
        });
        if (error) {
          throw error;
        }
      } else {
        writeLocalSession(nextSession);
      }
      setSession(nextSession);
      setStatus(
        plan === "free"
          ? "Free mode active. Add your provider API key before rendering."
          : `Premium mode active. Managed renders are capped at ${premiumLimits.hourly}/hour and ${premiumLimits.daily}/day.`,
      );
    } catch (error) {
      setStatus(error instanceof Error ? error.message : "Unable to update plan.");
    } finally {
      setIsSavingPlan(false);
    }
  }

  function saveApiKey(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const cleanKey = apiKey.trim();

    if (cleanKey.length < 8) {
      setStatus("Enter a valid provider API key before saving.");
      return;
    }

    const nextKey: StoredApiKey = {
      provider,
      key: cleanKey,
      savedAt: new Date().toISOString(),
    };
    writeStoredApiKey(nextKey);
    setStoredKey(nextKey);
    setApiKey("");
    setStatus(`${provider} key saved for Free BYO-key renders in this browser.`);
  }

  function removeApiKey() {
    window.localStorage.removeItem("skymotion.freeApiKey");
    setStoredKey(null);
    setStatus("Free account provider key removed from this browser.");
  }

  async function enhanceCurrentPrompt() {
    setIsEnhancing(true);
    setStatus("Enhancing prompt...");
    try {
      const data = await postJson<{ enhancedPrompt: string }>("/api/prompt/enhance", { prompt, style, duration });
      setEnhancedPrompt(data.enhancedPrompt);
      setStatus("Prompt enhanced and ready for render queue.");
    } catch (error) {
      setStatus(error instanceof Error ? `Enhance failed: ${error.message}` : "Enhance failed.");
    } finally {
      setIsEnhancing(false);
    }
  }

  async function generateScenes() {
    setIsStoryboarding(true);
    setSource("dreammotion");
    setStatus("Generating storyboard scenes...");
    try {
      const data = await postJson<{ title: string; scenes: StoryScene[] }>("/api/story", { idea: storyIdea });
      setStoryScenes(data.scenes);
      setEnhancedPrompt(data.scenes[0]?.prompt ?? enhancedPrompt);
      setStatus("Storyboard generated. Queue a render when ready.");
    } catch (error) {
      setStatus(error instanceof Error ? `Story generation failed: ${error.message}` : "Story generation failed.");
    } finally {
      setIsStoryboarding(false);
    }
  }

  async function queueRender() {
    if (!session) {
      setStatus("Log in before queuing a render.");
      return;
    }

    if (session.plan === "free" && !storedKey) {
      setStatus("Free accounts must add their own provider API key before rendering.");
      return;
    }

    setIsRendering(true);
    setStatus("Submitting render job...");
    try {
      const request: RenderJobRequest = {
        prompt: enhancedPrompt === "Enhance a prompt to see AI director notes here." ? prompt : enhancedPrompt,
        style,
        duration,
        source,
        controls,
      };
      const data = await postJson<{ job: RenderJob; quota: { plan: AccountPlan; remaining: number; reset: string } }>(
        "/api/render/jobs",
        request,
        {
          "x-skymotion-account-id": session.email,
          "x-skymotion-plan": session.plan,
          ...(session.plan === "free" && storedKey
            ? {
                "x-skymotion-user-api-key": storedKey.key,
                "x-skymotion-provider": storedKey.provider,
              }
            : {}),
        },
      );
      setJobs((current) => [data.job, ...current].slice(0, 5));
      setStatus(`Render queued. ${data.quota.remaining} managed ${data.quota.plan} slots remain until ${data.quota.reset}.`);
    } catch (error) {
      setStatus(error instanceof Error ? `Render failed: ${error.message}` : "Render failed.");
    } finally {
      setIsRendering(false);
    }
  }

  if (isCheckingSession) {
    return (
      <DashboardShell>
        <section className="mx-auto flex min-h-[70vh] max-w-3xl items-center justify-center">
          <div className="glass-panel rounded-[2rem] p-8 text-center">
            <span className="loading-spinner mx-auto block" />
            <p className="mt-4 text-slate-300">Checking your SkyMotion account...</p>
          </div>
        </section>
      </DashboardShell>
    );
  }

  if (!session) {
    return (
      <DashboardShell>
        <section className="mx-auto flex min-h-[70vh] max-w-3xl items-center justify-center">
          <div className="glass-panel rounded-[2rem] p-8 text-center">
            <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Account required</p>
            <h1 className="mt-3 text-4xl font-black text-white">Log in before creating.</h1>
            <p className="mt-4 leading-7 text-slate-300">
              SkyMotion generation is gated behind accounts so usage can be tied to BYO keys or premium rate limits.
            </p>
            <Link
              href="/login/"
              className="mt-6 inline-flex rounded-2xl bg-cyan-300 px-6 py-3 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200"
            >
              Log In / Create Account
            </Link>
          </div>
        </section>
      </DashboardShell>
    );
  }

  return (
    <DashboardShell email={session.email}>
      <section className="mx-auto max-w-7xl py-8">
        <div className="mb-6 grid gap-4 lg:grid-cols-[1.2fr_0.8fr_0.8fr]">
          <div className="glass-panel rounded-[2rem] p-6">
            <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Dashboard</p>
            <h1 className="mt-3 text-4xl font-black text-white">Create gated SkyMotion renders.</h1>
            <p className="mt-3 leading-7 text-slate-300">
              Signed in as {session.email}. Free usage requires your own provider key; premium usage uses managed
              SkyMotion capacity with profitability-safe rate limits.
            </p>
          </div>
          <PlanCard
            title="Free BYO key"
            active={session.plan === "free"}
            detail={`${freeLimits.daily}/day using your provider billing.`}
            disabled={isSavingPlan}
            onClick={() => void savePlan("free")}
          />
          <PlanCard
            title="Premium managed"
            active={session.plan === "premium"}
            detail={`${premiumLimits.hourly}/hour and ${premiumLimits.daily}/day on SkyMotion capacity.`}
            disabled={isSavingPlan}
            onClick={() => void savePlan("premium")}
          />
        </div>

        <div className="grid gap-5 lg:grid-cols-[0.8fr_1.2fr]">
          <div className="grid gap-5">
            <form onSubmit={saveApiKey} className="glass-panel rounded-[2rem] p-5">
              <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Free account API key</p>
              <h2 className="mt-2 text-2xl font-black text-white">Bring your own provider.</h2>
              <p className="mt-2 text-sm leading-6 text-slate-300">
                Free keys are sent only with render requests and kept in this browser for the current account.
              </p>
              <label htmlFor="provider" className="mt-4 block text-sm font-bold text-cyan-100">
                Provider
              </label>
              <select
                id="provider"
                value={provider}
                onChange={(event) => setProvider(event.target.value)}
                className="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950 p-3 text-white outline-none"
              >
                {providerOptions.map((option) => (
                  <option key={option}>{option}</option>
                ))}
              </select>
              <label htmlFor="api-key" className="mt-4 block text-sm font-bold text-cyan-100">
                API key
              </label>
              <input
                id="api-key"
                type="password"
                value={apiKey}
                onChange={(event) => setApiKey(event.target.value)}
                placeholder="sk-..."
                className="mt-2 w-full rounded-2xl border border-cyan-300/20 bg-slate-950/70 p-4 text-white outline-none ring-cyan-300/40 transition placeholder:text-slate-500 focus:ring-4"
              />
              <div className="mt-4 flex flex-col gap-3 sm:flex-row">
                <button
                  type="submit"
                  className="rounded-2xl bg-cyan-300 px-5 py-3 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200"
                >
                  Save API Key
                </button>
                <button
                  type="button"
                  onClick={removeApiKey}
                  className="rounded-2xl border border-white/15 bg-white/5 px-5 py-3 font-black text-white transition hover:-translate-y-1 hover:bg-white/10"
                >
                  Remove Key
                </button>
              </div>
              <p className="mt-4 rounded-2xl border border-white/10 bg-white/5 p-3 text-sm text-slate-300">
                Current key: {storedKey ? `${storedKey.provider} ${maskApiKey(storedKey.key)}` : "none configured"}
              </p>
            </form>

            <div className="hologram-card rounded-[2rem] p-5">
              <p className="text-xs font-black uppercase tracking-[0.24em] text-fuchsia-200">Usage status</p>
              <h2 className="relative mt-2 text-2xl font-black text-white">
                {session.plan === "premium" ? "Managed premium queue" : "Free BYO-key queue"}
              </h2>
              <p className="relative mt-3 text-sm leading-6 text-slate-300">{status}</p>
            </div>
          </div>

          <div className="grid gap-5">
            <div className="glass-panel rounded-[2rem] p-5">
              <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Create</p>
              <textarea
                value={prompt}
                onChange={(event) => setPrompt(event.target.value)}
                rows={5}
                className="mt-3 w-full resize-none rounded-3xl border border-cyan-300/20 bg-slate-950/70 p-4 text-white outline-none ring-cyan-300/40 focus:ring-4"
                aria-label="Dashboard prompt"
              />
              <div className="mt-4 grid gap-3 md:grid-cols-3">
                <SelectField label="Style" value={style} options={styles} onChange={(value) => setStyle(value as AnimationStyle)} />
                <SelectField label="Duration" value={duration} options={durations} onChange={(value) => setDuration(value as AnimationDuration)} />
                <SelectField label="Source" value={source} options={sources} onChange={(value) => setSource(value as RenderSource)} />
              </div>
              <div className="mt-4 grid gap-3 md:grid-cols-2">
                {Object.entries(controls).map(([key, value]) => (
                  <label key={key} className="block">
                    <span className="flex items-center justify-between text-sm font-bold text-white">
                      <span>{key.replace(/[A-Z]/g, (letter) => ` ${letter}`).toLowerCase()}</span>
                      <span className="text-cyan-200">{value}%</span>
                    </span>
                    <input
                      type="range"
                      min="0"
                      max="100"
                      value={value}
                      onChange={(event) => setControls((current) => ({ ...current, [key]: Number(event.target.value) }))}
                      className="slider mt-2 w-full"
                    />
                  </label>
                ))}
              </div>
              <div className="mt-5 flex flex-col gap-3 sm:flex-row">
                <DashboardActionButton loading={isEnhancing} label="Enhance Prompt" loadingLabel="Enhancing..." onClick={enhanceCurrentPrompt} />
                <DashboardActionButton loading={isRendering} label="Queue Render" loadingLabel="Submitting..." onClick={queueRender} />
              </div>
              <p className="mt-4 rounded-2xl border border-white/10 bg-slate-950/70 p-4 text-sm leading-6 text-slate-300">
                {enhancedPrompt}
              </p>
            </div>

            <div className="glass-panel rounded-[2rem] p-5">
              <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Story Builder</p>
              <textarea
                value={storyIdea}
                onChange={(event) => setStoryIdea(event.target.value)}
                rows={3}
                className="mt-3 w-full resize-none rounded-3xl border border-cyan-300/20 bg-slate-950/70 p-4 text-white outline-none ring-cyan-300/40 focus:ring-4"
                aria-label="Dashboard story idea"
              />
              <DashboardActionButton loading={isStoryboarding} label="Generate Scenes" loadingLabel="Generating..." onClick={generateScenes} fullWidth />
              <div className="mt-4 grid gap-3 md:grid-cols-2">
                {storyScenes.map((scene) => (
                  <article key={scene.id} className="rounded-2xl border border-white/10 bg-white/5 p-4">
                    <h3 className="font-black text-white">{scene.title}</h3>
                    <p className="mt-2 line-clamp-3 text-sm leading-6 text-slate-300">{scene.prompt}</p>
                  </article>
                ))}
              </div>
            </div>

            <div className="glass-panel rounded-[2rem] p-5">
              <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Recent jobs</p>
              <div className="mt-4 grid gap-3">
                {jobs.length === 0 ? (
                  <p className="rounded-2xl border border-white/10 bg-white/5 p-4 text-sm text-slate-300">
                    No render jobs yet. Enhance a prompt and queue your first dashboard render.
                  </p>
                ) : (
                  jobs.map((job) => (
                    <article key={job.id} className="rounded-2xl border border-white/10 bg-white/5 p-4">
                      <div className="flex flex-wrap items-center justify-between gap-3">
                        <h3 className="font-black text-white">{job.id}</h3>
                        <span className="rounded-full bg-cyan-300/10 px-3 py-1 text-xs font-black text-cyan-100">
                          {job.status} {job.progress}%
                        </span>
                      </div>
                      <p className="mt-2 text-sm text-slate-300">{job.storagePath}</p>
                    </article>
                  ))
                )}
              </div>
            </div>
          </div>
        </div>
      </section>
    </DashboardShell>
  );
}

function DashboardShell({ children, email }: { children: ReactNode; email?: string }) {
  return (
    <main className="relative min-h-screen px-4 py-5 text-slate-100 sm:px-6 lg:px-8">
      <nav className="mx-auto flex max-w-7xl items-center justify-between rounded-full border border-white/10 bg-slate-950/75 px-4 py-3 shadow-2xl shadow-cyan-950/30 backdrop-blur-xl">
        <Link href="/" className="flex items-center gap-3" aria-label="SkyMotion AI home">
          <span className="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-300 text-lg font-black text-slate-950 shadow-lg shadow-cyan-400/30">
            SK
          </span>
          <span>
            <span className="block text-sm font-semibold uppercase tracking-[0.32em] text-cyan-200">SkyMotion</span>
            <span className="block text-xs text-slate-400">Dashboard</span>
          </span>
        </Link>
        <div className="flex items-center gap-2">
          {email && <span className="hidden rounded-full bg-white/5 px-3 py-2 text-xs font-bold text-slate-300 sm:inline-flex">{email}</span>}
          <Link href="/logout/" className="rounded-full border border-white/15 bg-white/5 px-4 py-2 text-sm font-black text-white transition hover:bg-white/10">
            Logout
          </Link>
        </div>
      </nav>
      {children}
    </main>
  );
}

function PlanCard({
  title,
  detail,
  active,
  disabled,
  onClick,
}: {
  title: string;
  detail: string;
  active: boolean;
  disabled: boolean;
  onClick: () => void;
}) {
  return (
    <button
      type="button"
      disabled={disabled}
      onClick={onClick}
      className={`rounded-[2rem] border p-6 text-left transition hover:-translate-y-1 disabled:cursor-wait disabled:opacity-70 ${
        active ? "border-cyan-300 bg-cyan-300 text-slate-950" : "border-white/10 bg-white/5 text-white hover:bg-white/10"
      }`}
    >
      <span className={`text-xs font-black uppercase tracking-[0.24em] ${active ? "text-slate-800" : "text-cyan-200"}`}>
        {active ? "Active" : "Switch"}
      </span>
      <span className="mt-3 block text-2xl font-black">{title}</span>
      <span className={`mt-2 block text-sm leading-6 ${active ? "text-slate-800" : "text-slate-300"}`}>{detail}</span>
    </button>
  );
}

function SelectField({
  label,
  value,
  options,
  onChange,
}: {
  label: string;
  value: string;
  options: string[];
  onChange: (value: string) => void;
}) {
  return (
    <label className="block">
      <span className="text-sm font-bold text-cyan-100">{label}</span>
      <select
        value={value}
        onChange={(event) => onChange(event.target.value)}
        className="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950 p-3 text-white outline-none"
      >
        {options.map((option) => (
          <option key={option} value={option}>
            {option === "3d" ? "3D" : option.charAt(0).toUpperCase() + option.slice(1)}
          </option>
        ))}
      </select>
    </label>
  );
}

function DashboardActionButton({
  loading,
  label,
  loadingLabel,
  onClick,
  fullWidth = false,
}: {
  loading: boolean;
  label: string;
  loadingLabel: string;
  onClick: () => void;
  fullWidth?: boolean;
}) {
  return (
    <button
      type="button"
      disabled={loading}
      onClick={onClick}
      className={`flex items-center justify-center gap-2 rounded-2xl bg-cyan-300 px-5 py-3 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200 disabled:cursor-wait disabled:opacity-70 ${
        fullWidth ? "mt-4 w-full" : ""
      }`}
    >
      {loading && <span className="loading-spinner" aria-hidden="true" />}
      {loading ? loadingLabel : label}
    </button>
  );
}
