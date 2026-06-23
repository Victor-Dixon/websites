"use client";

import { useEffect, useMemo, useState } from "react";
import { RenderPreviewPanel } from "@/components/RenderPreviewPanel";
import {
  loadSavedRender,
  saveRenderPreview,
  submitAndPollVideoJob,
  type RenderJobPublic,
} from "@/lib/ai-video-client";
import { enhancePrompt, generateStoryScenes, type AnimationDuration, type AnimationStyle, type StoryScene } from "@/lib/prompt-engine";
import { type RenderSource } from "@/lib/render-queue";
import {
  canGenerateVideo,
  fetchSparkSession,
  isStaffSession,
  sparkLoginUrl,
  sparkSignupUrl,
  startSkyMotionCheckout,
  type SparkSession,
} from "@/lib/spark-auth-client";
import {
  buildSkyMotionPromptFromCharacter,
  readCharacterFromQuery,
} from "@/lib/spark-character-bridge";

type ControlKey =
  | "cameraZoom"
  | "cameraPan"
  | "slowMotion"
  | "characterMotion"
  | "backgroundMotion"
  | "particles"
  | "lighting";

type ControlState = Record<ControlKey, number>;

const styles: AnimationStyle[] = ["cinematic", "anime", "cartoon", "realistic", "fantasy", "3d"];
const durations: AnimationDuration[] = ["5s", "10s", "30s", "60s"];

const controlLabels: Array<{ key: ControlKey; label: string; detail: string }> = [
  { key: "cameraZoom", label: "Camera zoom", detail: "Lens push and pull intensity" },
  { key: "cameraPan", label: "Camera pan", detail: "Horizontal scene travel" },
  { key: "slowMotion", label: "Slow motion", detail: "Temporal stretch for drama" },
  { key: "characterMotion", label: "Character movement", detail: "Body, face, and gesture energy" },
  { key: "backgroundMotion", label: "Background movement", detail: "Clouds, traffic, crowds, foliage" },
  { key: "particles", label: "Particle effects", detail: "Rain, sparks, dust, magic" },
  { key: "lighting", label: "Lighting effects", detail: "Rim light, bloom, lightning, glow" },
];

const dashboardSections = ["Home", "Create Animation", "My Projects", "AI Story Builder", "Community", "Settings"];

const studioProjects = [
  { title: "Cloud Kingdom Pilot", status: "Ready for 4K export", folder: "Spark Motion Films" },
  { title: "Neon Samurai Loop", status: "Rendering sound design", folder: "Anime Tests" },
  { title: "Ocean City Reveal", status: "Needs voice pass", folder: "Client Pitches" },
];

const communityPosts = [
  { creator: "Mira VFX", title: "Crystal Dragons Over Seoul", likes: "42.8K", comments: "1.2K" },
  { creator: "OrbitFrame", title: "Mars Ballet in Zero Gravity", likes: "31.4K", comments: "884" },
  { creator: "Nocturne Lab", title: "Rainy Cyberpunk Cafe Cats", likes: "27.9K", comments: "643" },
];

const defaultControls: ControlState = {
  cameraZoom: 64,
  cameraPan: 48,
  slowMotion: 32,
  characterMotion: 74,
  backgroundMotion: 68,
  particles: 82,
  lighting: 90,
};

const examplePrompt =
  "A breathtaking floating cloud kingdom at sunset with multiple girls relaxing on glowing cloud sofas, cinematic lighting, dreamy atmosphere, ultra detailed animated scene.";

export default function Home() {
  const [prompt, setPrompt] = useState("girls on clouds");
  const [enhancedPrompt, setEnhancedPrompt] = useState(examplePrompt);
  const [storyIdea, setStoryIdea] = useState("a courier discovers that storms are living creatures");
  const [storyScenes, setStoryScenes] = useState<StoryScene[]>([]);
  const [style, setStyle] = useState<AnimationStyle>("cinematic");
  const [duration, setDuration] = useState<AnimationDuration>("10s");
  const [source, setSource] = useState<RenderSource>("text");
  const [controls, setControls] = useState<ControlState>(defaultControls);
  const [renderStatus, setRenderStatus] = useState("Ready — uses Fal credits when live");
  const [previewVideoUrl, setPreviewVideoUrl] = useState<string | null>(null);
  const [previewPrompt, setPreviewPrompt] = useState<string | null>(null);
  const [renderProgress, setRenderProgress] = useState(0);
  const [activeJobId, setActiveJobId] = useState<string | null>(null);
  const [isRendering, setIsRendering] = useState(false);
  const [isEnhancing, setIsEnhancing] = useState(false);
  const [isStoryboarding, setIsStoryboarding] = useState(false);
  const [authSession, setAuthSession] = useState<SparkSession | null>(null);
  const [authLoading, setAuthLoading] = useState(true);
  const [checkoutLoading, setCheckoutLoading] = useState(false);
  const [linkedCharacterName, setLinkedCharacterName] = useState<string | null>(null);

  const canRender = canGenerateVideo(authSession);
  const isStaff = isStaffSession(authSession);

  useEffect(() => {
    let cancelled = false;
    async function loadSession() {
      try {
        const session = await fetchSparkSession();
        if (!cancelled) {
          setAuthSession(session);
        }
      } finally {
        if (!cancelled) {
          setAuthLoading(false);
        }
      }
    }
    loadSession();
    return () => {
      cancelled = true;
    };
  }, []);

  useEffect(() => {
    const character = readCharacterFromQuery();
    if (!character) {
      return;
    }
    const videoPrompt = buildSkyMotionPromptFromCharacter(character);
    setPrompt(character.lead_domain || "Spark character");
    setEnhancedPrompt(videoPrompt);
    setLinkedCharacterName(character.name || character.lead_domain || "Your Spark");
    setRenderStatus(`Loaded Spark character: ${character.lead_domain || "character"} — ready for cinematic preview`);
  }, []);

  useEffect(() => {
    const saved = loadSavedRender();
    if (saved?.video_url) {
      setPreviewVideoUrl(saved.video_url);
      setPreviewPrompt(saved.prompt);
      setRenderStatus(`Saved render ${saved.job_id} ready`);
      setActiveJobId(saved.job_id);
      setRenderProgress(100);
    }
  }, []);

  const controlAverage = useMemo(() => {
    const values = Object.values(controls);
    return Math.round(values.reduce((total, value) => total + value, 0) / values.length);
  }, [controls]);

  async function enhanceCurrentPrompt() {
    setIsEnhancing(true);
    try {
      setEnhancedPrompt(enhancePrompt({ prompt, style, duration }));
    } finally {
      setIsEnhancing(false);
    }
  }

  async function buildStory() {
    setIsStoryboarding(true);
    try {
      setStoryScenes(generateStoryScenes(storyIdea));
    } finally {
      setIsStoryboarding(false);
    }
  }

  function handleRenderUpdate(job: RenderJobPublic) {
    setActiveJobId(job.job_id);
    setRenderProgress(job.progress);
    setRenderStatus(`${job.job_id} · ${job.status} · ${job.progress}%`);

    if (job.status === "succeeded" && job.video_url) {
      setPreviewVideoUrl(job.video_url);
      setPreviewPrompt(enhancedPrompt);
      saveRenderPreview(job, enhancedPrompt);
      setRenderStatus(`Render complete — playing in preview (${job.job_id})`);
    }
  }

  async function queueRenderJob() {
    if (isRendering) {
      return;
    }
    if (!canRender) {
      if (!authSession?.logged_in) {
        setRenderStatus("Sign in required — create a free MaskZero account first.");
        return;
      }
      setRenderStatus("Payment required — subscribe or buy render credits to generate.");
      return;
    }
    if (source !== "text") {
      setRenderStatus("Only text-to-video is live today — switch to Text prompt");
      return;
    }

    setIsRendering(true);
    setPreviewVideoUrl(null);
    setRenderProgress(1);
    setRenderStatus("Submitting live render job…");

    try {
      const job = await submitAndPollVideoJob(enhancedPrompt, handleRenderUpdate);
      handleRenderUpdate(job);
      const refreshed = await fetchSparkSession();
      setAuthSession(refreshed);
    } catch (error) {
      const message = error instanceof Error ? error.message : "Render failed";
      setRenderStatus(message);
    } finally {
      setIsRendering(false);
    }
  }

  async function handleSubscribe() {
    setCheckoutLoading(true);
    try {
      const checkout = await startSkyMotionCheckout();
      if (checkout.ok && checkout.url) {
        window.location.href = checkout.url;
        return;
      }
      setRenderStatus(checkout.message || "Checkout is not available yet.");
    } catch {
      setRenderStatus("Could not start checkout — try again or contact support.");
    } finally {
      setCheckoutLoading(false);
    }
  }

  return (
    <main className="relative min-h-screen overflow-hidden px-4 py-5 text-[var(--cream)] sm:px-6 lg:px-8">
      <div className="aurora-orb pointer-events-none absolute left-[-8rem] top-20 h-72 w-72 rounded-full bg-[var(--red)]/20 blur-3xl" />
      <div className="aurora-orb pointer-events-none absolute right-[-10rem] top-80 h-96 w-96 rounded-full bg-[var(--purple)]/20 blur-3xl" />

      <nav className="studio-nav comic-nav mx-auto flex max-w-7xl items-center justify-between rounded-none px-4 py-3">
        <a href="/" className="flex items-center gap-3" aria-label="MaskZero home">
          <span className="grid h-11 w-11 place-items-center rounded-none border-4 border-[var(--ink)] bg-[var(--yellow)] text-lg font-black text-[var(--ink)]">
            MZ
          </span>
          <span>
            <span className="block text-sm font-semibold uppercase tracking-[0.32em] text-[var(--yellow)]">MaskZero</span>
            <span className="block text-xs text-[var(--cream)]">Spark Animation Studio</span>
          </span>
        </a>
        <div className="hidden items-center gap-1 lg:flex">
          <a href="/" className="rounded-none px-4 py-2 text-sm transition hover:bg-[var(--yellow)] hover:text-[var(--ink)]">Home</a>
          <a href="/quiz/" className="rounded-none px-4 py-2 text-sm transition hover:bg-[var(--yellow)] hover:text-[var(--ink)]">Quiz</a>
          <a href="/spark-dashboard/" className="rounded-none px-4 py-2 text-sm transition hover:bg-[var(--yellow)] hover:text-[var(--ink)]">Dashboard</a>
          {dashboardSections.slice(1, 3).map((item) => (
            <a
              key={item}
              href={`#${item.toLowerCase().replaceAll(" ", "-")}`}
              className="rounded-none px-4 py-2 text-sm transition hover:bg-[var(--yellow)] hover:text-[var(--ink)]"
            >
              {item}
            </a>
          ))}
        </div>
        <div className="flex items-center gap-2">
          {!authLoading && !authSession?.logged_in ? (
            <>
              <a
                href={sparkLoginUrl("/")}
                className="hidden rounded-full border border-white/15 px-4 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10 sm:inline-flex"
              >
                Log in
              </a>
              <a
                href={sparkSignupUrl()}
                className="hidden rounded-full border border-cyan-300/30 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:bg-cyan-300/10 sm:inline-flex"
              >
                Sign up
              </a>
            </>
          ) : null}
          {!authLoading && authSession?.logged_in ? (
            <span className="hidden max-w-[12rem] truncate rounded-full border border-white/10 px-3 py-2 text-xs text-slate-300 sm:inline-flex">
              {isStaff ? "Owner access" : authSession.spark_plan || "free"}
            </span>
          ) : null}
          <a
            href="#create-animation"
            className="rounded-full bg-white px-5 py-2 text-sm font-bold text-slate-950 transition hover:bg-cyan-200"
          >
            Start creating
          </a>
        </div>
      </nav>

      <section id="home" className="mx-auto grid max-w-7xl gap-8 pb-14 pt-10 lg:grid-cols-[1.05fr_0.95fr] lg:pb-20 lg:pt-16">
        <div className="flex flex-col justify-center">
          <div className="studio-badge mb-5 inline-flex w-fit items-center gap-2 rounded-none px-4 py-2 text-xs">
            Spark Motion™ production engine live
          </div>
          <h1 className="studio-heading text-balance text-5xl font-black tracking-tight sm:text-6xl lg:text-7xl">
            Turn ideas, images, and clips into cinematic animated movies.
          </h1>
          <p className="studio-copy mt-6 max-w-2xl text-lg leading-8">
            MaskZero Studio combines text-to-video generation, image animation, AI storyboarding, voices, music,
            render queues, cloud storage, and the Spark creator community in one comic-book studio.
          </p>
          <div className="mt-8 grid gap-3 sm:flex">
            <a
              href="#create-animation"
              className="neon-border rounded-2xl bg-cyan-400 px-6 py-4 text-center font-black text-slate-950 transition hover:scale-[1.01]"
            >
              Generate animation
            </a>
            <a
              href="#spark-motion"
              className="rounded-2xl border border-white/15 bg-white/5 px-6 py-4 text-center font-bold text-white transition hover:bg-white/10"
            >
              Explore Spark Motion™
            </a>
          </div>
          <div className="mt-8 grid grid-cols-3 gap-3">
            <MetricCard value="4K" label="Pro exports" />
            <MetricCard value="60s" label="Scene duration" />
            <MetricCard value="7" label="Motion controls" />
          </div>
        </div>

        <div className="glass-panel relative min-h-[540px] overflow-hidden rounded-[2rem] p-4 sm:p-6">
          <div className="absolute inset-x-8 top-8 h-48 rounded-full bg-cyan-400/20 blur-3xl" />
          <div className="relative rounded-[1.5rem] border border-white/10 bg-slate-950/70 p-4">
            <div className="mb-4 flex items-center justify-between">
              <span className="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-emerald-200">
                Rendering preview
              </span>
              <span className="text-xs text-slate-400">Shot 08 / Act 02</span>
            </div>
            <RenderPreviewPanel
              videoUrl={previewVideoUrl}
              statusLabel={renderStatus}
              progress={renderProgress}
              promptLabel={previewPrompt ?? enhancedPrompt}
            />
            <div className="mt-4 grid grid-cols-4 gap-2">
              {["Storyboard", "Animate", "Voice", "Render"].map((step, index) => (
                <div key={step} className="rounded-xl border border-white/10 bg-white/5 p-3">
                  <div className="text-xs font-bold text-cyan-200">0{index + 1}</div>
                  <div className="mt-1 text-xs text-slate-300">{step}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section id="create-animation" className="mx-auto max-w-7xl py-8">
        <SectionHeading
          eyebrow="Create Animation"
          title="AI animation generator with pro-grade controls"
          description="Enter a scene, choose a visual style, upload source media, enhance the prompt, and queue a render with cinematic motion."
        />

        <div className="grid gap-5 lg:grid-cols-[1.05fr_0.95fr]">
          <div className="glass-panel rounded-[2rem] p-5 sm:p-6">
            <div className="mb-5 grid gap-3 sm:grid-cols-3">
              {(["text", "image", "video"] as RenderSource[]).map((item) => (
                <button
                  key={item}
                  type="button"
                  onClick={() => setSource(item)}
                  className={`rounded-2xl border px-4 py-3 text-left font-bold capitalize transition ${
                    source === item
                      ? "border-cyan-300 bg-cyan-300/15 text-cyan-100"
                      : "border-white/10 bg-white/5 text-slate-300 hover:bg-white/10"
                  }`}
                >
                  {item === "text" ? "Text prompt" : item === "image" ? "Image to animation" : "Short clip remix"}
                </button>
              ))}
            </div>

            <label htmlFor="prompt" className="text-sm font-bold text-cyan-100">
              Scene prompt
            </label>
            <textarea
              id="prompt"
              value={prompt}
              onChange={(event) => setPrompt(event.target.value)}
              rows={5}
              className="mt-2 w-full resize-none rounded-3xl border border-cyan-300/20 bg-slate-950/70 p-4 text-base text-white outline-none ring-cyan-300/40 transition placeholder:text-slate-500 focus:ring-4"
              placeholder="Describe a scene: characters, camera, mood, weather, action..."
            />

            <div className="mt-5 grid gap-4 sm:grid-cols-2">
              <div>
                <label htmlFor="style" className="text-sm font-bold text-cyan-100">
                  Animation style
                </label>
                <select
                  id="style"
                  value={style}
                  onChange={(event) => setStyle(event.target.value as AnimationStyle)}
                  className="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950 p-3 text-white outline-none"
                >
                  {styles.map((item) => (
                    <option key={item} value={item}>
                      {item === "3d" ? "3D" : item.charAt(0).toUpperCase() + item.slice(1)}
                    </option>
                  ))}
                </select>
              </div>
              <div>
                <label htmlFor="duration" className="text-sm font-bold text-cyan-100">
                  Duration
                </label>
                <select
                  id="duration"
                  value={duration}
                  onChange={(event) => setDuration(event.target.value as AnimationDuration)}
                  className="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950 p-3 text-white outline-none"
                >
                  {durations.map((item) => (
                    <option key={item} value={item}>
                      {item}
                    </option>
                  ))}
                </select>
              </div>
            </div>

            <div className="mt-5 grid gap-4 sm:grid-cols-2">
              <UploadCard title="Upload image" detail="Animate portraits, products, scenes, and concept art." accept="image/*" />
              <UploadCard title="Upload clip" detail="Extend, stylize, or transform short video clips." accept="video/*" />
            </div>

            <div className="mt-5 flex flex-col gap-3 sm:flex-row">
              <button
                type="button"
                onClick={enhanceCurrentPrompt}
                className="purple-border rounded-2xl bg-fuchsia-400 px-5 py-3 font-black text-slate-950 transition hover:scale-[1.01]"
              >
                {isEnhancing ? "Enhancing..." : "Enhance prompt"}
              </button>
              <button
                type="button"
                onClick={queueRenderJob}
                disabled={isRendering || authLoading || !canRender}
                className="rounded-2xl bg-cyan-300 px-5 py-3 font-black text-slate-950 transition hover:scale-[1.01] disabled:cursor-not-allowed disabled:opacity-60"
              >
                {isRendering ? `Rendering${activeJobId ? ` ${activeJobId.slice(0, 8)}` : ""}…` : "Generate live preview"}
              </button>
            </div>

            {!authLoading && !authSession?.logged_in ? (
              <div className="mt-4 rounded-2xl border border-amber-300/30 bg-amber-300/10 p-4 text-sm text-amber-100">
                <p className="font-bold">Account required</p>
                <p className="mt-1 leading-7">
                  Create a MaskZero account and unlock studio render access before generating live video previews.
                </p>
                <div className="mt-3 flex flex-wrap gap-3">
                  <a href={sparkSignupUrl()} className="rounded-full bg-white px-4 py-2 font-bold text-slate-950">
                    Create account
                  </a>
                  <a
                    href={sparkLoginUrl("/#create-animation")}
                    className="rounded-full border border-white/20 px-4 py-2 font-bold text-white"
                  >
                    Log in
                  </a>
                </div>
              </div>
            ) : null}

            {!authLoading && authSession?.logged_in && !canRender ? (
              <div className="mt-4 rounded-2xl border border-fuchsia-300/30 bg-fuchsia-400/10 p-4 text-sm text-fuchsia-100">
                <p className="font-bold">Payment required</p>
                <p className="mt-1 leading-7">
                  Signed in as {authSession.display_name || authSession.email}. Subscribe or buy render credits to
                  unlock live Fal-powered previews.
                </p>
                <button
                  type="button"
                  onClick={handleSubscribe}
                  disabled={checkoutLoading}
                  className="mt-3 rounded-full bg-fuchsia-300 px-4 py-2 font-bold text-slate-950 disabled:opacity-60"
                >
                  {checkoutLoading ? "Opening checkout…" : "Subscribe / buy credits"}
                </button>
              </div>
            ) : null}

            {!authLoading && linkedCharacterName ? (
              <p className="mt-4 rounded-2xl border border-cyan-300/20 bg-cyan-300/10 p-3 text-sm text-cyan-100">
                Spark character loaded: <strong>{linkedCharacterName}</strong> — prompt prefilled from your dossier.
              </p>
            ) : null}

            {!authLoading && canRender ? (
              <p className="mt-4 rounded-2xl border border-emerald-300/20 bg-emerald-400/10 p-3 text-sm text-emerald-100">
                {isStaff
                  ? "Owner/admin access — live renders are free on your account."
                  : `MaskZero Studio access active (${authSession?.spark_plan || "paid"}${authSession?.skymotion_render_credits ? ` · ${authSession.skymotion_render_credits} credits` : ""}).`}
              </p>
            ) : null}
          </div>

          <div className="grid gap-5">
            <div className="hologram-card rounded-[2rem] p-5">
              <div className="flex items-center justify-between gap-4">
                <div>
                  <p className="text-xs font-bold uppercase tracking-[0.24em] text-fuchsia-200">AI Prompt Engine</p>
                  <h3 className="mt-2 text-2xl font-black text-white">Professional prompt rewrite</h3>
                </div>
                <span className="rounded-full bg-cyan-300/10 px-3 py-1 text-sm font-bold text-cyan-100">
                  {style}/{duration}
                </span>
              </div>
              <p className="relative mt-4 rounded-2xl border border-white/10 bg-slate-950/70 p-4 leading-7 text-slate-200">
                {enhancedPrompt}
              </p>
            </div>

            <div className="glass-panel rounded-[2rem] p-5">
              <div className="mb-4 flex items-center justify-between">
                <h3 className="text-xl font-black text-white">Animation controls</h3>
                <span className="rounded-full bg-cyan-300/10 px-3 py-1 text-sm font-bold text-cyan-100">
                  {controlAverage}% motion mix
                </span>
              </div>
              <div className="grid gap-4">
                {controlLabels.map((control) => (
                  <label key={control.key} className="block">
                    <span className="flex items-center justify-between gap-3">
                      <span>
                        <span className="block text-sm font-bold text-white">{control.label}</span>
                        <span className="block text-xs text-slate-400">{control.detail}</span>
                      </span>
                      <span className="text-sm font-black text-cyan-200">{controls[control.key]}%</span>
                    </span>
                    <input
                      type="range"
                      min="0"
                      max="100"
                      value={controls[control.key]}
                      onChange={(event) =>
                        setControls((current) => ({
                          ...current,
                          [control.key]: Number(event.target.value),
                        }))
                      }
                      className="slider mt-3 w-full"
                    />
                  </label>
                ))}
              </div>
              <p className="mt-4 rounded-2xl border border-cyan-300/20 bg-cyan-300/10 p-3 text-sm text-cyan-100">
                Render queue: {renderStatus}
              </p>
            </div>
          </div>
        </div>
      </section>

      <section id="spark-motion" className="mx-auto max-w-7xl py-10">
        <div className="glass-panel overflow-hidden rounded-[2rem] p-5 sm:p-8">
          <div className="grid gap-8 lg:grid-cols-[0.9fr_1.1fr]">
            <div>
              <p className="studio-panel-title text-sm font-black">MaskZero engine</p>
              <h2 className="studio-heading mt-3 text-4xl font-black sm:text-5xl">Spark Motion™ builds the whole movie.</h2>
              <p className="studio-copy mt-4 leading-8">
                Give MaskZero Studio one Spark idea and Spark Motion™ automatically generates characters, backgrounds,
                camera angles, animation sequences, voice acting, sound effects, and the final rendered movie package.
              </p>
            </div>
            <div className="grid gap-3 sm:grid-cols-2">
              {[
                "Character casting",
                "Background generation",
                "Camera angle design",
                "Animation sequencing",
                "AI voice acting",
                "Sound effects and music",
              ].map((item, index) => (
                <div key={item} className="rounded-3xl border border-white/10 bg-white/5 p-5">
                  <div className="mb-5 h-2 rounded-full bg-gradient-to-r from-cyan-300 to-fuchsia-400" />
                  <div className="text-sm font-black text-cyan-200">Pipeline {index + 1}</div>
                  <div className="mt-2 text-lg font-bold text-white">{item}</div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section id="ai-story-builder" className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="AI Story Builder"
          title="Generate complete animated short films"
          description="Transform a story idea into a sequence of scenes with prompts, camera notes, dialogue-ready audio cues, and render durations."
        />
        <div className="grid gap-5 lg:grid-cols-[0.85fr_1.15fr]">
          <div className="glass-panel rounded-[2rem] p-5">
            <label htmlFor="story" className="text-sm font-bold text-cyan-100">
              Story idea
            </label>
            <textarea
              id="story"
              value={storyIdea}
              onChange={(event) => setStoryIdea(event.target.value)}
              rows={7}
              className="mt-2 w-full resize-none rounded-3xl border border-cyan-300/20 bg-slate-950/70 p-4 text-white outline-none ring-cyan-300/40 focus:ring-4"
            />
            <button
              type="button"
              onClick={buildStory}
              className="mt-4 w-full rounded-2xl bg-white px-5 py-3 font-black text-slate-950 transition hover:bg-cyan-200"
            >
              {isStoryboarding ? "Building scenes..." : "Generate short film"}
            </button>
          </div>
          <div className="grid gap-4">
            {(storyScenes.length ? storyScenes : placeholderScenes).map((scene, index) => (
              <article key={scene.id} className="hologram-card rounded-3xl p-5">
                <div className="relative flex items-start justify-between gap-4">
                  <div>
                    <p className="text-xs font-bold uppercase tracking-[0.22em] text-fuchsia-200">
                      Scene {String(index + 1).padStart(2, "0")} / {scene.duration}
                    </p>
                    <h3 className="mt-2 text-xl font-black text-white">{scene.title}</h3>
                  </div>
                  <span className="rounded-full bg-cyan-300/10 px-3 py-1 text-xs font-bold text-cyan-100">Auto-shot</span>
                </div>
                <p className="relative mt-3 text-sm leading-6 text-slate-300">{scene.prompt}</p>
                <div className="relative mt-4 grid gap-3 sm:grid-cols-2">
                  <p className="rounded-2xl border border-white/10 bg-white/5 p-3 text-xs text-slate-300">
                    <span className="block font-bold text-white">Camera</span>
                    {scene.camera}
                  </p>
                  <p className="rounded-2xl border border-white/10 bg-white/5 p-3 text-xs text-slate-300">
                    <span className="block font-bold text-white">Audio</span>
                    {scene.audio}
                  </p>
                </div>
              </article>
            ))}
          </div>
        </div>
      </section>

      <section id="my-projects" className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="Creator Studio"
          title="Save, edit, duplicate, organize, and export"
          description="Project management is designed for solo creators, agencies, and teams producing multiple animated campaigns."
        />
        <div className="grid gap-4 md:grid-cols-3">
          {studioProjects.map((project) => (
            <article key={project.title} className="glass-panel rounded-3xl p-5">
              <div className="mb-4 h-36 rounded-2xl bg-gradient-to-br from-cyan-400/30 via-slate-900 to-fuchsia-500/30" />
              <p className="text-xs font-bold uppercase tracking-[0.22em] text-cyan-200">{project.folder}</p>
              <h3 className="mt-2 text-xl font-black text-white">{project.title}</h3>
              <p className="mt-2 text-sm text-slate-400">{project.status}</p>
              <div className="mt-4 grid grid-cols-3 gap-2 text-center text-xs font-bold text-slate-300">
                <button type="button" className="rounded-xl bg-white/5 py-2 hover:bg-white/10">
                  Edit
                </button>
                <button type="button" className="rounded-xl bg-white/5 py-2 hover:bg-white/10">
                  Duplicate
                </button>
                <button type="button" className="rounded-xl bg-white/5 py-2 hover:bg-white/10">
                  Export
                </button>
              </div>
            </article>
          ))}
        </div>
      </section>

      <section id="community" className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="Community Hub"
          title="A social network for AI filmmakers"
          description="Share public animations, like and comment, follow creators, discover trending films, and feature rising artists."
        />
        <div className="grid gap-5 lg:grid-cols-[1fr_0.7fr]">
          <div className="grid gap-4">
            {communityPosts.map((post, index) => (
              <article key={post.title} className="glass-panel flex gap-4 rounded-3xl p-4">
                <div className="grid h-24 w-24 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-cyan-300/40 to-fuchsia-500/40 text-2xl font-black">
                  {index + 1}
                </div>
                <div className="min-w-0">
                  <p className="text-sm font-bold text-cyan-200">@{post.creator}</p>
                  <h3 className="mt-1 text-xl font-black text-white">{post.title}</h3>
                  <p className="mt-2 text-sm text-slate-400">
                    {post.likes} likes · {post.comments} comments · Featured creator eligible
                  </p>
                </div>
              </article>
            ))}
          </div>
          <div className="hologram-card rounded-[2rem] p-5">
            <p className="text-xs font-black uppercase tracking-[0.24em] text-fuchsia-200">Trending page</p>
            <h3 className="mt-3 text-3xl font-black text-white">Creator graph</h3>
            <div className="mt-5 space-y-4">
              {["Follow creators", "Comment threads", "Public remixes", "Featured reels"].map((item) => (
                <div key={item} className="relative rounded-2xl border border-white/10 bg-slate-950/70 p-4">
                  <div className="relative text-sm font-bold text-white">{item}</div>
                  <div className="relative mt-2 h-2 rounded-full bg-gradient-to-r from-cyan-300 to-fuchsia-500" />
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      <section id="settings" className="mx-auto max-w-7xl py-10">
        <div className="grid gap-5 lg:grid-cols-3">
          <PlanCard
            name="Free"
            price="$0"
            features={["Watermarked exports", "Limited generations", "Community publishing", "Starter styles"]}
          />
          <PlanCard
            name="Pro"
            price="$29"
            highlighted
            features={["Unlimited generations", "4K exports", "Faster rendering", "Premium styles", "Commercial usage"]}
          />
          <div className="glass-panel rounded-[2rem] p-6">
            <p className="text-sm font-black uppercase tracking-[0.24em] text-cyan-200">Technical stack</p>
            <h3 className="mt-3 text-2xl font-black text-white">Built for production</h3>
            <ul className="mt-5 space-y-3 text-sm text-slate-300">
              {[
                "Next.js App Router and React",
                "TypeScript strict mode",
                "Tailwind CSS responsive system",
                "Supabase auth and cloud storage hooks",
                "AI prompt engine endpoints",
                "Video rendering queue API",
              ].map((item) => (
                <li key={item} className="flex gap-3">
                  <span className="mt-1 h-2 w-2 rounded-full bg-cyan-300" />
                  <span>{item}</span>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </section>
    </main>
  );
}

const placeholderScenes: StoryScene[] = [
  {
    id: "placeholder-01",
    title: "Opening discovery",
    prompt: "Enter a story idea and MaskZero Studio will create the first cinematic scene with characters and mood.",
    camera: "Dolly-in with subtle parallax.",
    audio: "Ambient tone and first line cue.",
    duration: "10s",
  },
  {
    id: "placeholder-02",
    title: "World reveal",
    prompt: "The second scene expands the world, camera language, and animation beats.",
    camera: "Wide crane reveal.",
    audio: "Theme swell and environment effects.",
    duration: "30s",
  },
];

function SectionHeading({
  eyebrow,
  title,
  description,
}: {
  eyebrow: string;
  title: string;
  description: string;
}) {
  return (
    <div className="mb-6 max-w-3xl">
      <p className="text-sm font-black uppercase tracking-[0.26em] text-cyan-200">{eyebrow}</p>
      <h2 className="mt-3 text-3xl font-black text-white sm:text-5xl">{title}</h2>
      <p className="mt-4 leading-8 text-slate-300">{description}</p>
    </div>
  );
}

function MetricCard({ value, label }: { value: string; label: string }) {
  return (
    <div className="glass-panel rounded-3xl p-4">
      <div className="text-2xl font-black text-white">{value}</div>
      <div className="mt-1 text-xs uppercase tracking-[0.16em] text-slate-400">{label}</div>
    </div>
  );
}

function UploadCard({ title, detail, accept }: { title: string; detail: string; accept: string }) {
  return (
    <label className="block cursor-pointer rounded-3xl border border-dashed border-cyan-300/30 bg-cyan-300/5 p-4 transition hover:bg-cyan-300/10">
      <span className="block text-sm font-black text-white">{title}</span>
      <span className="mt-1 block text-xs leading-5 text-slate-400">{detail}</span>
      <input className="sr-only" type="file" accept={accept} />
      <span className="mt-4 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-cyan-100">
        Choose file
      </span>
    </label>
  );
}

function PlanCard({
  name,
  price,
  features,
  highlighted = false,
}: {
  name: string;
  price: string;
  features: string[];
  highlighted?: boolean;
}) {
  return (
    <article className={`rounded-[2rem] p-6 ${highlighted ? "neon-border bg-cyan-300 text-slate-950" : "glass-panel"}`}>
      <p className={`text-sm font-black uppercase tracking-[0.24em] ${highlighted ? "text-slate-800" : "text-cyan-200"}`}>
        {name} Plan
      </p>
      <div className="mt-3 flex items-end gap-2">
        <span className="text-5xl font-black">{price}</span>
        <span className={`pb-2 text-sm ${highlighted ? "text-slate-800" : "text-slate-400"}`}>/month</span>
      </div>
      <ul className="mt-6 space-y-3">
        {features.map((feature) => (
          <li key={feature} className="flex gap-3 text-sm font-semibold">
            <span className={`mt-1 h-2 w-2 rounded-full ${highlighted ? "bg-slate-950" : "bg-cyan-300"}`} />
            <span>{feature}</span>
          </li>
        ))}
      </ul>
    </article>
  );
}
