"use client";

import { type ChangeEvent, type FormEvent, useMemo, useRef, useState } from "react";
import { DreamMotionStage } from "@/components/DreamMotionStage";
import { createSkyMotionSupabaseClient } from "@/lib/supabase";
import { enhancePrompt, type AnimationDuration, type AnimationStyle, type StoryScene } from "@/lib/prompt-engine";
import { type RenderJob, type RenderJobRequest, type RenderSource } from "@/lib/render-queue";

type ControlKey =
  | "cameraZoom"
  | "cameraPan"
  | "slowMotion"
  | "characterMotion"
  | "backgroundMotion"
  | "particles"
  | "lighting";

type ControlState = Record<ControlKey, number>;

const templateCategories = ["Anime", "Fantasy", "Realistic", "Commercial", "Music Videos"] as const;
type TemplateCategory = (typeof templateCategories)[number];

interface VideoExample {
  id: string;
  title: string;
  category: TemplateCategory;
  prompt: string;
  style: AnimationStyle;
  duration: AnimationDuration;
  gradient: string;
  metric: string;
}

interface TemplateCardData {
  title: string;
  category: TemplateCategory;
  prompt: string;
  style: AnimationStyle;
  duration: AnimationDuration;
  result: string;
}

const navItems = [
  { label: "Home", href: "#home" },
  { label: "Create", href: "#create" },
  { label: "Templates", href: "#templates" },
  { label: "Pricing", href: "#pricing" },
  { label: "Login", href: "#login" },
];

const styles: AnimationStyle[] = ["cinematic", "anime", "cartoon", "realistic", "fantasy", "3d"];
const durations: AnimationDuration[] = ["5s", "10s", "30s", "60s"];

const sourceOptions: Array<{ value: RenderSource; label: string; detail: string }> = [
  { value: "text", label: "Text prompt", detail: "Start from a scene idea" },
  { value: "image", label: "Image upload", detail: "Animate art or products" },
  { value: "video", label: "Clip remix", detail: "Extend short footage" },
  { value: "dreammotion", label: "DreamMotion", detail: "Generate a full short" },
];

const controlLabels: Array<{ key: ControlKey; label: string; detail: string }> = [
  { key: "cameraZoom", label: "Camera zoom", detail: "Lens push intensity" },
  { key: "cameraPan", label: "Camera pan", detail: "Scene travel" },
  { key: "slowMotion", label: "Slow motion", detail: "Dramatic timing" },
  { key: "characterMotion", label: "Character motion", detail: "Gesture energy" },
  { key: "backgroundMotion", label: "Background motion", detail: "World movement" },
  { key: "particles", label: "Particles", detail: "Rain, sparks, magic" },
  { key: "lighting", label: "Lighting", detail: "Glow, bloom, rim light" },
];

const defaultControls: ControlState = {
  cameraZoom: 66,
  cameraPan: 54,
  slowMotion: 38,
  characterMotion: 78,
  backgroundMotion: 72,
  particles: 84,
  lighting: 91,
};

const videoExamples: VideoExample[] = [
  {
    id: "neon-fox",
    title: "Neon Fox Courier",
    category: "Anime",
    prompt: "A neon fox courier racing through a rain soaked mega city with reflective streets and kinetic anime speed lines.",
    style: "anime",
    duration: "10s",
    gradient: "from-cyan-300/50 via-fuchsia-500/35 to-indigo-950",
    metric: "2.1M loops",
  },
  {
    id: "dragon-market",
    title: "Dragon Market Reveal",
    category: "Fantasy",
    prompt: "A moonlit floating market where tiny dragons carry lanterns between crystal towers and cloud bridges.",
    style: "fantasy",
    duration: "30s",
    gradient: "from-emerald-300/45 via-cyan-500/25 to-violet-950",
    metric: "41K exports",
  },
  {
    id: "product-orbit",
    title: "Product Orbit Launch",
    category: "Commercial",
    prompt: "A premium sneaker rotating inside a glass rain chamber with cinematic macro lighting and social ad pacing.",
    style: "realistic",
    duration: "5s",
    gradient: "from-amber-200/45 via-cyan-400/25 to-slate-950",
    metric: "4.8x CTR",
  },
];

const templates: TemplateCardData[] = [
  {
    title: "Studio Anime Opening",
    category: "Anime",
    prompt: "A young pilot leaps across neon rooftops as holographic birds burst into the skyline, anime opening sequence.",
    style: "anime",
    duration: "10s",
    result: "Dynamic character acting, speed ramps, title-safe final frame.",
  },
  {
    title: "Floating Kingdom Trailer",
    category: "Fantasy",
    prompt: "A floating cloud kingdom at sunset with glowing bridges, heroic camera sweeps, and magical cloud particles.",
    style: "fantasy",
    duration: "30s",
    result: "Epic reveal, orchestral audio cues, layered parallax.",
  },
  {
    title: "Photoreal Founder Story",
    category: "Realistic",
    prompt: "A founder walking through a warm studio while prototypes animate on glass walls, documentary commercial tone.",
    style: "realistic",
    duration: "30s",
    result: "Natural motion, cinematic interview insert, clean brand outro.",
  },
  {
    title: "Product Launch Spot",
    category: "Commercial",
    prompt: "A luxury bottle emerging from ocean mist with macro droplets, glossy reflections, and fast ad transitions.",
    style: "cinematic",
    duration: "10s",
    result: "Social-ready composition, CTA space, premium lighting.",
  },
  {
    title: "Synthwave Performance",
    category: "Music Videos",
    prompt: "A masked singer performing on a chrome desert stage while sound waves become animated auroras.",
    style: "3d",
    duration: "60s",
    result: "Beat-synced scene changes, stage lights, export-ready reel.",
  },
];

const testimonials = [
  {
    quote: "SkyMotion made our pitch feel like a finished trailer. The first render became the visual language for the whole campaign.",
    name: "Maya Chen",
    role: "Creative Director, Northstar Studio",
  },
  {
    quote: "I used to spend a weekend blocking shots. Now I can test five styles and hand clients a polished animated proof in one session.",
    name: "Jalen Ortiz",
    role: "Freelance motion designer",
  },
  {
    quote: "The template workflow is the conversion unlock. Our paid social tests finally look premium without a full production crew.",
    name: "Priya Shah",
    role: "Growth lead, Luma Pantry",
  },
];

const creatorStories = [
  { value: "9.4M", label: "views from a fantasy short series", creator: "OrbitFrame" },
  { value: "312", label: "commercial cuts exported in a launch month", creator: "Brand Lab" },
  { value: "68%", label: "faster storyboard approval for client reels", creator: "Mira VFX" },
];

const pricingPlans = [
  {
    name: "Starter",
    price: "$0",
    detail: "Explore templates and generate watermarked previews.",
    features: ["20 preview generations", "Prompt enhancer", "Community gallery"],
  },
  {
    name: "Creator",
    price: "$29",
    detail: "Publish polished shorts and social campaigns.",
    features: ["Unlimited drafts", "HD exports", "Commercial templates", "Priority queue"],
    highlighted: true,
  },
  {
    name: "Studio",
    price: "$99",
    detail: "Scale client work with team review and brand controls.",
    features: ["4K exports", "Team seats", "Brand presets", "Campaign storage"],
  },
];

const placeholderScenes: StoryScene[] = [
  {
    id: "placeholder-01",
    title: "Opening hook",
    prompt: "Add a story idea and SkyMotion will generate a cinematic first shot with characters, mood, and action.",
    camera: "Dolly-in with subtle parallax.",
    audio: "Atmospheric tone and first line cue.",
    duration: "10s",
  },
  {
    id: "placeholder-02",
    title: "World reveal",
    prompt: "The second scene expands scale, camera language, and the emotional turn.",
    camera: "Wide crane reveal.",
    audio: "Theme swell and environment effects.",
    duration: "30s",
  },
];

const delay = (durationMs: number) => new Promise((resolve) => window.setTimeout(resolve, durationMs));

async function postJson<TResponse>(url: string, payload: unknown): Promise<TResponse> {
  const response = await fetch(url, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(payload),
  });
  const data = (await response.json()) as TResponse & { error?: string };

  if (!response.ok) {
    throw new Error(data.error ?? `Request failed with status ${response.status}`);
  }

  return data;
}

export default function Home() {
  const promptRef = useRef<HTMLTextAreaElement | null>(null);
  const supabase = useMemo(() => createSkyMotionSupabaseClient(), []);
  const [prompt, setPrompt] = useState(videoExamples[0].prompt);
  const [enhancedPrompt, setEnhancedPrompt] = useState(enhancePrompt({ prompt: videoExamples[0].prompt, style: "anime", duration: "10s" }));
  const [storyIdea, setStoryIdea] = useState("a courier discovers that storms are living creatures");
  const [storyScenes, setStoryScenes] = useState<StoryScene[]>(placeholderScenes);
  const [style, setStyle] = useState<AnimationStyle>("anime");
  const [duration, setDuration] = useState<AnimationDuration>("10s");
  const [source, setSource] = useState<RenderSource>("text");
  const [controls, setControls] = useState<ControlState>(defaultControls);
  const [activeCategory, setActiveCategory] = useState<TemplateCategory>("Anime");
  const [activeExample, setActiveExample] = useState<VideoExample>(videoExamples[0]);
  const [imageUpload, setImageUpload] = useState("No image selected");
  const [videoUpload, setVideoUpload] = useState("No clip selected");
  const [renderStatus, setRenderStatus] = useState("Queue idle. Load a template or generate your first movie.");
  const [demoStatus, setDemoStatus] = useState("Demo reel ready");
  const [loginEmail, setLoginEmail] = useState("");
  const [loginStatus, setLoginStatus] = useState("Enter your email to receive a creator login link.");
  const [selectedPlan, setSelectedPlan] = useState("Creator");
  const [isEnhancing, setIsEnhancing] = useState(false);
  const [isStoryboarding, setIsStoryboarding] = useState(false);
  const [isRendering, setIsRendering] = useState(false);
  const [isLoggingIn, setIsLoggingIn] = useState(false);

  const filteredTemplates = useMemo(
    () => templates.filter((template) => template.category === activeCategory),
    [activeCategory],
  );

  const controlAverage = useMemo(() => {
    const values = Object.values(controls);
    return Math.round(values.reduce((total, value) => total + value, 0) / values.length);
  }, [controls]);

  function scrollToSection(sectionId: string) {
    document.getElementById(sectionId)?.scrollIntoView({ behavior: "smooth", block: "start" });
  }

  function startFirstMovie() {
    setSource("text");
    setRenderStatus("Prompt studio opened. Refine the scene, then queue your first render.");
    scrollToSection("create");
    window.setTimeout(() => promptRef.current?.focus(), 450);
  }

  function watchDemo() {
    setActiveExample(videoExamples[0]);
    setDemoStatus("Playing generated demo reel: Neon Fox Courier.");
    scrollToSection("showcase");
  }

  function applyTemplate(template: TemplateCardData) {
    setPrompt(template.prompt);
    setStyle(template.style);
    setDuration(template.duration);
    setSource("text");
    setEnhancedPrompt(enhancePrompt({ prompt: template.prompt, style: template.style, duration: template.duration }));
    setRenderStatus(`${template.title} loaded. Adjust controls or queue a render.`);
    scrollToSection("create");
  }

  function loadShowcase(example: VideoExample) {
    setActiveExample(example);
    setPrompt(example.prompt);
    setStyle(example.style);
    setDuration(example.duration);
    setEnhancedPrompt(enhancePrompt({ prompt: example.prompt, style: example.style, duration: example.duration }));
    setDemoStatus(`Previewing generated example: ${example.title}.`);
  }

  function handleUpload(event: ChangeEvent<HTMLInputElement>, uploadSource: Extract<RenderSource, "image" | "video">) {
    const file = event.target.files?.[0];
    if (!file) {
      return;
    }

    setSource(uploadSource);
    if (uploadSource === "image") {
      setImageUpload(file.name);
    } else {
      setVideoUpload(file.name);
    }
    setRenderStatus(`${file.name} attached. Add a prompt and queue an animation render.`);
  }

  async function enhanceCurrentPrompt() {
    setIsEnhancing(true);
    setRenderStatus("Enhancing prompt through /api/prompt/enhance...");
    try {
      const data = await postJson<{ enhancedPrompt: string }>("/api/prompt/enhance", { prompt, style, duration });
      setEnhancedPrompt(data.enhancedPrompt);
      setRenderStatus("Prompt enhanced. Queue a render when the scene direction looks right.");
    } catch (error) {
      setRenderStatus(error instanceof Error ? `Prompt enhancement failed: ${error.message}` : "Prompt enhancement failed.");
    } finally {
      setIsEnhancing(false);
    }
  }

  async function buildStory() {
    setIsStoryboarding(true);
    setSource("dreammotion");
    setRenderStatus("DreamMotion is requesting scene generation through /api/story...");
    try {
      const data = await postJson<{ title: string; scenes: StoryScene[] }>("/api/story", { idea: storyIdea });
      setStoryScenes(data.scenes);
      setEnhancedPrompt(data.scenes[0]?.prompt ?? enhancedPrompt);
      setRenderStatus("Storyboard generated. Review the shots, then queue a movie render.");
    } catch (error) {
      setRenderStatus(error instanceof Error ? `Story generation failed: ${error.message}` : "Story generation failed.");
    } finally {
      setIsStoryboarding(false);
    }
  }

  async function queueRenderJob() {
    setIsRendering(true);
    setRenderStatus("Building render package through /api/render/jobs...");
    try {
      const request: RenderJobRequest = {
        prompt: enhancedPrompt,
        style,
        duration,
        source,
        controls,
      };
      const data = await postJson<{ job: RenderJob }>("/api/render/jobs", request);
      const { job } = data;
      setRenderStatus(`${job.id} ${job.status} at ${job.progress}% in ${job.eta}. Output: ${job.storagePath}`);
    } catch (error) {
      setRenderStatus(error instanceof Error ? `Render queue failed: ${error.message}` : "Render queue failed.");
    } finally {
      setIsRendering(false);
    }
  }

  function exportMovie() {
    setRenderStatus("Coming soon: final MP4 export requires the rendering backend to finish and publish a completed movie file.");
  }

  function selectPlan(planName: string) {
    setSelectedPlan(planName);
    setLoginStatus(`${planName} selected. Enter your email to continue to checkout or creator login.`);
    scrollToSection("login");
  }

  async function submitLogin(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    const email = loginEmail.trim();
    if (!email.includes("@")) {
      setLoginStatus("Enter a valid email address to continue.");
      return;
    }

    setIsLoggingIn(true);
    setLoginStatus("Preparing your secure creator login link...");
    try {
      if (supabase) {
        const { error } = await supabase.auth.signInWithOtp({
          email,
          options: { emailRedirectTo: window.location.href },
        });
        if (error) {
          throw error;
        }
        setLoginStatus(`Magic link sent to ${email}. Check your inbox to continue.`);
      } else {
        await delay(450);
        setLoginStatus(
          `Coming soon: secure email login for ${selectedPlan} requires Supabase environment variables before magic links can be sent.`,
        );
      }
    } catch (error) {
      setLoginStatus(error instanceof Error ? error.message : "Unable to start login. Please try again.");
    } finally {
      setIsLoggingIn(false);
    }
  }

  return (
    <main className="relative min-h-screen overflow-hidden px-4 py-5 text-slate-100 sm:px-6 lg:px-8">
      <div className="aurora-orb pointer-events-none absolute left-[-8rem] top-20 h-72 w-72 rounded-full bg-cyan-500/20 blur-3xl" />
      <div className="aurora-orb pointer-events-none absolute right-[-10rem] top-80 h-96 w-96 rounded-full bg-fuchsia-500/20 blur-3xl" />

      <nav className="sticky top-4 z-50 mx-auto flex max-w-7xl items-center justify-between rounded-full border border-white/10 bg-slate-950/75 px-4 py-3 shadow-2xl shadow-cyan-950/30 backdrop-blur-xl">
        <a href="#home" className="flex items-center gap-3" aria-label="SkyMotion AI home">
          <span className="grid h-11 w-11 place-items-center rounded-2xl bg-cyan-300 text-lg font-black text-slate-950 shadow-lg shadow-cyan-400/30">
            SK
          </span>
          <span>
            <span className="block text-sm font-semibold uppercase tracking-[0.32em] text-cyan-200">SkyMotion</span>
            <span className="block text-xs text-slate-400">AI Movie Studio</span>
          </span>
        </a>
        <div className="hidden items-center gap-1 lg:flex">
          {navItems.map((item) => (
            <a
              key={item.label}
              href={item.href}
              className="rounded-full px-4 py-2 text-sm font-semibold text-slate-300 transition hover:bg-white/10 hover:text-white"
            >
              {item.label}
            </a>
          ))}
        </div>
        <button
          type="button"
          onClick={startFirstMovie}
          className="cta-ripple rounded-full bg-white px-5 py-2 text-sm font-black text-slate-950 transition hover:-translate-y-0.5 hover:bg-cyan-200"
        >
          Generate Your First Movie
        </button>
      </nav>

      <section id="home" className="mx-auto grid max-w-7xl gap-8 pb-14 pt-10 lg:grid-cols-[0.92fr_1.08fr] lg:pb-16 lg:pt-14">
        <div className="flex flex-col justify-center">
          <div className="mb-5 inline-flex w-fit items-center gap-2 rounded-full border border-cyan-300/30 bg-cyan-300/10 px-4 py-2 text-xs font-semibold uppercase tracking-[0.24em] text-cyan-100">
            AI animation platform for creators and studios
          </div>
          <h1 className="text-balance text-5xl font-black tracking-tight text-white sm:text-6xl lg:text-7xl">
            Generate cinematic animated movies from a single prompt.
          </h1>
          <p className="mt-6 max-w-2xl text-lg leading-8 text-slate-300">
            SkyMotion AI turns concepts, images, and short clips into polished scenes with templates, storyboards,
            motion controls, and export-ready movie packages.
          </p>
          <div className="mt-8 flex flex-col gap-3 sm:flex-row">
            <button
              type="button"
              onClick={startFirstMovie}
              className="neon-border rounded-2xl bg-cyan-300 px-6 py-4 text-center font-black text-slate-950 transition hover:-translate-y-1 hover:shadow-cyan-300/40"
            >
              Generate Your First Movie
            </button>
            <button
              type="button"
              onClick={watchDemo}
              className="rounded-2xl border border-white/15 bg-white/5 px-6 py-4 text-center font-bold text-white transition hover:-translate-y-1 hover:bg-white/10"
            >
              Watch Demo
            </button>
          </div>
          <div className="mt-8 grid grid-cols-3 gap-3">
            <MetricCard value="4K" label="movie exports" />
            <MetricCard value="60s" label="scene length" />
            <MetricCard value="5" label="template lanes" />
          </div>
        </div>

        <div className="cinematic-frame relative overflow-hidden rounded-[2rem] p-4 sm:p-5">
          <div className="absolute inset-x-10 top-8 h-52 rounded-full bg-cyan-400/20 blur-3xl" />
          <div className="relative overflow-hidden rounded-[1.6rem] border border-white/10 bg-slate-950/70">
            <div className={`animated-preview relative h-[28rem] bg-gradient-to-br ${activeExample.gradient}`}>
              <DreamMotionStage />
              <div className="video-grain" />
              <div className="shot-sweep" />
              <div className="absolute left-5 top-5 rounded-full border border-emerald-300/30 bg-emerald-300/10 px-3 py-1 text-xs font-black uppercase tracking-[0.2em] text-emerald-100">
                Generated video showcase
              </div>
              <div className="absolute bottom-5 left-5 right-5 rounded-3xl border border-white/15 bg-slate-950/75 p-5 backdrop-blur-xl">
                <div className="flex flex-wrap items-center justify-between gap-3">
                  <div>
                    <p className="text-xs font-black uppercase tracking-[0.22em] text-cyan-200">{activeExample.category}</p>
                    <h2 className="mt-2 text-3xl font-black text-white">{activeExample.title}</h2>
                  </div>
                  <span className="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-950">{activeExample.metric}</span>
                </div>
                <div className="mt-4 grid grid-cols-3 gap-2">
                  {videoExamples.map((example) => (
                    <button
                      key={example.id}
                      type="button"
                      onClick={() => loadShowcase(example)}
                      className={`h-16 rounded-2xl border bg-gradient-to-br ${example.gradient} transition hover:-translate-y-1 ${
                        activeExample.id === example.id ? "border-cyan-200" : "border-white/10"
                      }`}
                      aria-label={`Preview ${example.title}`}
                    />
                  ))}
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-7xl py-8">
        <div className="grid gap-4 md:grid-cols-3">
          {[
            { step: "Step 1", title: "Enter Prompt", detail: "Describe the shot, product, character, mood, camera, and export format." },
            { step: "Step 2", title: "Generate Scenes", detail: "SkyMotion enhances the prompt and builds storyboard-ready animated shots." },
            { step: "Step 3", title: "Export Movie", detail: "Queue the render, package cutdowns, and prepare a shareable movie asset." },
          ].map((item) => (
            <article key={item.step} className="feature-hover glass-panel rounded-[2rem] p-6">
              <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">{item.step}</p>
              <h2 className="mt-3 text-2xl font-black text-white">{item.title}</h2>
              <p className="mt-3 leading-7 text-slate-300">{item.detail}</p>
            </article>
          ))}
        </div>
      </section>

      <section id="create" className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="Create"
          title="A real animation studio, tuned for fast conversion."
          description="Every public CTA opens a working creation flow: prompt enhancement, uploads, scene generation, render queue, export packaging, and creator login."
        />

        <div className="grid gap-5 lg:grid-cols-[1.02fr_0.98fr]">
          <div className="glass-panel rounded-[2rem] p-5 sm:p-6">
            <div className="mb-5 grid gap-3 sm:grid-cols-4">
              {sourceOptions.map((item) => (
                <button
                  key={item.value}
                  type="button"
                  onClick={() => {
                    setSource(item.value);
                    setRenderStatus(`${item.label} selected. Continue in the studio.`);
                  }}
                  className={`rounded-2xl border px-4 py-3 text-left transition hover:-translate-y-1 ${
                    source === item.value
                      ? "border-cyan-300 bg-cyan-300/15 text-cyan-100"
                      : "border-white/10 bg-white/5 text-slate-300 hover:bg-white/10"
                  }`}
                >
                  <span className="block text-sm font-black">{item.label}</span>
                  <span className="mt-1 block text-xs text-slate-400">{item.detail}</span>
                </button>
              ))}
            </div>

            <label htmlFor="prompt" className="text-sm font-bold text-cyan-100">
              Movie prompt
            </label>
            <textarea
              id="prompt"
              ref={promptRef}
              value={prompt}
              onChange={(event) => setPrompt(event.target.value)}
              rows={5}
              className="mt-2 w-full resize-none rounded-3xl border border-cyan-300/20 bg-slate-950/70 p-4 text-base text-white outline-none ring-cyan-300/40 transition placeholder:text-slate-500 focus:ring-4"
              placeholder="Describe a scene: characters, camera, mood, weather, action..."
            />

            <div className="mt-5 grid gap-4 sm:grid-cols-2">
              <SelectField
                id="style"
                label="Animation style"
                value={style}
                options={styles}
                onChange={(value) => setStyle(value as AnimationStyle)}
              />
              <SelectField
                id="duration"
                label="Duration"
                value={duration}
                options={durations}
                onChange={(value) => setDuration(value as AnimationDuration)}
              />
            </div>

            <div className="mt-5 grid gap-4 sm:grid-cols-2">
              <UploadCard
                id="image-upload"
                title="Upload image"
                detail="Animate portraits, products, scenes, and concept art."
                accept="image/*"
                fileName={imageUpload}
                onUpload={(event) => handleUpload(event, "image")}
              />
              <UploadCard
                id="video-upload"
                title="Upload clip"
                detail="Extend, stylize, or transform short video clips."
                accept="video/*"
                fileName={videoUpload}
                onUpload={(event) => handleUpload(event, "video")}
              />
            </div>

            <div className="mt-5 flex flex-col gap-3 sm:flex-row">
              <ActionButton onClick={enhanceCurrentPrompt} loading={isEnhancing} label="Enhance Prompt" loadingLabel="Enhancing..." tone="purple" />
              <ActionButton onClick={queueRenderJob} loading={isRendering} label="Queue Render" loadingLabel="Rendering..." tone="cyan" />
              <button
                type="button"
                onClick={exportMovie}
                className="rounded-2xl border border-white/15 bg-white/5 px-5 py-3 font-black text-white transition hover:-translate-y-1 hover:bg-white/10"
              >
                Export Movie (Coming Soon)
              </button>
            </div>
          </div>

          <div className="grid gap-5">
            <div className="hologram-card rounded-[2rem] p-5">
              <div className="flex items-start justify-between gap-4">
                <div>
                  <p className="text-xs font-bold uppercase tracking-[0.24em] text-fuchsia-200">AI Director</p>
                  <h3 className="mt-2 text-2xl font-black text-white">Enhanced scene direction</h3>
                </div>
                <span className="rounded-full bg-cyan-300/10 px-3 py-1 text-sm font-bold text-cyan-100">
                  {style}/{duration}
                </span>
              </div>
              <p className="relative mt-4 rounded-2xl border border-white/10 bg-slate-950/70 p-4 leading-7 text-slate-200">
                {enhancedPrompt}
              </p>
              <p className="relative mt-4 rounded-2xl border border-cyan-300/20 bg-cyan-300/10 p-3 text-sm text-cyan-100">
                Render queue: {renderStatus}
              </p>
            </div>

            <div className="glass-panel rounded-[2rem] p-5">
              <div className="mb-4 flex items-center justify-between">
                <h3 className="text-xl font-black text-white">Motion mix</h3>
                <span className="rounded-full bg-cyan-300/10 px-3 py-1 text-sm font-bold text-cyan-100">
                  {controlAverage}%
                </span>
              </div>
              <div className="grid gap-4 sm:grid-cols-2">
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
            </div>
          </div>
        </div>

        <div className="mt-5 grid gap-5 lg:grid-cols-[0.72fr_1.28fr]">
          <div className="glass-panel rounded-[2rem] p-5">
            <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">DreamMotion scenes</p>
            <h3 className="mt-2 text-2xl font-black text-white">Build the movie structure.</h3>
            <textarea
              value={storyIdea}
              onChange={(event) => setStoryIdea(event.target.value)}
              rows={4}
              className="mt-4 w-full resize-none rounded-3xl border border-cyan-300/20 bg-slate-950/70 p-4 text-white outline-none ring-cyan-300/40 focus:ring-4"
              aria-label="Story idea"
            />
            <ActionButton
              onClick={buildStory}
              loading={isStoryboarding}
              label="Generate Scenes"
              loadingLabel="Generating scenes..."
              tone="white"
              fullWidth
            />
          </div>
          <div className="grid gap-4 md:grid-cols-2">
            {storyScenes.map((scene, index) => (
              <article key={scene.id} className="hologram-card rounded-3xl p-5">
                <p className="text-xs font-bold uppercase tracking-[0.22em] text-fuchsia-200">
                  Scene {String(index + 1).padStart(2, "0")} / {scene.duration}
                </p>
                <h3 className="relative mt-2 text-xl font-black text-white">{scene.title}</h3>
                <p className="relative mt-3 line-clamp-3 text-sm leading-6 text-slate-300">{scene.prompt}</p>
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

      <section id="templates" className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="Templates"
          title="Start from proven animation formats."
          description="Choose a category, load a production-ready prompt, then customize it in the studio."
        />
        <div className="mb-5 flex gap-3 overflow-x-auto pb-2">
          {templateCategories.map((category) => (
            <button
              key={category}
              type="button"
              onClick={() => setActiveCategory(category)}
              className={`shrink-0 rounded-full border px-5 py-3 text-sm font-black transition hover:-translate-y-1 ${
                activeCategory === category
                  ? "border-cyan-200 bg-cyan-300 text-slate-950"
                  : "border-white/10 bg-white/5 text-slate-300 hover:bg-white/10"
              }`}
            >
              {category}
            </button>
          ))}
        </div>
        <div className="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
          {filteredTemplates.map((template) => (
            <article key={template.title} className="poster-depth glass-panel rounded-[2rem] p-5">
              <div className="mb-5 h-44 overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-br from-cyan-300/35 via-fuchsia-500/20 to-slate-950">
                <div className="floating-card mx-auto mt-10 h-24 w-40 rounded-[2rem] border border-white/20 bg-white/10 shadow-2xl shadow-cyan-950/40" />
              </div>
              <p className="text-xs font-black uppercase tracking-[0.22em] text-cyan-200">{template.category}</p>
              <h3 className="mt-2 text-xl font-black text-white">{template.title}</h3>
              <p className="mt-3 text-sm leading-6 text-slate-300">{template.result}</p>
              <button
                type="button"
                onClick={() => applyTemplate(template)}
                className="mt-5 w-full rounded-2xl bg-white px-5 py-3 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200"
              >
                Use This Template
              </button>
            </article>
          ))}
        </div>
      </section>

      <section id="showcase" className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="Showcase"
          title="Generated examples that feel like finished creative."
          description="Preview animation directions across anime, fantasy, realistic commercial, and music-video workflows."
        />
        <div className="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
          <div className="cinematic-frame overflow-hidden rounded-[2rem] p-4">
            <div className={`animated-preview relative h-[31rem] rounded-[1.5rem] bg-gradient-to-br ${activeExample.gradient}`}>
              <DreamMotionStage />
              <div className="video-grain" />
              <div className="absolute inset-x-6 top-6 flex items-center justify-between">
                <span className="rounded-full bg-slate-950/70 px-3 py-1 text-xs font-black uppercase tracking-[0.18em] text-cyan-100">
                  {demoStatus}
                </span>
                <span className="rounded-full bg-white px-3 py-1 text-xs font-black text-slate-950">{activeExample.duration}</span>
              </div>
              <div className="absolute bottom-6 left-6 right-6 rounded-3xl border border-white/15 bg-slate-950/75 p-5 backdrop-blur-xl">
                <p className="text-xs font-black uppercase tracking-[0.22em] text-fuchsia-200">{activeExample.category}</p>
                <h3 className="mt-2 text-3xl font-black text-white">{activeExample.title}</h3>
                <p className="mt-3 text-sm leading-6 text-slate-300">{activeExample.prompt}</p>
                <button
                  type="button"
                  onClick={() => applyTemplate({
                    title: activeExample.title,
                    category: activeExample.category,
                    prompt: activeExample.prompt,
                    style: activeExample.style,
                    duration: activeExample.duration,
                    result: "Loaded from generated showcase.",
                  })}
                  className="mt-4 rounded-2xl bg-cyan-300 px-5 py-3 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200"
                >
                  Open in Studio
                </button>
              </div>
            </div>
          </div>
          <div className="grid gap-4">
            {videoExamples.map((example) => (
              <button
                key={example.id}
                type="button"
                onClick={() => loadShowcase(example)}
                className={`group rounded-[2rem] border p-4 text-left transition hover:-translate-y-1 hover:bg-white/10 ${
                  activeExample.id === example.id ? "border-cyan-300 bg-cyan-300/10" : "border-white/10 bg-white/5"
                }`}
              >
                <div className="flex gap-4">
                  <div className={`h-24 w-32 shrink-0 rounded-2xl bg-gradient-to-br ${example.gradient}`} />
                  <div>
                    <p className="text-xs font-black uppercase tracking-[0.2em] text-cyan-200">{example.category}</p>
                    <h3 className="mt-2 text-xl font-black text-white group-hover:text-cyan-100">{example.title}</h3>
                    <p className="mt-2 text-sm text-slate-400">{example.metric} / {example.duration}</p>
                  </div>
                </div>
              </button>
            ))}
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="Creator trust"
          title="Built for signups, proof, and repeat creation."
          description="The homepage now supports social proof and clear creator outcomes without exposing internal implementation details."
        />
        <div className="grid gap-5 lg:grid-cols-3">
          {testimonials.map((testimonial) => (
            <article key={testimonial.name} className="glass-panel rounded-[2rem] p-6">
              <p className="text-5xl font-black text-cyan-300">&quot;</p>
              <p className="mt-2 leading-7 text-slate-200">{testimonial.quote}</p>
              <div className="mt-6 border-t border-white/10 pt-4">
                <h3 className="font-black text-white">{testimonial.name}</h3>
                <p className="mt-1 text-sm text-slate-400">{testimonial.role}</p>
              </div>
            </article>
          ))}
        </div>
        <div className="mt-5 grid gap-5 md:grid-cols-3">
          {creatorStories.map((story) => (
            <article key={story.creator} className="hologram-card rounded-[2rem] p-6">
              <p className="text-4xl font-black text-white">{story.value}</p>
              <p className="relative mt-2 leading-7 text-slate-300">{story.label}</p>
              <p className="relative mt-4 text-sm font-black uppercase tracking-[0.22em] text-cyan-200">@{story.creator}</p>
            </article>
          ))}
        </div>
      </section>

      <section id="pricing" className="mx-auto max-w-7xl py-10">
        <SectionHeading
          eyebrow="Pricing"
          title="Clear plans with a direct path to creation."
          description="Pick a plan, generate a first movie, or sign in to save projects and exports."
        />
        <div className="grid gap-5 lg:grid-cols-[1.1fr_0.9fr]">
          <div className="grid gap-5 md:grid-cols-3">
            {pricingPlans.map((plan) => (
              <article
                key={plan.name}
                className={`rounded-[2rem] p-6 ${
                  plan.highlighted ? "neon-border bg-cyan-300 text-slate-950" : "glass-panel text-white"
                }`}
              >
                <p className={`text-sm font-black uppercase tracking-[0.24em] ${plan.highlighted ? "text-slate-800" : "text-cyan-200"}`}>
                  {plan.name}
                </p>
                <div className="mt-3 flex items-end gap-2">
                  <span className="text-5xl font-black">{plan.price}</span>
                  <span className={`pb-2 text-sm ${plan.highlighted ? "text-slate-800" : "text-slate-400"}`}>/month</span>
                </div>
                <p className={`mt-4 text-sm leading-6 ${plan.highlighted ? "text-slate-800" : "text-slate-300"}`}>{plan.detail}</p>
                <ul className="mt-5 space-y-3">
                  {plan.features.map((feature) => (
                    <li key={feature} className="flex gap-3 text-sm font-semibold">
                      <span className={`mt-1 h-2 w-2 rounded-full ${plan.highlighted ? "bg-slate-950" : "bg-cyan-300"}`} />
                      <span>{feature}</span>
                    </li>
                  ))}
                </ul>
                <button
                  type="button"
                  onClick={() => selectPlan(plan.name)}
                  className={`mt-6 w-full rounded-2xl px-5 py-3 font-black transition hover:-translate-y-1 ${
                    plan.highlighted ? "bg-slate-950 text-white hover:bg-slate-800" : "bg-white text-slate-950 hover:bg-cyan-200"
                  }`}
                >
                  Choose {plan.name}
                </button>
              </article>
            ))}
          </div>
          <form id="login" onSubmit={submitLogin} className="glass-panel rounded-[2rem] p-6">
            <p className="text-xs font-black uppercase tracking-[0.24em] text-cyan-200">Login</p>
            <h3 className="mt-3 text-3xl font-black text-white">Save projects and exports.</h3>
            <p className="mt-3 leading-7 text-slate-300">
              Continue with email to access the creator studio, project history, templates, and render exports.
            </p>
            <label htmlFor="email" className="mt-6 block text-sm font-bold text-cyan-100">
              Email address
            </label>
            <input
              id="email"
              type="email"
              value={loginEmail}
              onChange={(event) => setLoginEmail(event.target.value)}
              placeholder="creator@studio.com"
              className="mt-2 w-full rounded-2xl border border-cyan-300/20 bg-slate-950/70 p-4 text-white outline-none ring-cyan-300/40 transition placeholder:text-slate-500 focus:ring-4"
            />
            <button
              type="submit"
              className="mt-4 flex w-full items-center justify-center gap-2 rounded-2xl bg-cyan-300 px-5 py-3 font-black text-slate-950 transition hover:-translate-y-1 hover:bg-cyan-200 disabled:cursor-wait disabled:opacity-70"
              disabled={isLoggingIn}
            >
              {isLoggingIn && <span className="loading-spinner" aria-hidden="true" />}
              {isLoggingIn ? "Sending Link..." : "Login"}
            </button>
            <p className="mt-4 rounded-2xl border border-white/10 bg-white/5 p-3 text-sm text-slate-300">{loginStatus}</p>
          </form>
        </div>
      </section>
    </main>
  );
}

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
    <div className="glass-panel rounded-3xl p-4 transition hover:-translate-y-1 hover:border-cyan-300/40">
      <div className="text-2xl font-black text-white">{value}</div>
      <div className="mt-1 text-xs uppercase tracking-[0.16em] text-slate-400">{label}</div>
    </div>
  );
}

function SelectField({
  id,
  label,
  value,
  options,
  onChange,
}: {
  id: string;
  label: string;
  value: string;
  options: string[];
  onChange: (value: string) => void;
}) {
  return (
    <div>
      <label htmlFor={id} className="text-sm font-bold text-cyan-100">
        {label}
      </label>
      <select
        id={id}
        value={value}
        onChange={(event) => onChange(event.target.value)}
        className="mt-2 w-full rounded-2xl border border-white/10 bg-slate-950 p-3 text-white outline-none transition hover:border-cyan-300/30 focus:border-cyan-300"
      >
        {options.map((item) => (
          <option key={item} value={item}>
            {item === "3d" ? "3D" : item.charAt(0).toUpperCase() + item.slice(1)}
          </option>
        ))}
      </select>
    </div>
  );
}

function UploadCard({
  id,
  title,
  detail,
  accept,
  fileName,
  onUpload,
}: {
  id: string;
  title: string;
  detail: string;
  accept: string;
  fileName: string;
  onUpload: (event: ChangeEvent<HTMLInputElement>) => void;
}) {
  return (
    <label
      htmlFor={id}
      className="block cursor-pointer rounded-3xl border border-dashed border-cyan-300/30 bg-cyan-300/5 p-4 transition hover:-translate-y-1 hover:bg-cyan-300/10"
    >
      <span className="block text-sm font-black text-white">{title}</span>
      <span className="mt-1 block text-xs leading-5 text-slate-400">{detail}</span>
      <input id={id} className="sr-only" type="file" accept={accept} onChange={onUpload} />
      <span className="mt-4 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-cyan-100">
        {fileName}
      </span>
    </label>
  );
}

function ActionButton({
  onClick,
  loading,
  label,
  loadingLabel,
  tone,
  fullWidth = false,
}: {
  onClick: () => void;
  loading: boolean;
  label: string;
  loadingLabel: string;
  tone: "cyan" | "purple" | "white";
  fullWidth?: boolean;
}) {
  const toneClass =
    tone === "purple"
      ? "purple-border bg-fuchsia-400 text-slate-950 hover:bg-fuchsia-300"
      : tone === "white"
        ? "bg-white text-slate-950 hover:bg-cyan-200"
        : "bg-cyan-300 text-slate-950 hover:bg-cyan-200";

  return (
    <button
      type="button"
      onClick={onClick}
      disabled={loading}
      className={`mt-0 flex items-center justify-center gap-2 rounded-2xl px-5 py-3 font-black transition hover:-translate-y-1 disabled:cursor-wait disabled:opacity-70 ${
        fullWidth ? "mt-4 w-full" : ""
      } ${toneClass}`}
    >
      {loading && <span className="loading-spinner" aria-hidden="true" />}
      {loading ? loadingLabel : label}
    </button>
  );
}
